@extends('layouts.portal')

@section('content')
    <div class="pagetitle">
        <h1>{{ ucfirst($type ?? 'All') }} Investments</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Investments</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Investment Portfolio</h6>
                        <x-alerts />

                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Investor</th>
                                    <th>Investment Plan</th>
                                    <th>Amount</th>
                                    <th>Expected Return</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($investments as $investment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $investment->user->name }}</td>
                                        <td>{{ $investment->investmentPlan->name ?? 'N/A' }}</td>
                                        <td>${{ number_format($investment->amount, 8) }}</td>
                                        <td>
                                            @if($investment->investmentPlan)
                                                ${{ number_format($investment->amount * ($investment->investmentPlan->interest_rate / 100), 8) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $investment->status === 'active' ? 'success' : ($investment->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($investment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary viewInvestmentBtn" data-bs-toggle="modal"
                                                data-bs-target="#investmentModal"
                                                data-user="{{ $investment->user->name }}"
                                                data-plan="{{ $investment->investmentPlan->name ?? 'N/A' }}"
                                                data-amount="${{ number_format($investment->amount, 8) }}"
                                                data-interest-rate="{{ $investment->investmentPlan->interest_rate ?? 0 }}%"
                                                data-expected-return="${{ number_format($investment->amount * (($investment->investmentPlan->interest_rate ?? 0) / 100), 8) }}"
                                                data-network="{{ $investment->network ?? 'N/A' }}"
                                                data-status="{{ ucfirst($investment->status) }}"
                                                data-date="{{ $investment->created_at->format('d M, Y h:i A') }}">
                                                View Details
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

        <!-- Investment Modal -->
        <div class="modal fade" id="investmentModal" tabindex="-1" aria-labelledby="investmentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="investmentModalLabel">Investment Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Investor:</strong> <span id="modalUser"></span></li>
                            <li class="list-group-item"><strong>Investment Plan:</strong> <span id="modalPlan"></span></li>
                            <li class="list-group-item"><strong>Investment Amount:</strong> <span id="modalAmount"></span></li>
                            <li class="list-group-item"><strong>Interest Rate:</strong> <span id="modalInterestRate"></span></li>
                            <li class="list-group-item"><strong>Expected Return:</strong> <span id="modalExpectedReturn"></span></li>
                            <li class="list-group-item"><strong>Network:</strong> <span style="text-transform: uppercase;" id="modalNetwork"></span></li>
                            <li class="list-group-item"><strong>Status:</strong> <span id="modalStatus"></span></li>
                            <li class="list-group-item"><strong>Investment Date:</strong> <span id="modalDate"></span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('investmentModal');

                modal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;

                    document.getElementById('modalUser').textContent = button.getAttribute('data-user');
                    document.getElementById('modalPlan').textContent = button.getAttribute('data-plan');
                    document.getElementById('modalAmount').textContent = button.getAttribute('data-amount');
                    document.getElementById('modalInterestRate').textContent = button.getAttribute('data-interest-rate');
                    document.getElementById('modalExpectedReturn').textContent = button.getAttribute('data-expected-return');
                    document.getElementById('modalNetwork').textContent = button.getAttribute('data-network');
                    document.getElementById('modalStatus').textContent = button.getAttribute('data-status');
                    document.getElementById('modalDate').textContent = button.getAttribute('data-date');
                });
            });
        </script>
    </section>
@endsection
