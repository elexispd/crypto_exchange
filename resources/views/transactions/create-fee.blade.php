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
        <h1>Service Fee</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Transaction</li>
                <li class="breadcrumb-item active">Fee</li>
            </ol>
        </nav>
    </div>

    <section class="section profile">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form class="row g-3 py-2" method="POST" action="{{ route('admin.transaction.storeFee') }}">
                            @csrf
                            <x-alerts />

                            <div class="col-md-6">
                                <label for="inputName5" class="form-label">Amount</label>
                                <input type="text" name="amount"
                                    class="form-control @error('amount') is-invalid @enderror" id="inputName5"
                                    value="{{ $fee->amount ?? 0 }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="t-3">
                                <button type="submit" class="btn btn-primary">
                                    {{ $fee?->exists ? 'Update' : 'Add' }}
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
