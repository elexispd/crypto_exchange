<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

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



}
