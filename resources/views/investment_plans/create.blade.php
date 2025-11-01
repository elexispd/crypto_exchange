@extends('layouts.portal')

@section('content')
    <div class="pagetitle">
        <h1>Create Investment Plan</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Investment</li>
                <li class="breadcrumb-item active">Create Plan</li>
            </ol>
        </nav>
    </div>

    <section class="section profile">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form class="row g-3 py-2" method="POST" action="{{ route('admin.plan.store') }}">
                            @csrf
                            <x-alerts />

                            <div class="col-md-6">
                                <label for="planName" class="form-label">Plan Name</label>
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="planName"
                                    placeholder="e.g. Bitcoin Starter, Ethereum Pro, Gold Premium"
                                    value="{{ old('name') }}"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="network" class="form-label">Network/Asset</label>
                                <select
                                    name="network"
                                    class="form-control @error('network') is-invalid @enderror"
                                    id="network"
                                    required>
                                    <option value="">Select Network</option>
                                    @foreach($supportedNetworks as $key => $name)
                                        <option value="{{ $key }}" {{ old('network') == $key ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('network')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="minAmount" class="form-label">Minimum Amount</label>
                                <div class="input-group">
                                    <input
                                        type="number"
                                        step="any"
                                        name="min_amount"
                                        class="form-control @error('min_amount') is-invalid @enderror"
                                        id="minAmount"
                                        placeholder="e.g. 0.001 for BTC, 100 for Oil"
                                        value="{{ old('min_amount') }}"
                                        required>
                                    <span class="input-group-text" id="amountSuffix">USD</span>
                                </div>
                                <small class="text-muted">Minimum investment amount for this plan</small>
                                @error('min_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="interestRate" class="form-label">Interest Rate (%)</label>
                                <input
                                    type="number"
                                    step="any"
                                    name="interest_rate"
                                    class="form-control @error('interest_rate') is-invalid @enderror"
                                    id="interestRate"
                                    placeholder="e.g. 5.5"
                                    value="{{ old('interest_rate') }}"
                                    required>
                                <small class="text-muted">Percentage return on investment</small>
                                @error('interest_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Network Information</h6>
                                        <div id="networkInfo">
                                            <p class="mb-1">Select a network to see available plans</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Create Plan
                                </button>
                                <a href="{{ route('admin.plan.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Plans
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const networkSelect = document.getElementById('network');
        const networkInfo = document.getElementById('networkInfo');

        const networkDetails = {
            'btc': { plans: 'Bitcoin Plans', note: 'Cryptocurrency investment' },
            'eth': { plans: 'Ethereum Plans', note: 'Smart contract platform' },
            'xrp': { plans: 'Ripple Plans', note: 'Payment protocol' },
            'sol': { plans: 'Solana Plans', note: 'High-performance blockchain' },
            'gold': { plans: 'Gold Plans', note: 'Precious metal investment' },
            'oil': { plans: 'Oil Plans', note: 'Commodity investment' },
            'sp500': { plans: 'S&P 500 Plans', note: 'Stock market index' },
            'nasdaq': { plans: 'Nasdaq Plans', note: 'Technology stock index' }
        };

        function updateNetworkInfo() {
            const selectedNetwork = networkSelect.value;
            if (selectedNetwork && networkDetails[selectedNetwork]) {
                const info = networkDetails[selectedNetwork];
                networkInfo.innerHTML = `
                    <div class="d-flex align-items-center mb-2">
                        <i class="${getNetworkIcon(selectedNetwork)} me-2"></i>
                        <strong>${info.plans}</strong>
                    </div>
                    <p class="mb-0 text-muted">${info.note}</p>
                `;
            } else {
                networkInfo.innerHTML = '<p class="mb-1">Select a network to see available plans</p>';
            }
        }

        function getNetworkIcon(network) {
            const icons = {
                'btc': 'fab fa-bitcoin text-warning',
                'eth': 'fab fa-ethereum text-primary',
                'xrp': 'fas fa-circle text-info',
                'sol': 'fas fa-sun text-warning',
                'gold': 'fas fa-gem text-warning',
                'oil': 'fas fa-gas-pump text-dark',
                'sp500': 'fas fa-chart-line text-success',
                'nasdaq': 'fas fa-chart-bar text-info'
            };
            return icons[network] || 'fas fa-coins text-secondary';
        }

        networkSelect.addEventListener('change', updateNetworkInfo);
        updateNetworkInfo(); // Initialize on page load
    });
</script>
@endsection

