<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use App\Models\InvestmentProfit;


class PortfolioController extends Controller
{
    public function portfolioTransactions(Request $request) {
        $investId = $request->invest_id;
        if(!$investId) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid investment id',
            ], 422);
        }
        $returns = InvestmentProfit::where('invest_id', $investId)
            ->latest()
            ->get(['id', 'invest_id', 'profit_amount', 'profit_date', 'credited']);

        return response()->json([
            'status'  => true,
            'message' => 'Portfolio transactions retrieved successfully',
            'data' => $returns,
        ]);
    }


}
