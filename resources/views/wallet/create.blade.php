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
        <h1>Create Wallet</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Wallet</li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    <section class="section profile">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form class="row g-3" method="POST" action="{{ route('admin.wallet.store') }}">
                            @csrf
                            <x-alerts />

                            <div class="col-md-12 mt-4">
                                <label for="inputName5" class="form-label">Bitcoin</label>
                                <input type="text" name="btc_address"
                                    class="form-control @error('btc_address') is-invalid @enderror" id="inputName5"
                                    value="{{ old('btc_address') }}" required>
                                @error('btc_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-3">
                                <label for="inputName5" class="form-label">Ethereum</label>
                                <input type="text" name="eth_address"
                                    class="form-control @error('eth_address') is-invalid @enderror" id="inputName5"
                                    value="{{ old('eth_address') }}" required>
                                @error('eth_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-3">
                                <label for="inputName5" class="form-label">XRP</label>
                                <input type="text" name="xrp_address"
                                    class="form-control @error('xrp_address') is-invalid @enderror" id="inputName5"
                                    value="{{ old('xrp_address') }}" required>
                                @error('xrp_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-3">
                                <label for="inputName5" class="form-label">Solana</label>
                                <input type="text" name="solana_address"
                                    class="form-control @error('solana_address') is-invalid @enderror" id="inputName5"
                                    value="{{ old('solana_address') }}" required>
                                @error('solana_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary">Add</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
