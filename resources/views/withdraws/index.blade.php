@extends('layouts.portal')

@section('content')
    <div class="pagetitle">
        <h1>Withdrawal List</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Withdrawals</li>
                <li class="breadcrumb-item active">Data</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title"> {{ ucfirst($status) }} withdraw Listing</h6>
                        <x-alerts />
                        <!-- Table with stripped rows -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>
                                        Network
                                    </th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($withdraws as $withdraw)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ ucfirst($withdraw->currency) }}</td>
                                        <td>{{ number_format($withdraw->amount, 2) }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $withdraw->status === 'approved' ? 'success' : ($withdraw->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($withdraw->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary viewwithdrawBtn" data-bs-toggle="modal"
                                                data-bs-target="#withdrawModal" data-user="{{ $withdraw->user->name }}"
                                                data-currency="{{ $withdraw->currency }}"
                                                data-narration="{{ $withdraw->narrative ?? '—' }}"
                                                data-address="{{ $withdraw->to_address ?? '—' }}"
                                                data-amount="{{ number_format($withdraw->amount, 2, '.', '') }}"
                                                data-status="{{ ucfirst($withdraw->status) }}"
                                                data-date="{{ $withdraw->created_at->format('d M, Y h:i A') }}"
                                                data-id="{{ $withdraw->id }}">
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

        <!-- withdraw Modal -->
        <div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="withdrawModalLabel">Withdraw Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>User:</strong> <span id="modalUser"></span></li>
                            <li class="list-group-item"><strong>Currency:</strong> <span id="modalCurrency"></span></li>
                            <li class="list-group-item"><strong>Wallet Address:</strong> <span id="modalWallet"></span></li>
                            <li class="list-group-item"><strong>Amount:</strong> <span id="modalAmount"></span></li>
                            <li class="list-group-item"><strong>Narration:</strong> <span id="modalNarration"></span></li>
                            <li class="list-group-item"><strong>Status:</strong> <span id="modalStatus"></span></li>
                            <li class="list-group-item"><strong>withdrawed At:</strong> <span id="modalDate"></span></li>
                        </ul>
                    </div>



                    <div class="modal-footer d-flex justify-content-between align-items-center">
                        <div id="statusBadgeContainer">
                            <span id="statusBadge" class="badge"></span>
                        </div>

                        <div id="actionButtons">
                            <form id="approveForm" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-success">Approve</button>
                            </form>

                            <form id="declineForm" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-danger">Decline</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const withdrawModal = document.getElementById('withdrawModal');

                withdrawModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;

                    const user = button.getAttribute('data-user');
                    const currency = button.getAttribute('data-currency');
                    const wallet = button.getAttribute('data-address');
                    const amount = button.getAttribute('data-amount');
                    const narration = button.getAttribute('data-narration');
                    const status = button.getAttribute('data-status').toLowerCase();
                    const date = button.getAttribute('data-date');
                    const id = button.getAttribute('data-id');

                    // Populate modal fields
                    document.getElementById('modalUser').textContent = user;
                    document.getElementById('modalCurrency').textContent = currency;
                    document.getElementById('modalWallet').textContent = wallet;
                    document.getElementById('modalAmount').textContent = amount;
                    document.getElementById('modalNarration').textContent = narration;
                    document.getElementById('modalStatus').textContent = status.charAt(0).toUpperCase() + status
                        .slice(1);
                    document.getElementById('modalDate').textContent = date;

                    // Update forms' action URLs dynamically
                    document.getElementById('approveForm').action = `/withdraw/${id}`;
                    document.getElementById('declineForm').action = `/withdraw/${id}`;

                    const actionButtons = document.getElementById('actionButtons');
                    const badgeContainer = document.getElementById('statusBadgeContainer');
                    const badge = document.getElementById('statusBadge');

                    // Reset visibility
                    actionButtons.style.display = 'none';
                    badgeContainer.style.display = 'none';

                    // Show/hide based on status
                    if (status === 'pending') {
                        actionButtons.style.display = 'block';
                    } else {
                        badgeContainer.style.display = 'block';

                        // Style badge based on status
                        badge.className = 'badge';
                        if (status === 'approved') {
                            badge.classList.add('bg-success');
                            badge.textContent = 'Approved';
                        } else if (status === 'rejected') {
                            badge.classList.add('bg-danger');
                            badge.textContent = 'Rejected';
                        } else {
                            badge.classList.add('bg-secondary');
                            badge.textContent = status;
                        }
                    }
                });
            });
        </script>


    </section>
@endsection
