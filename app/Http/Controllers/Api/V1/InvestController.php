<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invest;
use Illuminate\Http\Request;

class InvestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|uuid|exists:investment_plans,id',
            'network' => 'required|string',
            'amount' => 'required|string',
        ]);

        $userId = $request->user()->id;

        $investment = Invest::create([
            'user_id' => $userId,
            'investment_plan_id' => $validated['plan_id'],
            'network' => $validated['network'],
            'amount' => $validated['amount']
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Wallet successfully assigned',
            'data'  => $investment
        ]);
    }

    // get user's investments.
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $investments = Invest::where('user_id', $userId)
            ->with('investmentPlan') // singular name matches relationship
            ->latest()
            ->get(['id', 'user_id', 'investment_plan_id', 'amount', 'network', 'status']);

        return response()->json([
            'status'  => true,
            'message' => 'Investments retrieved successfully',
            'data'    => $investments,
        ]);
    }



    // Show a single investment
    public function show(Request $request, $invest)
    {
        $userId = $request->user()->id;

        $investment = Invest::where('id', $invest)
            ->where('user_id', $userId)
            ->with('investmentPlan')
            ->first();

        if (!$investment) {
            return response()->json([
                'status'  => false,
                'message' => 'Investment not found',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Investment retrieved successfully',
            'data'    => $investment,
        ]);
    }
}
