@extends('layouts.portal')
@section('content')
    <div class="pagetitle">
        <h1>Service Fees</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Transaction</li>
                <li class="breadcrumb-item active">Fees</li>
            </ol>
        </nav>
    </div>

    <section class="section profile">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add/Update Transaction Fee</h5>
                        <form class="row g-3 py-2" method="POST" action="{{ route('admin.transaction.storeFee') }}">
                            @csrf
                            <x-alerts />

                            <div class="col-md-4">
                                <label for="type" class="form-label">Transaction Type</label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="Deposit">Deposit</option>
                                    <option value="Swap">Swap</option>
                                    <option value="Withdrawal">Withdrawal</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="network" class="form-label">Network</label>
                                <select name="network" id="network" class="form-select @error('network') is-invalid @enderror" required>
                                    <option value="">Select Network</option>
                                    @foreach($networks as $key => $value)
                                        <option value="{{ $key }}">{{ ucfirst($value) }}</option>
                                    @endforeach
                                </select>
                                @error('network')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="amount" class="form-label">Fee Amount</label>
                                <input type="number" name="amount" step="0.01" min="0.01"
                                    class="form-control @error('amount') is-invalid @enderror"
                                    id="amount" value="{{ old('amount') }}" required placeholder="0.00">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    Save Fee
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Existing Fees Table -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Existing Transaction Fees</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Type</th>
                                        <th>Network</th>
                                        <th>Fee Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($fees as $fee)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $fee->type }}</td>
                                        <td>{{ $fee->network }}</td>
                                        <td>{{ number_format($fee->amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $fee->status === 'active' ? 'success' : 'danger' }}">
                                                {{ $fee->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-fee"
                                                data-fee-id="{{ $fee->id }}"
                                                data-fee-type="{{ $fee->type }}"
                                                data-fee-network="{{ $fee->network }}"
                                                data-fee-amount="{{ $fee->amount }}">
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.transaction.destroyFee', $fee) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No transaction fees found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>

    // Edit fee functionality
    $('.edit-fee').on('click', function() {
        const feeId = $(this).data('fee-id');
        const feeType = $(this).data('fee-type');
        const feeNetwork = $(this).data('fee-network');
        const feeAmount = $(this).data('fee-amount');

        console.log(feeId, feeType, feeNetwork, feeAmount);

        // Populate form with existing data
        $('#type').val(feeType);
        $('#network').val(feeNetwork);
        $('#amount').val(feeAmount);

        // Add hidden field for update
        if (!$('#fee_id').length) {
            $('form').append('<input type="hidden" name="fee_id" id="fee_id" value="' + feeId + '">');
        } else {
            $('#fee_id').val(feeId);
        }

        // Scroll to form
        $('html, body').animate({
            scrollTop: $('form').offset().top - 100
        }, 500);
    });

    // Clear form for new entry
    $('form').on('reset', function() {
        $('#fee_id').remove();
    });

    // Network suggestions based on type
    $('#type').on('change', function() {
        const type = $(this).val();
        const networkSelect = $('#network');

        // You can customize networks based on type if needed
        if (type === 'Deposit') {
            // All networks available for deposits
        } else if (type === 'Withdrawal') {
            // Specific networks for withdrawals
        }
    });

</script>

@endsection



