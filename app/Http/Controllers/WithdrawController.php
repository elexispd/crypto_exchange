<?php

namespace App\Http\Controllers;

use App\Mail\WithdrawStatusMail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        $status = $request->action === 'approve' ? 'approved' : 'rejected';

        if ($request->action === 'approve') {
            $withdraw->update([
                'status' => $status
            ]);
        } else {
            $withdraw->update([
                'status' => $status
            ]);
        }


        // send email
        Mail::to($withdraw->user->email)->send(new WithdrawStatusMail($withdraw->user, $withdraw, $status));

        return back()->with('success', "Withdrawal {$request->action}d successfully.");
    }


}
