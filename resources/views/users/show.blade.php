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
        <h1>User Information</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Users</li>
                <li class="breadcrumb-item active">Data</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->


    <section class="section profile">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">
                            <h4 class="fw-bold">User Profile</h4>
                        </div>
                        <div>

                            @can('is-admin')
                                {{-- <a class="btn  btn-sm" href="#" style="background: #191970; color: #fff;">
                                <i class="fas fa-money-check-alt"></i> Debit
                            </a>
                            <a class="btn  btn-sm" href="#" style="background: #191970; color: #fff;">
                                <i class="fas fa-money-check-alt"></i> Credit </a> --}}
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal">
                                    <i class="bi bi-pen me-1"></i> Edit
                                </button>

                                <form action="{{ route('users.changeStatus', $user) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status"
                                        value="{{ $user->status === 'active' ? 'inactive' : 'active' }}">
                                    <button type="submit"
                                        class="btn btn-{{ $user->status === 'active' ? 'danger' : 'success' }} btn-sm">
                                        <i
                                            class="bi bi-{{ $user->status === 'active' ? 'exclamation-octagon' : 'check-circle' }} mr-1"></i>
                                        {{ $user->status === 'active' ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <x-alerts />
                        <div class="row">
                            <!-- Personal Information Column -->
                            <div class="col-md-6">
                                <div class="info-section mb-4">
                                    <h5 class="section-title fw-bold text-primary mb-3">
                                        <i class="fas fa-user-circle mr-2"></i>Personal Information
                                    </h5>
                                    <div class="info-item row mb-2">
                                        <div class="col-sm-4 fw-bold">Full Name:</div>
                                        <div class="col-sm-8">{{ $user->name }}</div>
                                    </div>
                                    <div class="info-item row mb-2">
                                        <div class="col-sm-4 fw-bold">Email:</div>
                                        <div class="col-sm-8">{{ $user->email }}</div>
                                    </div>
                                    <div class="info-item row mb-2">
                                        <div class="col-sm-4 fw-bold">Phone:</div>
                                        <div class="col-sm-8">{{ $user->phone ?? 'N/A' }}</div>
                                    </div>
                                    <div class="info-item row mb-2">
                                        <div class="col-sm-4 fw-bold">Location:</div>
                                        <div class="col-sm-8">
                                            {{ $user->state ? ucwords($user->state) . ', ' : '' }}{{ $user->country ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Account Information -->
                                <div class="info-section mb-4">
                                    <h5 class="section-title fw-bold text-primary mb-3">
                                        <i class="fas fa-wallet mr-2"></i>Account Information
                                    </h5>
                                    <div class="info-item row mb-2">
                                        <div class="col-sm-4 fw-bold">Status:</div>
                                        <div class="col-sm-8">
                                            <span
                                                class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="info-item row mb-2">
                                        <div class="col-sm-4 fw-bold">Joined Date:</div>
                                        <div class="col-sm-8">{{ $user->created_at->format('M d, Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Wallet & Referral Information Column -->
                            <div class="col-md-6">
                                <!-- Wallet Addresses -->
                                <div class="info-section mb-4">
                                    <h5 class="section-title fw-bold text-primary mb-3">
                                        <i class="fas fa-coins mr-2"></i>Wallet Addresses
                                    </h5>
                                    @if ($user->wallet)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Currency</th>
                                                        <th>Address</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $wallet = $user->wallet;
                                                        $currencies = [
                                                            'btc' => 'Bitcoin',
                                                            'eth' => 'Ethereum',
                                                            'xrp' => 'XRP',
                                                            'solana' => 'Solana',
                                                        ];
                                                    @endphp

                                                    @foreach ($currencies as $key => $name)
                                                        @php
                                                            $field = $key . '_address';
                                                            $address = $wallet->$field ?? null;
                                                        @endphp

                                                        @if ($address)
                                                            <tr>
                                                                <td class="text-uppercase text-monospace small">
                                                                    {{ $name }}</td>
                                                                <td class="text-monospace small">{{ $address }}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-warning py-2">
                                            <i class="bi bi-exclamation-triangle me-1"></i> No wallet addresses found
                                        </div>
                                    @endif

                                </div>

                                <div class="info-section mb-4">
                                    <h5 class="section-title fw-bold text-primary mb-3">
                                        <i class="fas fa-id-card me-2"></i>KYC Documents
                                    </h5>

                                    @if ($user->latestKyc)
                                        @php
                                            $kyc = $user->latestKyc;
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'verified' => 'success',
                                                'rejected' => 'danger',
                                            ];
                                            $status = strtolower($kyc->status ?? 'pending');
                                            $frontExtension = pathinfo($kyc->front_image, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($frontExtension), [
                                                'jpg',
                                                'jpeg',
                                                'png',
                                                'gif',
                                                'webp',
                                            ]);
                                        @endphp

                                        <div class="card shadow-sm border-0">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="fw-bold mb-0 text-primary">
                                                        <i class="bi bi-person-vcard me-2"></i>
                                                        {{ ucfirst($kyc->document_type) }}
                                                    </h6>
                                                    <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">
                                                        {{ ucfirst($status) }}
                                                    </span>

                                                    @can('is-admin')
                                                        @if (strtolower($status) == 'pending')
                                                            <div class="d-flex gap-2">
                                                                {{-- ✅ Approve Button --}}
                                                                <form action="{{ route('admin.kyc.update', $kyc) }}" method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="action" value="approve">
                                                                    <button class="btn btn-outline-success">
                                                                        <i class="bi bi-check-circle me-1"></i> Approve
                                                                    </button>
                                                                </form>

                                                                {{-- ❌ Reject Button (opens modal) --}}
                                                                <button type="button" class="btn btn-outline-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#rejectModal{{ $kyc->id }}">
                                                                    <i class="bi bi-x-circle me-1"></i> Reject
                                                                </button>
                                                            </div>
                                                        @endif
                                                    @endcan

                                                </div>

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="border rounded p-2 text-center bg-light">
                                                            <h6 class="fw-bold mb-2">Front Image</h6>
                                                            @if ($isImage)
                                                                <img src="{{ asset('storage/' . $kyc->front_image) }}"
                                                                    alt="Front Document" class="img-fluid rounded shadow-sm"
                                                                    style="max-height: 180px; object-fit: cover;">
                                                            @else
                                                                <a href="{{ asset('storage/' . $kyc->front_image) }}"
                                                                    target="_blank"
                                                                    class="btn btn-outline-secondary btn-sm">
                                                                    <i class="bi bi-file-earmark-pdf me-1"></i> View PDF
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if ($kyc->back_image)
                                                        <div class="col-md-6">
                                                            <div class="border rounded p-2 text-center bg-light">
                                                                <h6 class="fw-bold mb-2">Back Image</h6>
                                                                @php
                                                                    $backExt = pathinfo(
                                                                        $kyc->back_image,
                                                                        PATHINFO_EXTENSION,
                                                                    );
                                                                    $backIsImg = in_array(strtolower($backExt), [
                                                                        'jpg',
                                                                        'jpeg',
                                                                        'png',
                                                                        'gif',
                                                                        'webp',
                                                                    ]);
                                                                @endphp
                                                                @if ($backIsImg)
                                                                    <img src="{{ asset('storage/' . $kyc->back_image) }}"
                                                                        alt="Back Document"
                                                                        class="img-fluid rounded shadow-sm"
                                                                        style="max-height: 180px; object-fit: cover;">
                                                                @else
                                                                    <a href="{{ asset('storage/' . $kyc->back_image) }}"
                                                                        target="_blank"
                                                                        class="btn btn-outline-secondary btn-sm">
                                                                        <i class="bi bi-file-earmark-pdf me-1"></i> View
                                                                        PDF
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($kyc->selfie_image)
                                                        <div class="col-md-6">
                                                            <div class="border rounded p-2 text-center bg-light">
                                                                <h6 class="fw-bold mb-2">Selfie Image</h6>
                                                                <img src="{{ asset('storage/' . $kyc->selfie_image) }}"
                                                                    alt="Selfie" class="img-fluid rounded shadow-sm"
                                                                    style="max-height: 180px; object-fit: cover;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                @if ($kyc->rejection_reason)
                                                    <div class="alert alert-danger mt-3 mb-0">
                                                        <i class="bi bi-exclamation-octagon me-2"></i>
                                                        <strong>Rejection Reason:</strong> {{ $kyc->rejection_reason }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning py-2">
                                            <i class="bi bi-exclamation-triangle me-1"></i> No KYC found
                                        </div>
                                    @endif
                                </div>



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editUserModalLabel">
                            <i class="bi bx-edit me-2"></i> Edit User
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" id="name"
                                value="{{ $user->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" id="email"
                                value="{{ $user->email }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label fw-bold">Phone</label>
                            <input type="text" name="phone" class="form-control" id="phone"
                                value="{{ $user->phone }}">
                        </div>

                        <div class="mb-3">
                            <label for="country" class="form-label fw-bold">state</label>
                            <input type="text" name="state" class="form-control" id="state"
                                value="{{ $user->state }}">
                        </div>

                        <div class="mb-3">
                            <label for="country" class="form-label fw-bold">Country</label>
                            <input type="text" name="country" class="form-control" id="country"
                                value="{{ $user->country }}">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

 @if ($user->latestKyc)
     <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal{{ $kyc->id }}" tabindex="-1"
        aria-labelledby="rejectModalLabel{{ $kyc->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel{{ $kyc->id }}">
                        <i class="bi bi-x-circle me-2"></i> Reject KYC
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('admin.kyc.update', $kyc) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="reject">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reason{{ $kyc->id }}" class="form-label">Rejection Reason</label>
                            <textarea name="rejection_reason" id="reason{{ $kyc->id }}" class="form-control" rows="3" required
                                placeholder="Enter reason for rejection..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle me-1"></i> Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
 @endif





@endsection
