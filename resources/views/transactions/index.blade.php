@extends('layouts.portal')

@section('content')
    <div class="pagetitle">
        <h1>{{ ucfirst($type ?? 'All') }} Transactions</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Transactions</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Transaction Listing</h6>
                        <x-alerts />

                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    @if ($type === 'swap')
                                        <th>From Coin/Asset</th>
                                        <th>To Coin/Asset</th>
                                    @else
                                        <th>Currency</th>
                                        <th>Amount</th>
                                    @endif
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $transaction->user->name }}</td>

                                        @if ($transaction->type === 'swap')
                                            <td>{{ strtoupper($transaction->from_currency) }}</td>
                                            <td>{{ strtoupper($transaction->to_currency) }}</td>
                                        @else
                                            <td>{{ strtoupper($transaction->currency) }}</td>
                                            <td>{{ number_format($transaction->amount, 2) }}</td>
                                        @endif

                                        <td>{{ strtoupper($transaction->type) }}</td>

                                        <td>
                                            <button class="btn btn-sm btn-primary viewTransactionBtn" data-bs-toggle="modal"
                                                data-bs-target="#transactionModal"
                                                data-user="{{ $transaction->user->name }}"
                                                data-type="{{ $transaction->type }}"
                                                data-currency="{{ strtoupper($transaction->currency ?? '-') }}"
                                                data-amount="{{ number_format($transaction->amount ?? 0, 2) }}"
                                                data-from="{{ strtoupper($transaction->from_currency ?? '-') }}"
                                                data-to="{{ strtoupper($transaction->to_currency ?? '-') }}"
                                                data-from-amount="{{ number_format($transaction->from_amount ?? 0, 2) }}"
                                                data-to-amount="{{ number_format($transaction->to_amount ?? 0, 2) }}"
                                                data-date="{{ $transaction->created_at->format('d M, Y h:i A') }}">
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Modal -->
        <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="transactionModalLabel">Transaction Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <ul class="list-group list-group-flush" id="swapSection" style="display: none;">
                            <li class="list-group-item"><strong>User:</strong> <span id="modalUser"></span></li>
                            <li class="list-group-item"><strong>From:</strong> <span id="modalFrom"></span></li>
                            <li class="list-group-item"><strong>To:</strong> <span id="modalTo"></span></li>
                            <li class="list-group-item"><strong>From Amount:</strong> <span id="modalFromAmount"></span>
                            </li>
                            <li class="list-group-item"><strong>To Amount:</strong> <span id="modalToAmount"></span></li>
                            <li class="list-group-item"><strong>Swapped At:</strong> <span id="modalDate"></span></li>
                        </ul>

                        <ul class="list-group list-group-flush" id="normalSection" style="display: none;">
                            <li class="list-group-item"><strong>User:</strong> <span id="modalUserNormal"></span></li>
                            <li class="list-group-item"><strong>Currency:</strong> <span id="modalCurrency"></span></li>
                            <li class="list-group-item"><strong>Amount:</strong> <span id="modalAmount"></span></li>
                            <li class="list-group-item"><strong>Transaction Date:</strong> <span
                                    id="modalDateNormal"></span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('transactionModal');

                modal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;

                    const type = button.getAttribute('data-type');
                    const user = button.getAttribute('data-user');
                    const currency = button.getAttribute('data-currency');
                    const amount = button.getAttribute('data-amount');
                    const from = button.getAttribute('data-from');
                    const to = button.getAttribute('data-to');
                    const fromAmount = button.getAttribute('data-from-amount');
                    const toAmount = button.getAttribute('data-to-amount');
                    const date = button.getAttribute('data-date');

                    const swapSection = document.getElementById('swapSection');
                    const normalSection = document.getElementById('normalSection');

                    // Reset visibility
                    swapSection.style.display = 'none';
                    normalSection.style.display = 'none';

                    if (type === 'swap') {
                        swapSection.style.display = 'block';
                        document.getElementById('modalUser').textContent = user;
                        document.getElementById('modalFrom').textContent = from;
                        document.getElementById('modalTo').textContent = to;
                        document.getElementById('modalFromAmount').textContent = fromAmount;
                        document.getElementById('modalToAmount').textContent = toAmount;
                        document.getElementById('modalDate').textContent = date;
                    } else {
                        normalSection.style.display = 'block';
                        document.getElementById('modalUserNormal').textContent = user;
                        document.getElementById('modalCurrency').textContent = currency;
                        document.getElementById('modalAmount').textContent = amount;
                        document.getElementById('modalDateNormal').textContent = date;
                    }
                });
            });
        </script>

    </section>
@endsection
