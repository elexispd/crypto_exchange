<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Wallet;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function store(Request $request)
    {
        $request->merge([
            'currency' => strtoupper($request->currency),
        ]);
        $request->validate([
            'wallet_id' => 'required|uuid|exists:wallets,id',
            'currency'     => 'required|string|in:BTC,ETH,XRP,SOL',
            'amount'    => 'required|numeric|min:0.00000001'
        ]);

        $userId = $request->user()->id;

        $wallet = Wallet::where('id', $request->wallet_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $wallet) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid wallet',
            ], 403);
        }

        // Pick correct address based on currency
        $addressMap = [
            'BTC' => $wallet->btc_address,
            'ETH' => $wallet->eth_address,
            'XRP' => $wallet->xrp_address,
            'SOL' => $wallet->solana_address,
        ];

        $depositAddress = $addressMap[$request->currency] ?? null;

        if (! $depositAddress) {
            return response()->json([
                'status'  => false,
                'message' => 'Unsupported currency',
            ], 422);
        }

        $deposit = Deposit::create([
            'user_id'   => $request->user()->id,
            'wallet_id' => $wallet->id,
            'currency'  => $request->currency,
            'amount'    => $request->amount,
            'status'    => 'pending',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Deposit created successfully',
            'deposit' => [
                'id'       => $deposit->id,
                'currency' => $deposit->currency,
                'amount'   => $deposit->amount,
                'status'   => $deposit->status,
                'address'  => $depositAddress, // send correct wallet address here
            ]
        ]);
    }

    // List user deposits
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $deposits = Deposit::where('user_id', $userId)
            ->latest()
            ->get(['id', 'user_id', 'wallet_id', 'currency', 'amount', 'status', 'created_at']);

        return response()->json([
            'status'   => true,
            'message'  => 'Deposits retrieved successfully',
            'deposits' => $deposits,
        ]);
    }

    // Show a single deposit
    public function show($id, Request $request)
    {
        $deposit = Deposit::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $deposit) {
            return response()->json([
                'status'  => false,
                'message' => 'Deposit not found',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'deposit' => $deposit->only([
                'id',
                'user_id',
                'wallet_id',
                'currency',
                'amount',
                'status',
                'created_at'
            ]),
        ]);
    }
}
