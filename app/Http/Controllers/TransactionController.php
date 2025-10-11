<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionFee;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('q');

        $transactions = Transaction::query()
            ->when($type, fn($q) => $q->where('type', $type))
            ->with('user')
            ->latest()
            ->get();

        return view('transactions.index', compact('transactions', 'type'));
    }

    public function fees()
    {
        $fee = TransactionFee::first();
        return view('transactions.create-fee', compact('fee'));
    }

    public function storeFee(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $fee = TransactionFee::updateOrCreate(
            ['id' => 1],
            ['amount' => $validated['amount']]
        );

        return back()->with(
            'success',
            $fee->wasRecentlyCreated
                ? 'Transaction fee added successfully.'
                : 'Transaction fee updated successfully.'
        );
    }
}
