<?php

namespace App\Http\Controllers;

use App\Models\KycDocument;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userCount = User::where('is_admin', false)->count();

        $depositSum = Transaction::query()
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->sum('amount');

        $kycCount = KycDocument::count();

        $recentDeposits = Transaction::with('user')
            ->where('type', 'deposit')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'userCount',
            'depositSum',
            'kycCount',
            'recentDeposits'
        ));
    }
}
