<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;

class TransactionController extends Controller
{

    public function index(Request $request)
    {
        $query = Transaction::where('user_id', $request->user()->id);

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $transactions = $query->latest()->paginate(20);

        return response()->json([
            'status' => true,
            'transactions' => $transactions,
        ]);

    }

    public function show(Request $request, Transaction $transaction) {
        return response()->json([
            'status' => true,
            'transaction' => $transaction,
        ]);
    }

    // Create a deposit transaction
    public function deposit(Request $request)
    {
        $request->merge(['currency' => strtoupper($request->currency)]);

        $request->validate([
            'wallet_id' => 'required|uuid|exists:wallets,id',
            'currency'  => 'required|string|in:BTC,ETH,XRP,SOL',
            'amount'    => 'required|numeric|min:0.00000001'
        ]);

        $wallet = Wallet::where('id', $request->wallet_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $wallet) {
            return response()->json(['status' => false, 'message' => 'Invalid wallet'], 403);
        }

        $transaction = Transaction::create([
            'user_id'   => $request->user()->id,
            'wallet_id' => $wallet->id,
            'type'      => 'deposit',
            'currency'  => strtoupper($request->currency),
            'amount'    => $request->amount,
            'status'    => 'pending',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Deposit transaction created',
            'transaction' => $transaction,
        ]);
    }

    // Create a withdraw transaction
    public function withdraw(Request $request)
    {
        $request->merge(['currency' => strtolower($request->currency)]);

        $request->validate([
            'wallet_id' => 'required|uuid|exists:wallets,id',
            'currency'  => 'required|string|in:btc,eth,xrp,sol',
            'amount'    => 'required|numeric|min:0.00000001',
            'to_address' => 'required|string',
        ]);

        $wallet = Wallet::where('id', $request->wallet_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $wallet) {
            return response()->json(['status' => false, 'message' => 'Invalid wallet'], 403);
        }

        $transaction = Transaction::create([
            'user_id'   => $request->user()->id,
            'wallet_id' => $wallet->id,
            'type'      => 'withdraw',
            'currency'  => strtoupper($request->currency),
            'amount'    => $request->amount,
            'status'    => 'pending',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Withdrawal transaction created',
            'transaction' => $transaction,
        ]);
    }

    // Create a swap transaction
    public function swap(Request $request)
    {
        $request->merge([
            'from_currency' => strtolower($request->from_currency),
            'to_currency'   => strtolower($request->to_currency),
        ]);

        $request->validate([
            'wallet_id'     => 'required|uuid|exists:wallets,id',
            'from_currency' => 'required|string|in:btc,eth,xrp,sol',
            'to_currency'   => 'required|string|in:btc,eth,xrp,sol|different:from_currency',
            'from_amount'   => 'required|numeric|min:0.00000001',
            'to_amount'     => 'required|numeric|min:0.00000001',
        ]);

        $wallet = Wallet::where('id', $request->wallet_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $wallet) {
            return response()->json(['status' => false, 'message' => 'Invalid wallet'], 403);
        }

        $transaction = Transaction::create([
            'user_id'       => $request->user()->id,
            'wallet_id'     => $wallet->id,
            'type'          => 'swap',
            'from_currency' => strtoupper($request->from_currency),
            'to_currency'   => strtoupper($request->to_currency),
            'from_amount'   => $request->from_amount,
            'to_amount'     => $request->to_amount,
            'status'        => 'pending',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Swap transaction created',
            'transaction' => $transaction,
        ]);
    }



}
