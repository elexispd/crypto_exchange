<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class PortfolioController extends Controller
{
    public function portfolioTransactions(Request $request) {
        return $request->user()->wallet->transactions()->latest()->get();
    }
}
