<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\InvestmentPlan;
use App\Services\CoinGeckoService;

class InvestmentPlanController extends Controller
{
    public $coinGecko;
    public function __construct(CoinGeckoService $coinGecko)
    {
        $this->coinGecko = $coinGecko;
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'network' => 'required|string|max:255',
            'min_amount' => "required",
            'interest_rate' => "required",
        ]);
        $plan = InvestmentPlan::create($validated);
        return redirect()->back()->with('success', 'Plan created successfully.');
    }

    public function create() {
        return view('investment_plans.create', [
            'supportedNetworks' =>  $this->coinGecko->getCoinListSingle(),
        ]);
    }

    public function index() {
        $plans = InvestmentPlan::all();
        $plansByNetwork = $plans->groupBy('network');

        return view('investment_plans.index', [
            'plans' => $plans,
            'plansByNetwork' => $plansByNetwork,
            'supportedNetworks' => $this->coinGecko->getCoinList(),
        ]);
    }

    public function update(Request $request) {
        $validated = $request->validate([
            'plan_id' => 'required|exists:investment_plans,id',
            'name' => 'required|string|max:255',
            'network' => 'required|string|max:255',
            'min_amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
        ]);

        $plan = InvestmentPlan::findOrFail($validated['plan_id']);
        $plan->update($validated);

        return redirect()->back()->with('success', 'Plan updated successfully.');
    }

    public function changeStatus(Request $request) {
        $plan = InvestmentPlan::findOrFail($request->plan_id);
        $plan->status = !$plan->status;
        $plan->save();
        return redirect()->back()->with('success', 'Plan status updated successfully.');
    }

     public function getPlansByNetwork($network) {
        $plans = InvestmentPlan::where('network', $network)
            ->active()
            ->get();

        return response()->json([
            'network' => $network,
            'network_name' => $this->coinGecko[$network] ?? ucfirst($network),
            'plans' => $plans
        ]);
    }

}
