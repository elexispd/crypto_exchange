<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\CoinGeckoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\DepositMail;
use App\Mail\SwapMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Services\FeeService;

class TransactionController extends Controller
{
    protected $feeService;

    public function __construct(FeeService $feeService)
    {
        $this->feeService = $feeService;
    }

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
        $validated = $request->validate([
            'wallet_id' => 'required|uuid|exists:wallets,id',
            'network'  => 'required|string|in:BTC,ETH,XRP,SOL,btc,eth,xrp,sol',
            'amount'    => 'required|numeric|min:0.00000001'
        ]);

        $wallet = Wallet::where('id', $validated['wallet_id'])
            ->where('user_id', $user->id)
            ->first();

        $network = strtolower($validated['network']); // Use lowercase for fee service

        if (! $wallet) {
            return response()->json(['status' => false, 'message' => 'Invalid wallet'], 403);
        }

        // Get deposit fee
        $depositFee = $this->feeService->getFee('Deposit', $network, $validated['amount']);


        // If there's a deposit fee, deduct it from the amount
        $netAmount = $validated['amount'] - $depositFee;

        if ($netAmount <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Deposit amount is less than the fee'
            ], 400);
        }

        $transaction = Transaction::create([
            'user_id'   => $user->id,
            'wallet_id' => $wallet->id,
            'type'      => 'deposit',
            'currency'  => strtoupper($network),
            'amount'    => $netAmount, // Store net amount after fee
            'fee'       => $depositFee,
            'status'    => 'pending',
        ]);

        Mail::to($user->email)->send(new DepositMail($user, $transaction));

        return response()->json([
            'status' => true,
            'message' => 'Deposit transaction created',
            'transaction' => $transaction,
            'fee_details' => [
                'deposit_fee' => $depositFee,
                'original_amount' => $validated['amount'],
                'net_amount' => $netAmount,
            ]
        ]);
    }


    // Create a swap transaction
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

        // Get swap fee
        $swapFee = $this->feeService->getFee('Swap', $request->from_currency, $request->from_amount);
        // Calculate total amount to deduct (original amount + fee)
        $totalDeductAmount = $request->from_amount + $swapFee;

        // Check wallet balance including the fee
        $fromBalance = $wallet->getBalance($request->from_currency);
        if ($fromBalance < $totalDeductAmount) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient balance to cover swap amount and fee'
            ], 400);
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

        // Calculate conversion using the original from_amount (before fee)
        $toAmount = ($request->from_amount * $fromPriceUsd) / $toPriceUsd;

        // ✅ Update balances (wrap in DB transaction for safety)
        DB::transaction(function () use ($wallet, $request, $toAmount, $swapFee, $totalDeductAmount) {
            // Deduct the original amount + fee
            $wallet->decrementBalance($request->from_currency, $totalDeductAmount);
            // Credit the converted amount
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
            'fee'           => $swapFee,
            'status'        => 'completed',
        ]);

        Mail::to($request->user()->email)->send(new SwapMail($request->user(), $transaction));

        return response()->json([
            'status' => true,
            'message' => 'Swap successful',
            'transaction' => $transaction,
            'fee_details' => [
                'swap_fee' => $swapFee,
                'total_deducted' => $totalDeductAmount,
                'net_converted_amount' => $toAmount,
            ]
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
}
