<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\adminWallet;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\CoinGeckoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\DepositMail;
use App\Mail\SwapMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

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

        // Use per_page from request, default to 20 if not provided
        $perPage = (int) $request->input('per_page', 20);

        // Add a sensible limit to prevent abuse
        $perPage = min(max($perPage, 1), 100);

        $transactions = $query->latest()->paginate($perPage);

        return response()->json([
            'status' => true,
            'per_page' => $perPage,
            'transactions' => $transactions,
        ]);
    }


    public function show(Request $request, Transaction $transaction)
    {
        return response()->json([
            'status' => true,
            'data' => $transaction,
        ]);
    }

    // Create a deposit transaction
    public function deposit(Request $request)
    {
        $request->merge(['currency' => strtoupper($request->network)]);
        $user = $request->user();
        $validated =$request->validate([
            'wallet_id' => 'required|uuid|exists:wallets,id',
            'network'  => 'required|string|in:BTC,ETH,XRP,SOL,btc,eth,xrp,sol',
            'amount'    => 'required|numeric|min:0.00000001'
        ]);

        $wallet = Wallet::where('id', $validated['wallet_id'])
            ->where('user_id', $user->id)
            ->first();

        $network = strtoupper($validated['network']);

        if (! $wallet) {
            return response()->json(['status' => false, 'message' => 'Invalid wallet'], 403);
        }

        $admnWallet = adminWallet::query()
                    ->where('network', $network)
                    ->where('status', 'active')
                    ->first();
        if (! $admnWallet) {
            return response()->json(['status' => false, 'message' => 'Payment wallet not found'], 403);
        }

        $transaction = Transaction::create([
            'user_id'   => $user->id,
            'wallet_id' => $wallet->id,
            'type'      => 'deposit',
            'currency'  => $network,
            'amount'    => $validated['amount'],
            'status'    => 'pending',
        ]);

        Mail::to($user->email)->send(new DepositMail($user, $transaction));

        return response()->json([
            'status' => true,
            'message' => 'Deposit transaction created',
            'transaction' => $transaction,
            'payment_wallet_address' => $admnWallet->address,
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
    public function swap(Request $request, CoinGeckoService $coinGecko)
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
        ]);

        $wallet = Wallet::where('id', $request->wallet_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $wallet) {
            return response()->json(['status' => false, 'message' => 'Invalid wallet'], 403);
        }

        // Check wallet balance
        $fromBalance = $wallet->getBalance($request->from_currency);
        if ($fromBalance < $request->from_amount) {
            return response()->json(['status' => false, 'message' => 'Insufficient balance'], 400);
        }

        // ✅ Map to CoinGecko IDs
        $fromId = $coinGecko->mapSymbolToId($request->from_currency);
        $toId   = $coinGecko->mapSymbolToId($request->to_currency);

        if (! $fromId || ! $toId) {
            return response()->json(['status' => false, 'message' => 'Unsupported currency'], 400);
        }

        // ✅ Fetch prices with mapped IDs
        $prices = $coinGecko->getPrices([$fromId, $toId], ['usd'], 60);

        if (!isset($prices[$fromId]['usd']) || !isset($prices[$toId]['usd'])) {
            return response()->json(['status' => false, 'message' => 'Price fetch failed'], 500);
        }

        $fromPriceUsd = $prices[$fromId]['usd'];
        $toPriceUsd   = $prices[$toId]['usd'];

        // Calculate conversion
        $toAmount = ($request->from_amount * $fromPriceUsd) / $toPriceUsd;

        // ✅ Update balances (wrap in DB transaction for safety)
        DB::transaction(function () use ($wallet, $request, $toAmount) {
            $wallet->decrementBalance($request->from_currency, $request->from_amount);
            $wallet->incrementBalance($request->to_currency, $toAmount);
        });

        $transaction = Transaction::create([
            'user_id'       => $request->user()->id,
            'wallet_id'     => $wallet->id,
            'type'          => 'swap',
            'from_currency' => strtoupper($request->from_currency),
            'to_currency'   => strtoupper($request->to_currency),
            'from_amount'   => $request->from_amount,
            'to_amount'     => $toAmount,
            'status'        => 'completed',
        ]);

        Mail::to($request->user()->email)->send(new SwapMail($request->user(), $transaction));

        return response()->json([
            'status' => true,
            'message' => 'Swap successful',
            'transaction' => $transaction,
        ]);
    }


    public function previewSwap(Request $request, CoinGeckoService $coinGecko)
    {
        $request->merge([
            'from_currency' => strtolower($request->query('from_currency')),
            'to_currency'   => strtolower($request->query('to_currency')),
        ]);

        $request->validate([
            'from_currency' => 'required|string|in:btc,eth,xrp,sol,gold,sp500,nasdaq,oil',
            'to_currency'   => 'required|string|in:btc,eth,xrp,sol,gold,sp500,nasdaq,oil|different:from_currency',
            'from_amount'   => 'required|numeric|min:0.00000001',
        ]);

        $fromId = $coinGecko->mapSymbolToId($request->from_currency);
        $toId   = $coinGecko->mapSymbolToId($request->to_currency);

        if (! $fromId || ! $toId) {
            return response()->json(['status' => false, 'message' => 'Unsupported currency'], 400);
        }

        // ✅ Use mapped IDs only
        $prices = $coinGecko->getPrices([$fromId, $toId], ['usd'], 60);

        if (
            !isset($prices[$fromId]['usd']) ||
            !isset($prices[$toId]['usd'])
        ) {
            return response()->json(['status' => false, 'message' => 'Price fetch failed'], 500);
        }

        $fromPriceUsd = $prices[$fromId]['usd'];
        $toPriceUsd   = $prices[$toId]['usd'];

        $toAmount = ($request->from_amount * $fromPriceUsd) / $toPriceUsd;

        return response()->json([
            'status' => true,
            'message' => 'Swap preview calculated',
            'data' => [
                'from_currency'   => strtoupper($request->from_currency),
                'to_currency'     => strtoupper($request->to_currency),
                'from_amount'     => $request->from_amount,
                'from_usd_value'  => $request->from_amount * $fromPriceUsd,
                'to_amount'       => $toAmount,
                'to_usd_value'    => $toAmount * $toPriceUsd,
                'rate_used'       => [
                    'from_price_usd' => $fromPriceUsd,
                    'to_price_usd'   => $toPriceUsd,
                ],
            ],
        ]);
    }





















}
