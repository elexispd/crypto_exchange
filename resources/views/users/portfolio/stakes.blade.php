@extends('layouts.portal')

@section('content')

    <div class="pagetitle">
        <h1>{{ $user->name }}'s Investment Portfolio</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Portfolio</li>
                <li class="breadcrumb-item active">Stakes</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <!-- Summary Cards -->
            <div class="col-lg-3 col-md-6">
                <div class="card info-card sales-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Profit</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <div class="ps-3">
                                <h6>${{ number_format($stakes->sum('totalProfit'), 2) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card info-card customers-card">
                    <div class="card-body">
                        <h5 class="card-title">Active Stakes</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ $stakes->where('status', 'active')->count() }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card info-card customers-card">
                    <div class="card-body">
                        <h5 class="card-title">All Stakes</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ $stakes->count() }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <h5 class="card-title">Pending Profit</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="ps-3">
                                <h6>${{ number_format($stakes->sum('pendingProfit'), 2) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <!-- Filters Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Filters</h5>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select class="form-select" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="planFilter">
                                    <option value="">All Plans</option>
                                    @foreach ($stakes->pluck('investmentPlan.name')->unique() as $planName)
                                        @if ($planName)
                                            <option value="{{ $planName }}">{{ $planName }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="networkFilter">
                                    <option value="">All Networks</option>
                                    @foreach ($stakes->pluck('network')->unique() as $network)
                                        @if ($network)
                                            <option value="{{ $network }}">{{ strtoupper($network) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-secondary w-100" id="resetFilters">Reset Filters</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stakes Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Investment Stakes</h5>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown">
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
                                        <th>S/N</th>
                                        <th>Investment Date</th>
                                        <th>Plan</th>
                                        <th>Network</th>
                                        <th>Amount</th>
                                        <th>Lock Period</th>
                                        <th>Status</th>
                                        <th>Profit</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stakes as $stake)
                                        <tr data-status="{{ $stake->status }}"
                                            data-plan="{{ $stake->investmentPlan->name ?? '' }}"
                                            data-network="{{ $stake->network }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $stake->created_at->format('d M, Y') }}</div>
                                                <small class="text-muted">{{ $stake->created_at->format('H:i A') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge plan-badge">
                                                    {{ $stake->investmentPlan->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="network-badge">{{ strtoupper($stake->network) }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-primary">${{ number_format($stake->amount, 2) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-calendar-check me-2 text-muted"></i>
                                                    <span>{{ $stake->lock_period }} days</span>
                                                </div>
                                                @if($stake->invested_at)
                                                    @php
                                                        $lockEndDate = $stake->invested_at->addDays($stake->lock_period);
                                                        $daysLeft = now()->diffInDays($lockEndDate, false);
                                                    @endphp
                                                    @if($daysLeft > 0)
                                                        <small class="text-warning">{{ $daysLeft }} days left</small>
                                                    @else
                                                        <small class="text-success">Lock period ended</small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if ($stake->status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($stake->status === 'completed')
                                                    <span class="badge bg-primary">Completed</span>
                                                @elseif($stake->status === 'cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($stake->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="profit-breakdown">
                                                    <div class="d-flex justify-content-between">
                                                        <small>Total:</small>
                                                        <small
                                                            class="fw-bold text-success">${{ number_format($stake->totalProfit(), 2) }}</small>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <small>Credited:</small>
                                                        <small
                                                            class="text-primary">${{ number_format($stake->creditedProfit(), 2) }}</small>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <small>Pending:</small>
                                                        <small
                                                            class="text-warning">${{ number_format($stake->pendingProfit(), 2) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary view-details"
                                                        data-bs-toggle="tooltip" title="View Details"
                                                        data-stake-id="{{ $stake->id }}"
                                                        data-stake-data='@json($stake)'>
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info view-profits"
                                                        data-bs-toggle="tooltip" title="View Profits"
                                                        data-stake-id="{{ $stake->id }}"
                                                        data-stake-profits='@json($stake->profits)'>
                                                        <i class="bi bi-graph-up"></i>
                                                    </button>
                                                </div>
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

    <!-- Stake Details Modal -->
    <div class="modal fade" id="stakeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Stake Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="stakeDetails">
                    <!-- Details will be loaded here via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Profits Modal -->
    <div class="modal fade" id="profitsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Profit History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="profitsDetails">
                    <!-- Profits will be loaded here via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <style>
        .plan-badge {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .network-badge {
            background-color: #e9ecef;
            color: #495057;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .profit-breakdown {
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 0.375rem;
            border-left: 3px solid #28a745;
        }

        .info-card .card-icon {
            font-size: 1.5rem;
        }

        .revenue-card .card-icon {
            background-color: #e7f1ff;
            color: #2962ff;
        }

        .sales-card .card-icon {
            background-color: #e6f4ea;
            color: #34a853;
        }

        .customers-card .card-icon {
            background-color: #fef7e0;
            color: #f9ab00;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .view-details,
        .view-profits,
        .redeem-stake {
            transition: all 0.2s ease;
        }

        .view-details:hover,
        .view-profits:hover,
        .redeem-stake:hover {
            transform: scale(1.1);
        }

        .btn-group .btn {
            margin: 0 2px;
        }
    </style>

   <script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Filter functionality
        $('#statusFilter, #planFilter, #networkFilter').on('change', function() {
            filterTable();
        });

        $('#resetFilters').on('click', function() {
            $('#statusFilter, #planFilter, #networkFilter').val('');
            filterTable();
        });

        function filterTable() {
            const statusValue = $('#statusFilter').val();
            const planValue = $('#planFilter').val();
            const networkValue = $('#networkFilter').val();

            $('tbody tr').each(function() {
                const rowStatus = $(this).data('status');
                const rowPlan = $(this).data('plan');
                const rowNetwork = $(this).data('network');

                const statusMatch = !statusValue || rowStatus === statusValue;
                const planMatch = !planValue || rowPlan === planValue;
                const networkMatch = !networkValue || rowNetwork === networkValue;

                if (statusMatch && planMatch && networkMatch) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // View stake details with real data
        const stakeModal = new bootstrap.Modal('#stakeModal');
        $('.view-details').on('click', function() {
            const stakeData = $(this).data('stake-data'); // jQuery automatically parses JSON

            // Format dates
            const investedDate = stakeData.invested_at ? new Date(stakeData.invested_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }) : 'Not set';

            const redeemedDate = stakeData.redeemed_at ? new Date(stakeData.redeemed_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }) : 'Not redeemed';

            const createdDate = new Date(stakeData.created_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // Calculate lock period info
            let lockPeriodInfo = '';
            if (stakeData.invested_at) {
                const lockEndDate = new Date(stakeData.invested_at);
                lockEndDate.setDate(lockEndDate.getDate() + (stakeData.lock_period || 30));
                const daysLeft = Math.ceil((lockEndDate - new Date()) / (1000 * 60 * 60 * 24));

                if (daysLeft > 0) {
                    lockPeriodInfo = `<small class="text-warning">${daysLeft} days left</small>`;
                } else {
                    lockPeriodInfo = `<small class="text-success">Lock period ended</small>`;
                }
            }

            $('#stakeDetails').html(`
                <div class="row">
                    <div class="col-md-6">
                        <strong>Stake ID:</strong>
                        <p class="text-muted">${stakeData.id}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Created Date:</strong>
                        <p class="text-muted">${createdDate}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Investment Plan:</strong>
                        <p class="text-muted">${stakeData.investment_plan ? stakeData.investment_plan.name : 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Network:</strong>
                        <p class="text-muted">${stakeData.network ? stakeData.network.toUpperCase() : 'N/A'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Investment Amount:</strong>
                        <p class="fw-bold text-primary">$${parseFloat(stakeData.amount).toFixed(2)}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Lock Period:</strong>
                        <p class="text-muted">${stakeData.lock_period} days</p>
                        ${lockPeriodInfo}
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Investment Date:</strong>
                        <p class="text-muted">${investedDate}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Redemption Date:</strong>
                        <p class="text-muted">${redeemedDate}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p><span class="badge ${getStatusBadgeClass(stakeData.status)}">${stakeData.status.charAt(0).toUpperCase() + stakeData.status.slice(1)}</span></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Can Be Redeemed:</strong>
                        <p class="text-muted">${stakeData.can_be_redeemed ? 'Yes' : 'No'}</p>
                    </div>
                </div>

            `);

            stakeModal.show();
        });

        // View profits with real data
        const profitsModal = new bootstrap.Modal('#profitsModal');
        $('.view-profits').on('click', function() {
            const stakeId = $(this).data('stake-id');
            const profits = $(this).data('stake-profits'); // jQuery automatically parses JSON
            const stakeData = $(this).closest('tr').find('.view-details').data('stake-data');

            let profitsHtml = '';
            let totalProfit = 0;
            let creditedProfit = 0;
            let pendingProfit = 0;

            if (profits && profits.length > 0) {
                profits.forEach(profit => {
                    const profitDate = profit.profit_date ? new Date(profit.profit_date).toLocaleDateString('en-US') : 'N/A';
                    const creditedDate = profit.profit_date ? new Date(profit.profit_date).toLocaleDateString('en-US') : '-';
                    const profitAmount = parseFloat(profit.profit_amount || 0);

                    totalProfit += profitAmount;
                    if (profit.credited) {
                        creditedProfit += profitAmount;
                    } else {
                        pendingProfit += profitAmount;
                    }

                    profitsHtml += `
                        <tr>
                            <td>${profitDate}</td>
                            <td class="text-success">+$${profitAmount.toFixed(2)}</td>
                            <td>
                                <span class="badge ${profit.credited ? 'bg-success' : 'bg-warning'}">
                                    ${profit.credited ? 'Credited' : 'Pending'}
                                </span>
                            </td>
                            <td>${creditedDate}</td>
                        </tr>
                    `;
                });
            } else {
                profitsHtml = `
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">No profit records found</p>
                        </td>
                    </tr>
                `;
            }

            $('#profitsDetails').html(`
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6>Profit History for Stake #${stakeId}</h6>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success">Total: $${totalProfit.toFixed(2)}</span>
                        <span class="badge bg-primary">Credited: $${creditedProfit.toFixed(2)}</span>
                        <span class="badge bg-warning">Pending: $${pendingProfit.toFixed(2)}</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Profit Amount</th>
                                <th>Status</th>
                                <th>Credited Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${profitsHtml}
                        </tbody>
                    </table>
                </div>
                ${profits && profits.length > 0 ? `
                <div class="mt-3 p-3 bg-light rounded">
                    <div class="row text-center">
                        <div class="col-4">
                            <h6 class="text-success">$${totalProfit.toFixed(2)}</h6>
                            <small>Total Profit</small>
                        </div>
                        <div class="col-4">
                            <h6 class="text-primary">$${creditedProfit.toFixed(2)}</h6>
                            <small>Credited Profit</small>
                        </div>
                        <div class="col-4">
                            <h6 class="text-warning">$${pendingProfit.toFixed(2)}</h6>
                            <small>Pending Profit</small>
                        </div>
                    </div>
                </div>
                ` : ''}
            `);

            profitsModal.show();
        });

        // Helper function for status badge classes
        function getStatusBadgeClass(status) {
            if (!status) return 'bg-secondary';

            switch(status.toLowerCase()) {
                case 'active': return 'bg-success';
                case 'completed': return 'bg-primary';
                case 'cancelled': return 'bg-danger';
                default: return 'bg-secondary';
            }
        }
    });
</script>
@endsection
