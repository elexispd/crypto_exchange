@extends('layouts.portal')

@section('content')

<div class="pagetitle">
    <h1>{{ $user->name }}'s Transactions</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Portfolio</li>
            <li class="breadcrumb-item active">Transactions</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <!-- Filters Card -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Filters</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select class="form-select" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="deposit">Deposit</option>
                                <option value="withdraw">Withdraw</option>
                                <option value="swap">Swap</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="currencyFilter">
                                <option value="">All Currencies</option>
                                <option value="BTC">BTC</option>
                                <option value="ETH">ETH</option>
                                <option value="SOL">SOL</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="completed">Completed</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-secondary w-100" id="resetFilters">Reset Filters</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Card -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Transaction History</h5>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">CSV</a></li>
                                <li><a class="dropdown-item" href="#">PDF</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover datatable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Currency</th>
                                    <th>Amount</th>
                                    <th>Fee</th>
                                    <th>From/To</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr data-type="{{ $transaction->type }}" data-currency="{{ $transaction->currency ?? $transaction->from_currency }}" data-status="{{ $transaction->status }}">
                                        <td>
                                            <div class="fw-bold">{{ $transaction->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $transaction->created_at->format('H:i A') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge transaction-type-{{ $transaction->type }}">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($transaction->type === 'swap')
                                                <div class="d-flex align-items-center">
                                                    <span class="currency-badge">{{ $transaction->from_currency }}</span>
                                                    <i class="bi bi-arrow-right mx-2 text-muted"></i>
                                                    <span class="currency-badge">{{ $transaction->to_currency }}</span>
                                                </div>
                                            @else
                                                <span class="currency-badge">{{ strtoupper($transaction->currency) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->type === 'swap')
                                                <div class="text-end">
                                                    <div class="fw-bold text-danger">-{{ number_format($transaction->from_amount, 8) }}</div>
                                                    <div class="fw-bold text-success">+{{ number_format($transaction->to_amount, 8) }}</div>
                                                </div>
                                            @else
                                                <div class="fw-bold {{ $transaction->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount, 8) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->fee)
                                                <span class="text-muted">{{ number_format($transaction->fee, 8) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->to_address)
                                                <small class="text-muted font-monospace">
                                                    {{ Str::limit($transaction->to_address, 12) }}
                                                </small>
                                            @elseif($transaction->narrative)
                                                <small class="text-muted">{{ $transaction->narrative }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($transaction->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($transaction->status === 'completed')
                                                <span class="badge bg-primary">Completed</span>
                                            @elseif($transaction->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary view-details"
                                                    data-bs-toggle="tooltip"
                                                    title="View Details"
                                                    data-transaction-id="{{ $transaction->id }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table -->

                </div>
            </div>

        </div>
    </div>
</section>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="transactionDetails">
                <!-- Details will be loaded here via JavaScript -->
            </div>
        </div>
    </div>
</div>

<style>
.transaction-type-deposit {
    background-color: #d1e7dd;
    color: #0f5132;
}

.transaction-type-withdraw {
    background-color: #f8d7da;
    color: #721c24;
}

.transaction-type-swap {
    background-color: #cfe2ff;
    color: #084298;
}

.currency-badge {
    background-color: #e9ecef;
    color: #495057;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.view-details {
    transition: all 0.2s ease;
}

.view-details:hover {
    transform: scale(1.1);
}
</style>

<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Filter functionality
    $('#typeFilter, #currencyFilter, #statusFilter').on('change', function() {
        filterTable();
    });

    $('#resetFilters').on('click', function() {
        $('#typeFilter, #currencyFilter, #statusFilter').val('');
        filterTable();
    });

    function filterTable() {
        const typeValue = $('#typeFilter').val();
        const currencyValue = $('#currencyFilter').val();
        const statusValue = $('#statusFilter').val();

        $('tbody tr').each(function() {
            const rowType = $(this).data('type');
            const rowCurrency = $(this).data('currency');
            const rowStatus = $(this).data('status');

            const typeMatch = !typeValue || rowType === typeValue;
            const currencyMatch = !currencyValue || rowCurrency === currencyValue;
            const statusMatch = !statusValue || rowStatus === statusValue;

            if (typeMatch && currencyMatch && statusMatch) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // View details functionality
    const transactionModal = new bootstrap.Modal('#transactionModal');

    $('.view-details').on('click', function() {
        const transactionId = $(this).data('transaction-id');

        $('#transactionDetails').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading transaction details...</p>
            </div>
        `);

        transactionModal.show();

        // Simulate API call - replace this with actual AJAX call
        setTimeout(() => {
            $('#transactionDetails').html(`
                <div class="row">
                    <div class="col-md-6">
                        <strong>Transaction ID:</strong>
                        <p class="text-muted">${transactionId}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Date:</strong>
                        <p class="text-muted">November 1, 2025 00:49:59</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Type:</strong>
                        <p class="text-muted">Deposit</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p><span class="badge bg-warning">Pending</span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <strong>Details:</strong>
                        <p class="text-muted">Full transaction details would be displayed here.</p>
                    </div>
                </div>
            `);
        }, 1000);
    });
});
</script>
@endsection
