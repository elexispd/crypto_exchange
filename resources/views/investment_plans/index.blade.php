@extends('layouts.portal')

@section('content')
<div class="pagetitle">
    <h1>Investment Plans</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Investment Plans</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Network Summary Cards -->
        <div class="col-lg-12">
            <div class="row">
                @foreach($supportedNetworks as $key => $name)
                @php
                    $networkPlans = $plans->where('network', $key);
                    $activePlans = $networkPlans->where('status', true)->count();
                @endphp
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-{{ getNetworkColor($key) }} shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-{{ getNetworkColor($key) }} text-uppercase mb-1">
                                        {{ $name }}
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $activePlans }} Active Plans
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="{{ getNetworkIconClass($key) }} fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Plans Table -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <x-alerts />
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">All Investment Plans</h5>
                        <a href="{{ route('admin.plan.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create New Plan
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Plan Name</th>
                                    <th>Network</th>
                                    <th>Min Amount</th>
                                    <th>Interest Rate</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plans as $plan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $plan->name }}</strong>
                                    </td>
                                    <td>
                                       {{ ucfirst($plan->network) }}
                                    </td>
                                    <td>
                                        {{ number_format($plan->min_amount, 8) }}
                                        <small class="text-muted">USD</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $plan->interest_rate }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $plan->status ? 'success' : 'danger' }}">
                                            {{ $plan->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPlanModal{{ $plan->id }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <form action="{{ route('admin.plan.changeStatus') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $plan->status ? 'warning' : 'success' }}">
                                                <i class="bi bi-{{ $plan->status ? 'pause' : 'play' }}"></i>
                                                {{ $plan->status ? 'Pause' : 'Activate' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@foreach($plans as $plan)
<!-- Edit Plan Modal -->
<div class="modal fade" id="editPlanModal{{ $plan->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Plan: {{ $plan->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.plan.update') }}">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName{{ $plan->id }}" class="form-label">Plan Name</label>
                        <input type="text" class="form-control" id="editName{{ $plan->id }}" name="name" value="{{ $plan->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="editNetwork{{ $plan->id }}" class="form-label">Network</label>
                        <select class="form-control" id="editNetwork{{ $plan->id }}" name="network" required>
                            @foreach($supportedNetworks as $key => $name)
                                <option value="{{ $key }}" {{ $plan->network == $key ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editMinAmount{{ $plan->id }}" class="form-label">Minimum Amount</label>
                        <input type="number" step="any" class="form-control" id="editMinAmount{{ $plan->id }}" name="min_amount" value="{{ $plan->min_amount }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="editInterestRate{{ $plan->id }}" class="form-label">Interest Rate (%)</label>
                        <input type="number" step="any" class="form-control" id="editInterestRate{{ $plan->id }}" name="interest_rate" value="{{ $plan->interest_rate }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@php
function getNetworkColor($network) {
    $colors = [
        'btc' => 'warning',
        'eth' => 'primary',
        'xrp' => 'info',
        'sol' => 'warning',
        'gold' => 'warning',
        'oil' => 'dark',
        'sp500' => 'success',
        'nasdaq' => 'info',
    ];
    return $colors[$network] ?? 'secondary';
}

function getNetworkIconClass($network) {
    $icons = [
        'btc' => 'fab fa-bitcoin',
        'eth' => 'fab fa-ethereum',
        'xrp' => 'fas fa-circle',
        'sol' => 'fas fa-sun',
        'gold' => 'fas fa-gem',
        'oil' => 'fas fa-gas-pump',
        'sp500' => 'fas fa-chart-line',
        'nasdaq' => 'fas fa-chart-bar',
    ];
    return $icons[$network] ?? 'fas fa-coins';
}
@endphp
