<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $withdraws = Transaction::query()
            ->where('type', 'withdraw')
            ->when($status, fn($q) => $q->where('status', $status))
            ->with('user')
            ->latest()
            ->get();

        return view('withdraws.index', compact('withdraws', 'status'));
    }

    public function update(Request $request, Transaction $withdraw)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        if ($request->action === 'approve') {
            $withdraw->update([
                'status' => 'approved'
            ]);
        } else {
            $withdraw->update([
                'status' => 'rejected'
            ]);
        }

        return back()->with('success', "Withdrawal {$request->action}d successfully.");
    }


}
