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
        <h1>Add Wallet</h1>
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
                        <form class="row g-3 py-3" method="POST" action="{{ route('admin.walletmethod.store') }}">
                            @csrf
                            <x-alerts />


                            <div class="col-md-12 mt-3">
                                <label for="inputName5" class="form-label">Network</label>
                                <select name="network" class="form-control @error('network') is-invalid @enderror" id="inputName5" required>
                                    <option value="">Select Network</option>
                                    <option value="BTC">BTC</option>
                                    <option value="ETH">ETH</option>
                                    <option value="SOL">SOL</option>
                                    <option value="XRP">XRP</option>
                                </select>
                                @error('network')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="inputEmail5" class="form-label">Address</label>
                                <input type="text" name="address"
                                    class="form-control @error('address') is-invalid @enderror" id="inputEmail5"
                                    value="{{ old('address') }}" required>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class=" mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
