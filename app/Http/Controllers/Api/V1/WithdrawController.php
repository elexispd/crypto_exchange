<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Helpers\WalletHelper;
use App\Mail\WithdrawMail;
use App\Models\User;
use App\Services\CoinGeckoService;
use App\Services\FeeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class WithdrawController extends Controller
{
    protected $feeService;

    public function __construct(FeeService $feeService)
    {
        $this->feeService = $feeService;
    }

    /**
     * Handle user withdrawal request
     */
    public function store(Request $request)
    {
        $request->validate([
            'currency' => 'required|string',
            'amount' => 'required|numeric|min:0.00000001',
            'address' => 'required|string',
            'narrative' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (! $wallet) {
            return response()->json([
                'status' => false,
                'message' => 'Wallet not found.'
            ], 404);
        }

        $currency = strtolower($request->currency);

        // Get smart transaction fee - both type and network are compulsory
        $transactionFee = $this->feeService->getFee('Withdrawal', $currency, $request->amount);


        // Get wallet balance
        try {
            $balance = $wallet->getBalance($currency);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unsupported currency.'
            ], 422);
        }

        $total = $request->amount + $transactionFee;

        if ($balance < $total) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient balance.'
            ], 422);
        }

        // Get user's own address (where funds are being sent from)
        $fromAddress = WalletHelper::getAddress($wallet, $currency);

        if (! $fromAddress) {
            return response()->json([
                'status' => false,
                'message' => 'Your wallet address for this currency is not set.'
            ], 422);
        }

        // Process withdrawal in transaction for safety
        DB::beginTransaction();
        try {
            // Deduct immediately (or you can move this to approval)
            $wallet->decrementBalance($currency, $total);

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => 'withdraw',
                'currency' => $currency,
                'amount' => $request->amount,
                'fee' => $transactionFee,
                'to_address' => $request->address,
                'narrative' => $request->narrative,
                'status' => 'pending',
            ]);

            DB::commit();

            Mail::to($user->email)->send(new WithdrawMail($user, $transaction));

            return response()->json([
                'status' => true,
                'message' => 'Withdrawal request submitted successfully.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'currency' => $currency,
                    'amount' => $request->amount,
                    'fee' => $transactionFee,
                    'total_deducted' => $total,
                    'from_address' => $fromAddress,
                    'to_address' => $request->address,
                    'narrative' => $request->narrative,
                    'status' => 'pending'
                ]
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Withdrawal failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function withdrawInfo(Request $request, User $user, CoinGeckoService $coinGecko)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'network' => ['required', 'string'],
        ]);

        // Get smart transaction fee - both type and network are compulsory
        $serviceFee = $this->feeService->getFee('Withdrawal', $validated['network'], $validated['amount']);


        // Extract validated input
        $amount = (float) $validated['amount'];
        $network = strtolower($validated['network']);

        // Fetch price safely
        $symbolId = $coinGecko->mapSymbolToId($network);
        $priceData = $coinGecko->getPrice($network, 'usd');
        $price = $priceData[$symbolId]['usd'] ?? null;

        if (! $price || $price <= 0) {
            return response()->json([
                'status' => false,
                'message' => "Unable to retrieve USD price for {$network}.",
            ], 422);
        }

        // Compute derived values
        $amountInUsd = $amount * $price;
        $serviceFeeInNetwork = $serviceFee / $price;
        $totalInUsd = $amountInUsd + $serviceFee;

        // Return structured response
        return response()->json([
            'status' => true,
            'message' => 'Withdrawal info retrieved successfully.',
            'data' => [
                'network' => strtoupper($network),
                'amount' => number_format($amount, 8, '.', ''),
                'service_fee_usd' => number_format($serviceFee, 2),
                'base_price_usd' => number_format($price, 2),
                'amount_in_usd' => number_format($amountInUsd, 2),
                'service_fee_in_network' => number_format($serviceFeeInNetwork, 8, '.', ''),
                'total_in_usd' => number_format($totalInUsd, 2),
                'fee_details' => $this->feeService->getFeeDetails('Withdrawal', $validated['network'], $amount),
            ],
        ]);
    }
}
