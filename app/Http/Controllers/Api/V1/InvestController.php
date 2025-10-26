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
use Illuminate\Support\Facades\Mail;

class InvestController extends Controller
{
    use Assets;
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|uuid|exists:investment_plans,id',
            'network' => 'required|string',
            'amount' => 'required|string',
        ]);

        // Get all allowed assets
        $assets = $this->getAllAssets();

        $allowedSymbols = array_map(
            fn($asset) => strtolower($asset['symbol']),
            $assets
        );

        $inputSymbol = strtolower($validated['network']);


        if (!in_array($inputSymbol, $allowedSymbols)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid network symbol provided.',
                'allowed' => array_map('strtoupper', $allowedSymbols), // optional for clarity
            ], 422);
        }

        $user = $request->user();
        $wallet = $user->wallet;

        $walletBalance = WalletHelper::getWalletBalance($wallet, $inputSymbol);

        if ($walletBalance < $validated['amount']) {
            return response()->json([
                'status'  => false,
                'message' => 'Insufficient balance.',
            ], 422);
        }


        $investment = Invest::create([
            'user_id' => $user->id,
            'investment_plan_id' => $validated['plan_id'],
            'network' => $validated['network'],
            'amount' => $validated['amount']
        ]);

        $transactionFee = TransactionFee::first()->amount;
        $total = $validated["amount"] + $transactionFee;
        $wallet->decrementBalance($validated["network"], $total);

        Mail::to($user->email)->send(new InvestmentMail($user, $investment, 'initial'));

        return response()->json([
            'status'  => true,
            'message' => 'Investment successfully created',
            'data'  => $investment
        ]);
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


    public function getInvestmentPlans()
    {
        $plans = InvestmentPlan::where('status', true)->get(['id', 'name', 'min_amount', 'interest_rate']);
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


}
