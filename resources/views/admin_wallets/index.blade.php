@extends('layouts.portal')

@section('content')
    <div class="pagetitle">
        <h1>Wallet Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Admin</li>
                <li class="breadcrumb-item active">Wallets</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Wallet Listing</h6>

                        <x-alerts />

                        <div class="d-flex justify-content-end mb-3">
                            <a href="{{ route('admin.walletmethod.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Wallet
                            </a>
                        </div>

                        <!-- Wallet Table -->
                        <table class="table datatable align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Network</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($wallets as $wallet)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-uppercase">{{ $wallet->network }}</td>
                                        <td>{{ $wallet->address }}</td>
                                        <td>
                                            <span class="badge bg-{{ $wallet->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($wallet->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $wallet->created_at->format('d M, Y h:i A') }}</td>
                                        <td>
                                            <form method="POST"
                                                  action="{{ route('admin.walletmethod.updateStatus', $wallet->id) }}"
                                                  class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-{{ $wallet->status === 'active' ? 'danger' : 'success' }}"
                                                        onclick="return confirm('Are you sure you want to {{ $wallet->status === 'active' ? 'deactivate' : 'activate' }} this wallet?')">
                                                    {{ $wallet->status === 'active' ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No wallets found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- End Wallet Table -->

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
