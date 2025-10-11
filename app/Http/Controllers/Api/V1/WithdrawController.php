<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Helpers\WalletHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
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

        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (! $wallet) {
            return response()->json([
                'status' => false,
                'message' => 'Wallet not found.'
            ], 404);
        }

        $currency = strtolower($request->currency);
        $transactionFee = 0.0005; // Hardcoded for now (e.g. BTC withdrawal fee)

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
                'status' => 'pending', // to be approved by admin
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Withdrawal request submitted successfully.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'currency' => $currency,
                    'amount' => $request->amount,
                    'fee' => $transactionFee,
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
}
