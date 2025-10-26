<?php

namespace App\Http\Controllers;

use App\Models\Invest;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('q');

        $investments = Invest::query()
            ->when($type, fn($q) => $q->where('status', $type))
            ->with(['user', 'investmentPlan'])
            ->latest()
            ->get();
        return view('investments.index', compact('investments', 'type'));
    }
}
