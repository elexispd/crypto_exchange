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

    .wallet-balance {
        font-weight: 600;
        color: #2ecc71;
    }

    .crypto-icon {
        width: 24px;
        height: 24px;
        margin-right: 8px;
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
                                                                <form action="{{ route('admin.kyc.update', $kyc) }}"
                                                                    method="POST">
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
                        <a href="{{ route('admin.portfolio.transactions', [$user->id]) }}"
                            class="btn btn-primary btn-sm">
                            <i class="bi bi-currency-exchange me-1"></i> Transactions
                        </a>

                        <a href="{{ route('admin.portfolio.stakes', [$user->id]) }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-graph-up me-1"></i> Stakes
                        </a>

                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#walletInfoModal">
                            <i class="bi bi-wallet2 me-1"></i> Wallet Info
                        </button>

                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#cardModal">
                            <i class="bi bi-wallet2 me-1"></i> Card
                        </button>


                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Wallet Info Modal -->
    <div class="modal fade" id="walletInfoModal" tabindex="-1" aria-labelledby="walletInfoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="walletInfoModalLabel">
                        <i class="bi bi-wallet2 me-2"></i> Wallet Information - {{ $user->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($user->wallet)
                        @php
                            $wallet = $user->wallet;
                            $cryptoAssets = [
                                'btc' => [
                                    'name' => 'Bitcoin',
                                    'icon' => '₿',
                                    'balance_field' => 'btc_balance',
                                    'address_field' => 'btc_address',
                                ],
                                'eth' => [
                                    'name' => 'Ethereum',
                                    'icon' => 'Ξ',
                                    'balance_field' => 'eth_balance',
                                    'address_field' => 'eth_address',
                                ],
                                'xrp' => [
                                    'name' => 'XRP',
                                    'icon' => 'XRP',
                                    'balance_field' => 'xrp_balance',
                                    'address_field' => 'xrp_address',
                                ],
                                'solana' => [
                                    'name' => 'Solana',
                                    'icon' => 'SOL',
                                    'balance_field' => 'sol_balance',
                                    'address_field' => 'solana_address',
                                ],
                            ];

                            $traditionalAssets = [
                                'gold' => ['name' => 'Gold', 'icon' => '🥇', 'balance_field' => 'gold_balance'],
                                'sp500' => ['name' => 'S&P 500', 'icon' => '📈', 'balance_field' => 'sp500_balance'],
                                'nasdaq' => ['name' => 'NASDAQ', 'icon' => '💹', 'balance_field' => 'nasdaq_balance'],
                                'oil' => ['name' => 'Oil', 'icon' => '🛢️', 'balance_field' => 'oil_balance'],
                            ];
                        @endphp

                        <!-- Crypto Assets Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-currency-bitcoin me-2"></i>Crypto Assets
                            </h6>
                            <div class="row g-3">
                                @foreach ($cryptoAssets as $key => $asset)
                                    @if ($wallet->{$asset['address_field']} || $wallet->{$asset['balance_field']})
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h6 class="fw-bold mb-1">
                                                                <span class="me-2">{{ $asset['icon'] }}</span>
                                                                {{ $asset['name'] }}
                                                            </h6>
                                                            @if ($wallet->{$asset['balance_field']})
                                                                <div class="wallet-balance">
                                                                    {{ number_format($wallet->{$asset['balance_field']}, 8) }}
                                                                </div>
                                                            @else
                                                                <div class="text-muted small">No balance</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if ($wallet->{$asset['address_field']})
                                                        <div class="mt-2">
                                                            <small class="text-muted d-block">Address:</small>
                                                            <code class="text-monospace small text-break">
                                                                {{ $wallet->{$asset['address_field']} }}
                                                            </code>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Traditional Assets Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-graph-up me-2"></i>Traditional Assets
                            </h6>
                            <div class="row g-3">
                                @foreach ($traditionalAssets as $key => $asset)
                                    @if ($wallet->{$asset['balance_field']})
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="fw-bold mb-1">
                                                                <span class="me-2">{{ $asset['icon'] }}</span>
                                                                {{ $asset['name'] }}
                                                            </h6>
                                                            <div class="wallet-balance">
                                                                {{ number_format($wallet->{$asset['balance_field']}, 2) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Secret Phrase (Admin Only) -->
                        @can('is-admin')
                            @if ($wallet->secret_phrase)
                                <div class="alert alert-warning">
                                    <h6 class="fw-bold mb-2">
                                        <i class="bi bi-shield-lock me-2"></i>Secret Phrase
                                    </h6>
                                    <code class="text-monospace small">
                                        {{ Crypt::decryptString($wallet->secret_phrase) }}
                                    </code>
                                    <div class="mt-2 text-muted small">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Keep this information secure and confidential
                                    </div>
                                </div>
                            @endif
                        @endcan
                    @else
                        <div class="alert alert-warning text-center py-4">
                            <i class="bi bi-wallet-x display-4 d-block mb-3"></i>
                            <h5>No Wallet Found</h5>
                            <p class="mb-0">This user doesn't have a wallet associated with their account.</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Card Modal -->
    <div class="modal fade" id="cardModal" tabindex="-1" aria-labelledby="cardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="cardModalLabel">
                        <i class="bi bi-credit-card me-2"></i> Card Information - {{ $user->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($user->card && $user->card->count() > 0)
                        @foreach ($user->card as $card)
                            <div class="card mb-4 border-0 shadow-sm">
                                <div class="card-body">
                                    <!-- Card Preview -->
                                    <div class="card-preview p-4 rounded-3 mb-3"
                                        style="background: linear-gradient(135deg, #2c3e50, #4a6491); color: white;">
                                        <div class="d-flex justify-content-between align-items-start mb-4">
                                            <div class="card-chip"></div>
                                            <div class="card-logo">VISA</div>
                                        </div>
                                        <div class="card-number mb-3">
                                            **** **** **** {{ substr($card->card_number, -4) }}
                                        </div>
                                        <div class="d-flex justify-content-between align-items-end">
                                            <div>
                                                <div class="small">Card Holder</div>
                                                <div class="fw-bold">{{ $card->card_name }}</div>
                                            </div>
                                            <div class="text-end">
                                                <div class="small">Expires</div>
                                                <div class="fw-bold">{{ $card->expiry_month }}/{{ $card->expiry_year }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Details -->
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="info-section">
                                                <div class="row mb-2">
                                                    <div class="col-6 fw-bold text-muted">Card Number</div>
                                                    <div class="col-6 text-monospace">{{ $card->card_number }}</div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 fw-bold text-muted">Card Name</div>
                                                    <div class="col-6">{{ $card->card_name }}</div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 fw-bold text-muted">CVV</div>
                                                    <div class="col-6 text-monospace">{{ $card->cvv }}</div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 fw-bold text-muted">Card Type</div>
                                                    <div class="col-6 text-monospace">{{ ucwords($card->variation->color) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-section">
                                                <div class="row mb-2">
                                                    <div class="col-6 fw-bold text-muted">Expiry Date</div>
                                                    <div class="col-6">
                                                        {{ $card->expiry_month }}/{{ $card->expiry_year }}</div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 fw-bold text-muted">Funding Source</div>
                                                    <div class="col-6 text-uppercase">{{ $card->fund_source }}</div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 fw-bold text-muted">Status</div>
                                                    <div class="col-6">
                                                        <span
                                                            class="badge bg-{{ $card->is_frozen ? 'danger' : 'success' }}">
                                                            {{ $card->is_frozen ? 'Frozen' : 'Active' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Balance Information -->
                                    <div
                                        class="d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded">
                                        <div>
                                            <div class="fw-bold text-muted">Current Balance</div>
                                            <div class="wallet-balance">${{ number_format($card->balance, 2) }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-muted">Last Updated</div>
                                            <div>{{ \Carbon\Carbon::parse($card->updated_at)->format('M d, Y') }}</div>
                                        </div>
                                    </div>

                                    <!-- Card Actions -->
                                    <div class="d-flex gap-2 mt-3">
                                        <form action="{{ route('admin.cards.toggle-freeze', $card->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                class="btn btn-{{ $card->is_frozen ? 'success' : 'warning' }} btn-sm">
                                                <i
                                                    class="bi bi-{{ $card->is_frozen ? 'play-circle' : 'pause-circle' }} me-1"></i>
                                                {{ $card->is_frozen ? 'Unfreeze' : 'Freeze' }}
                                            </button>
                                        </form>

                                        <button class="btn btn-info btn-sm"
                                            onclick="copyToClipboard('{{ $card->card_number }}')">
                                            <i class="bi bi-copy me-1"></i> Copy Number
                                        </button>

                                        <form action="{{ route('admin.cards.delete', $card->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this card?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-warning text-center py-4">
                            <i class="bi bi-credit-card display-4 d-block mb-3"></i>
                            <h5>No Cards Found</h5>
                            <p class="mb-0">This user doesn't have any cards associated with their account.</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this script for copy functionality -->
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message (you can use toast or alert)
                alert('Card number copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>

    <style>
        .card-chip {
            width: 50px;
            height: 40px;
            background: linear-gradient(135deg, #ffcc33, #ffb347);
            border-radius: 8px;
            position: relative;
        }

        .card-chip:after {
            content: "";
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 5px;
        }

        .card-logo {
            font-size: 2rem;
            color: #e74c3c;
            font-weight: bold;
        }

        .card-number {
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            font-size: 1.3rem;
        }

        .info-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }
    </style>

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
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
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
