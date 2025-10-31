<?php

namespace App\Http\Controllers;

use App\Models\TransactionFee;
use App\Services\CoinGeckoService;
use Illuminate\Http\Request;

class TransactionFeeController extends Controller
{
    public function create(CoinGeckoService $coinGecko)
    {
        $fees = TransactionFee::all();
        $networks = $coinGecko->getCoinList();
        return view('transactions.create-fee', compact('fees', 'networks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'type' => ['required', 'in:Deposit,Swap,Withdrawal'],
            'network' => ['required', 'string', 'max:255'],
        ]);

        $fee = TransactionFee::updateOrCreate(
            [
                'type' => $validated['type'],
                'network' => $validated['network']
            ],
            [
                'amount' => $validated['amount']
            ]
        );

        return back()->with(
            'success',
            $fee->wasRecentlyCreated
                ? 'Transaction fee added successfully.'
                : 'Transaction fee updated successfully.'
        );
    }

    public function destroy(TransactionFee $fee)
    {
        $fee->delete();
        return back()->with('success', 'Transaction fee deleted successfully.');
    }

}
