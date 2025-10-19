<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\InvestmentPlan;

class InvestmentPlanController extends Controller
{
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_amount' => "required",
            'interest_rate' => "required",
        ]);
        $plan = InvestmentPlan::create($validated);
        return redirect()->back()->with('success', 'Plan created successfully.');
    }

    public function create() {
        return view('investment_plans.create');
    }

    public function index() {
        $plans = InvestmentPlan::all();
        return view('investment_plans.index', compact('plans'));
    }

    public function update(Request $request) {
        $validated = $request->validate([
            'plan_id' => 'required|exists:investment_plans,id',
            'name' => 'required|string|max:255',
            'min_amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
        ]);

        $plan = InvestmentPlan::findOrFail($validated['plan_id']);
        $plan->update($validated);

        return redirect()->back()->with('success', 'Plan updated successfully.');
    }

    public function updateStatus(Request $request) {
        $plan = InvestmentPlan::findOrFail($request->plan_id);
        $plan->status = !$plan->status;
        $plan->save();
        return redirect()->back()->with('success', 'Plan status updated successfully.');
    }

}
