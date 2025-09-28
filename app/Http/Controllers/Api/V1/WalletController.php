<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class WalletController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'secret_phrase' => 'required|string',
        ]);

        $userId = $request->user()->id;

        // 1. Check if user already has a wallet
        $hasWallet = Wallet::where('user_id', $userId)->first();
        if ($hasWallet) {
            return response()->json([
                'status'  => false,
                'message' => 'You already have a wallet',
            ], 422);
        }

        // 2. Look for an unclaimed wallet with matching secret phrase
        $unclaimedWallets = Wallet::whereNull('user_id')->get();
        $matchedWallet = null;

        foreach ($unclaimedWallets as $wallet) {
            try {
                $decryptedPhrase = Crypt::decryptString($wallet->secret_phrase);
                if ($decryptedPhrase === $request->secret_phrase) {
                    $matchedWallet = $wallet;
                    break;
                }
            } catch (\Exception $e) {
                // skip wallets with invalid encrypted data
                continue;
            }
        }

        if (! $matchedWallet) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or already claimed secret phrase',
            ], 404);
        }

        // 3. Assign wallet to the user
        $matchedWallet->user_id = $userId;
        $matchedWallet->save();

        return response()->json([
            'status'  => true,
            'message' => 'Wallet successfully assigned',
            'wallet'  => $matchedWallet,
        ]);
    }

    public function show(Request $request)
    {
        $wallet = Wallet::where('user_id', $request->user()->id)->first();
        if (!$wallet) {
            return response()->json([
                'status'  => false,
                'message' => 'No wallet found',
                'data'  => []
            ]);
        }
        return response()->json([
            'status'  => true,
            'message' => 'Wallet successfully retrieved',
            'data'  => [
                'btc_address' => $wallet->btc_address,
                'eth_address' => $wallet->eth_address,
                'solana_address' => $wallet->solana_address,
                'xrp_address' => $wallet->xrp_address,
            ],
        ]);
    }

    public function getPhrase(Request $request)
    {
        $wallet = Wallet::whereNull('user_id')->first();

        if (! $wallet) {
            return response()->json([
                'status'  => false,
                'message' => 'No available wallets',
            ], 404);
        }

        try {
            $phrase = Crypt::decryptString($wallet->secret_phrase);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Unable to retrieve phrase',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Secret phrase retrieved',
            'data' => [
                'secret_phrase' => $phrase,
                'wallet_id'     => $wallet->id,
            ]

        ]);
    }
}
