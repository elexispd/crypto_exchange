<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Invest;

class PortfolioController extends Controller
{
    public function portfolioTransactions(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);
        if (!$user->wallet) {
            return redirect()->back()->with('success', 'User has no wallet.');
        }
        $transactions = $user->wallet->transactions()->latest()->get();
        return view('users.portfolio.transactions', compact('transactions', 'user'));
    }

    public function portfolioStakes(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);
        $stakes = $user->invests()->with('investmentPlan', 'profits')->latest()->get();
        return view('users.portfolio.stakes', compact('stakes', 'user'));
    }
}
