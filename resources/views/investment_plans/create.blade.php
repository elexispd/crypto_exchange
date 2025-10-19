@extends('layouts.portal')

<style>
    .info-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        border-left: 4px solid #4e73df;
    }

    .section-title {
        font-size: 1.1rem;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 8px;
    }

    .info-item {
        padding: 5px 0;
    }

    .text-monospace {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.85em;
    }
</style>

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
                                    placeholder="e.g. Silver Plan"
                                    value="{{ old('name') }}"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="minAmount" class="form-label">Minimum Amount</label>
                                <input
                                    type="number"
                                    step="any"
                                    name="min_amount"
                                    class="form-control @error('min_amount') is-invalid @enderror"
                                    id="minAmount"
                                    placeholder="e.g. 100"
                                    value="{{ old('min_amount') }}"
                                    required>
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
                                    placeholder="e.g. 5"
                                    value="{{ old('interest_rate') }}"
                                    required>
                                @error('interest_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary">
                                    Create Plan
                                </button>
                                <a href="{{ route('admin.plan.index') }}" class="btn btn-secondary">
                                    Back to Plans
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
