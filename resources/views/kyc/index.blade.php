@extends('layouts.portal')

@section('content')
    <div class="pagetitle">
        <h1>KYC List</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Users</li>
                <li class="breadcrumb-item active">Kyc</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"> {{ $status }} KYC Listing</h5>
                        <!-- Table with stripped rows -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>
                                        <b>N</b>ame
                                    </th>
                                    <th>Document Type</th>
                                    <th>Status</th>
                                    <th>Submitted At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kycs as $kyc)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>{{ $kyc->user->name }}</td>
                                        <td>{{ ucfirst($kyc->document_type) }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $kyc->status === 'verified' ? 'success' : ($kyc->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($kyc->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $kyc->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('users.show', $kyc->user_id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                View User
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No KYC records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- End Table with stripped rows -->

                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
