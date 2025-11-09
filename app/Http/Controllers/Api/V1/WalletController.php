<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use App\Helpers\WalletHelper;

class WalletController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'secret_phrase' => 'required|string',
        ]);

        $userId = $request->user()->id;

        // Check if user already has a wallet
        if (Wallet::where('user_id', $userId)->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'You already have a wallet',
            ], 422);
        }

        // Search without encrypting first - compare encrypted values in query
        $wallet = Wallet::whereNull('user_id')
            ->get()
            ->filter(function ($wallet) use ($request) {
                try {
                    $decryptedPhrase = Crypt::decryptString($wallet->secret_phrase);
                    return $decryptedPhrase === $request->secret_phrase;
                } catch (\Exception $e) {
                    return false;
                }
            })
            ->first();

        if (!$wallet) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or already claimed secret phrase',
            ], 404);
        }

        // Generate unique wallet_id
        do {
            $walletId = 'WAL-' . strtoupper(Str::random(10));
        } while (Wallet::where('wallet_id', $walletId)->exists());

        // Assign wallet to user
        $wallet->user_id = $userId;
        $wallet->wallet_id = $walletId;
        $wallet->save();

        return response()->json([
            'status'  => true,
            'message' => 'Wallet successfully assigned',
            'wallet'  => [
                'id'              => $wallet->id,
                'user_id'         => $wallet->user_id,
                'wallet_id'       => $wallet->wallet_id,
                'btc_address'     => $wallet->btc_address,
                'eth_address'     => $wallet->eth_address,
                'xrp_address'     => $wallet->xrp_address,
                'solana_address'  => $wallet->solana_address,
            ],
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

    public function getBalances(Request $request)
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
                'btc'     => number_format((float) $wallet->btc_balance, 2, '.', ''),
                'eth'     => number_format((float) $wallet->eth_balance, 2, '.', ''),
                'xrp'     => number_format((float) $wallet->xrp_balance, 2, '.', ''),
                'solana'  => number_format((float) $wallet->solana_balance, 2, '.', '')
            ],
        ]);
    }

    public function getWalletAddressBySymbol(Wallet $wallet, Request $request){

        $network = $request->network;
        $address = WalletHelper::getAddress($wallet, $network);
        if (!$address) {
            return response()->json([
                'status'  => false,
                'message' => 'No wallet address found',
                'data'  => []
            ]);
        }
        return response()->json([
                'status'  => true,
                'message' => 'Wallet successfully retrieved',
                'data'  => [
                    'wallet_address' => $address
                ]
            ]);
    }
}
