@extends('layouts.portal')

@section('content')
    <div class="pagetitle">
        <h1>Wallet List</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Wallet</li>
                <li class="breadcrumb-item active">Data</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @if ($type)
                            <h6 class="card-title"> These are unused wallets. You can add new wallets from the <a
                                href="{{ route('admin.wallet.create') }}">Create Wallet</a> page.</h6>
                        @endif

                        <x-alerts/>
                        <!-- Table with stripped rows -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>User</th>
                                    <th>
                                        Bitcoin
                                    </th>
                                    <th>Etherum</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($wallets as $wallet)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $wallet->user->name ?? 'N/A'}}</td>
                                        <td>{{ $wallet->btc_address }}</td>
                                        <td>{{ $wallet->eth_address }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary viewWalletBtn" data-bs-toggle="modal"
                                                data-bs-target="#walletModal" data-wallet='@json($wallet)'>
                                                View
                                            </button>
                                            <a href="{{ route('admin.wallet.destroy', $wallet) }}"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this wallet?')">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- End Table with stripped rows -->

                    </div>
                </div>

            </div>
        </div>


        <!-- Wallet Modal -->
        <div class="modal fade" id="walletModal" tabindex="-1" aria-labelledby="walletModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="walletModalLabel">Wallet Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="fw-semibold">Secret Phrase:</label>
                            <p id="wallet-secret_phrase" class="text-muted small"></p>
                        </div>
                        <hr>
                        <div class="mb-2">
                            <label class="fw-semibold">Bitcoin Address:</label>
                            <p id="wallet-btc_address" class="text-muted"></p>
                        </div>
                        <div class="mb-2">
                            <label class="fw-semibold">Ethereum Address:</label>
                            <p id="wallet-eth_address" class="text-muted"></p>
                        </div>
                        <div class="mb-2">
                            <label class="fw-semibold">XRP Address:</label>
                            <p id="wallet-xrp_address" class="text-muted"></p>
                        </div>
                        <div class="mb-2">
                            <label class="fw-semibold">Solana Address:</label>
                            <p id="wallet-solana_address" class="text-muted"></p>
                        </div>
                        <hr>
                        <div class="mb-2">
                            <label class="fw-semibold">Created By:</label>
                            <p id="wallet-created_by" class="text-muted"></p>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>



        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const walletModal = document.getElementById('walletModal');

                document.querySelectorAll('.viewWalletBtn').forEach(button => {
                    button.addEventListener('click', function() {
                        const wallet = JSON.parse(this.dataset.wallet);

                        document.getElementById('wallet-secret_phrase').textContent = wallet
                            .secret_phrase || '—';
                        document.getElementById('wallet-btc_address').textContent = wallet
                            .btc_address || '—';
                        document.getElementById('wallet-eth_address').textContent = wallet
                            .eth_address || '—';
                        document.getElementById('wallet-xrp_address').textContent = wallet
                            .xrp_address || '—';
                        document.getElementById('wallet-solana_address').textContent = wallet
                            .solana_address || '—';
                        document.getElementById('wallet-created_by').textContent = wallet.creator_name ||
                            '—';
                    });
                });
            });
        </script>




    </section>
@endsection
