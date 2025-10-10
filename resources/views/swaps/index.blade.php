@extends('layouts.portal')

@section('content')
    <div class="pagetitle">
        <h1>Swap List</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Swaps</li>
                <li class="breadcrumb-item active">Data</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title"> Current Swap Listing</h6>
                        <x-alerts />
                        <!-- Table with stripped rows -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>User</th>
                                    <th>
                                        From Coin/Asset
                                    </th>
                                    <th>
                                        To Coin/Asset
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($swaps as $swap)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $swap->user->name }}</td>
                                        <td>{{ $swap->from_currency }}</td>
                                        <td>{{ $swap->to_currency }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary viewswapBtn" data-bs-toggle="modal"
                                                data-bs-target="#swapModal" data-user="{{ $swap->user->name }}"
                                                data-from="{{ strtoupper($swap->from_currency) }}"
                                                data-to="{{ strtoupper($swap->to_currency) }}"
                                                data-from-amount="{{ number_format($swap->from_amount, 2) }}"
                                                data-to-amount="{{ number_format($swap->to_amount, 2) }}"
                                                data-date="{{ $swap->created_at->format('d M, Y h:i A') }}">
                                                View
                                            </button>
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

        <!-- swap Modal -->
        <div class="modal fade" id="swapModal" tabindex="-1" aria-labelledby="swapModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="swapModalLabel">Swap Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>User:</strong> <span id="modalUser"></span></li>
                            <li class="list-group-item"><strong>From:</strong> <span id="modalFrom"></span></li>
                            <li class="list-group-item"><strong>To:</strong> <span id="modalTO"></span></li>
                            <li class="list-group-item"><strong>From Amount:</strong> <span id="modalFromAmount"></span>
                            </li>
                            <li class="list-group-item"><strong>To Amount:</strong> <span id="modalToAmount"></span></li>
                            <li class="list-group-item"><strong>Swapped At:</strong> <span id="modalDate"></span></li>
                        </ul>

                    </div>

                </div>
            </div>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const swapModal = document.getElementById('swapModal');

                swapModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;

                    const user = button.getAttribute('data-user');
                    const fromCurrency = button.getAttribute('data-from');
                    const toCurrency = button.getAttribute('data-to');
                    const fromAmount = button.getAttribute('data-from-amount');
                    const toAmount = button.getAttribute('data-to-amount');
                    const date = button.getAttribute('data-date');

                    // Populate modal fields
                    document.getElementById('modalUser').textContent = user;
                    document.getElementById('modalFrom').textContent = fromCurrency;
                    document.getElementById('modalTO').textContent = toCurrency;
                    document.getElementById('modalFromAmount').textContent = fromAmount ?? '-';
                    document.getElementById('modalToAmount').textContent = toAmount ?? '-';
                    document.getElementById('modalDate').textContent = date;
                });
            });
        </script>



    </section>
@endsection
