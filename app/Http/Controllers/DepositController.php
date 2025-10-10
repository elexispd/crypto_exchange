<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $deposits = Transaction::query()
            ->where('type', 'deposit')
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->with('user')
            ->latest()
            ->get();

        return view('deposits.index', compact('deposits', 'status'));
    }


    public function update(Request $request, Transaction $deposit)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        if ($request->action === 'approve') {
            $deposit->update([
                'status' => 'approved'
            ]);
        } else {
            $deposit->update([
                'status' => 'rejected'
            ]);
        }

        return back()->with('success', "Deposit {$request->action}d successfully.");
    }


    public function destroy(Deposit $deposit)
    {
        $deposit->delete();
        return back()->with('success', 'Deposit deleted successfully.');
    }

}
