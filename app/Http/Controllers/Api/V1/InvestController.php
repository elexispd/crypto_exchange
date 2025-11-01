<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\WalletHelper;
use App\Http\Controllers\Controller;
use App\Mail\InvestmentMail;
use App\Models\Invest;
use App\Models\InvestmentPlan;
use App\Models\TransactionFee;
use Illuminate\Http\Request;
use App\Traits\Assets;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InvestController extends Controller
{
    use Assets;
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'plan_id' => 'required|uuid|exists:investment_plans,id',
            'network' => 'required|string',
            'amount' => 'required|numeric|min:0.00000001',
        ]);

        // Get all allowed assets from the Assets trait
        $assets = $this->getAllAssets();

        // Extract allowed symbols (btc, eth, xrp, etc.) and convert to lowercase
        $allowedSymbols = array_map(
            fn($asset) => strtolower($asset['symbol']),
            $assets
        );

        // Convert user input to lowercase for comparison
        $inputSymbol = strtolower($validated['network']);

        // Check if the provided network is valid
        if (!in_array($inputSymbol, $allowedSymbols)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid network symbol provided.',
                'allowed_networks' => array_map('strtoupper', $allowedSymbols),
            ], 422);
        }

        // Get the authenticated user and their wallet
        $user = $request->user();
        $wallet = $user->wallet;

        // Find the investment plan
        $plan = InvestmentPlan::where('id', $validated['plan_id'])->first();

        // Check if investment amount meets minimum requirement
        if ($validated["amount"] < $plan->min_amount) {
            return response()->json([
                'status'  => false,
                'message' => "Minimum investment amount is " . $plan->min_amount,
            ], 422);
        }

        // Get user's balance for the selected network
        $walletBalance = WalletHelper::getWalletBalance($wallet, $inputSymbol);

        // Get transaction fee
        $transactionFee = TransactionFee::first()->amount;
        $totalDeduction = $validated["amount"] + $transactionFee;

        // Check if user has sufficient balance (amount + fee)
        if ($walletBalance < $totalDeduction) {
            return response()->json([
                'status'  => false,
                'message' => 'Insufficient balance. You need ' . $totalDeduction . ' but only have ' . $walletBalance,
            ], 422);
        }

        // Start database transaction to ensure data consistency
        DB::beginTransaction();
        try {
            // Create the investment record
            $investment = Invest::create([
                'user_id' => $user->id,
                'investment_plan_id' => $validated['plan_id'],
                'network' => $validated['network'],
                'amount' => $validated['amount'],
                'status' => 'active',
                'invested_at' => now(),
                'lock_period' => 30 // 30 days lock period
            ]);

            // Deduct the investment amount + transaction fee from user's wallet
            $wallet->decrementBalance($validated["network"], $totalDeduction);

            // Send investment confirmation email
            Mail::to($user->email)->send(new InvestmentMail($user, $investment, 'initial'));

            // Commit the transaction if everything is successful
            DB::commit();

            // Return success response
            return response()->json([
                'status'  => true,
                'message' => 'Investment successfully created',
                'data'  => [
                    'investment' => $investment,
                    'amount_invested' => $validated['amount'],
                    'transaction_fee' => $transactionFee,
                    'total_deducted' => $totalDeduction,
                    'lock_period' => '30 days',
                    'invested_at' => $investment->invested_at
                ]
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollBack();

            // Return error response
            return response()->json([
                'status'  => false,
                'message' => 'Failed to create investment: ' . $e->getMessage(),
            ], 500);
        }
    }

    // get user's investments.
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $investments = Invest::where('user_id', $userId)
            ->with('investmentPlan') // singular name matches relationship
            ->latest()
            ->get(['id', 'user_id', 'investment_plan_id', 'amount', 'network', 'status']);

        return response()->json([
            'status'  => true,
            'message' => 'Investments retrieved successfully',
            'data'    => $investments,
        ]);
    }

    // Show a single investment
    public function show(Request $request, $invest)
    {
        $userId = $request->user()->id;

        $investment = Invest::where('id', $invest)
            ->where('user_id', $userId)
            ->with('investmentPlan')
            ->first();

        if (!$investment) {
            return response()->json([
                'status'  => false,
                'message' => 'Investment not found',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Investment retrieved successfully',
            'data'    => $investment,
        ]);
    }


    public function getInvestmentPlans($network)
    {
        $plans = InvestmentPlan::where('status', true)
            ->where('network', $network) // assuming you have a 'network' column
            ->get(['id', 'name', 'min_amount', 'interest_rate']);

        return response()->json([
            'status'  => true,
            'message' => 'Investment plans retrieved successfully',
            'data'    => $plans,
        ]);
    }

    public function getInvestmentPlan(InvestmentPlan $plan)
    {

        return response()->json([
            'status'  => true,
            'message' => 'Investment plan retrieved successfully',
            'data'    => $plan->only(['id', 'name', 'min_amount', 'interest_rate']),
        ]);
    }

    /**
     * Redeem an investment
     */
    public function redeem(Request $request, $investId)
    {
        $user = $request->user();

        $investment = Invest::where('id', $investId)
            ->where('user_id', $user->id)
            ->with(['profits', 'investmentPlan'])
            ->first();

        if (!$investment) {
            return response()->json([
                'status'  => false,
                'message' => 'Investment not found',
            ], 404);
        }

        if ($investment->redeemed_at) {
            return response()->json([
                'status'  => false,
                'message' => 'Investment already redeemed',
            ], 422);
        }

        if (!$investment->canBeRedeemed()) {
            $lockEndDate = $investment->invested_at->addDays($investment->lock_period ?? 30);
            return response()->json([
                'status'  => false,
                'message' => "Investment cannot be redeemed before " . $lockEndDate->format('Y-m-d H:i:s'),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $wallet = $user->wallet;
            $network = strtolower($investment->network);

            // Calculate total to refund (capital + all profits)
            $totalProfit = $investment->totalProfit();
            $totalAmount = $investment->amount + $totalProfit;

            // Credit profits to wallet
            $wallet->incrementBalance($network, $totalAmount);

            // Mark investment as redeemed
            $investment->update([
                'redeemed_at' => now(),
                'status' => 'completed'
            ]);

            // Mark all profits as credited
            $investment->profits()->update(['credited' => true]);

            // Send notification email
            Mail::to($user->email)->send(new InvestmentMail($user, $investment, 'redemption', $totalAmount));

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Investment redeemed successfully',
                'data'    => [
                    'capital_returned' => $investment->amount,
                    'total_profit' => $totalProfit,
                    'total_amount' => $totalAmount,
                    'redeemed_at' => $investment->redeemed_at
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Failed to redeem investment: ' . $e->getMessage(),
            ], 500);
        }
    }
}
