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
        <h1>Change Password</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">User</li>
                <li class="breadcrumb-item active">Password</li>
            </ol>
        </nav>
    </div>

    <section class="section profile">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form class="row g-3" method="POST" action="{{ route('users.changePassword') }}">
                            @csrf
                            <x-alerts />


                            <div class="col-md-12">
                                <label for="inputEmail5" class="form-label">Old Password</label>
                                <input type="password" name="old_password"
                                    class="form-control @error('old_password') is-invalid @enderror" id="inputEmail5"
                                    value="{{ old('old_password') }}" required>
                                @error('old_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="inputPhone" class="form-label">New Password</label>
                                <input type="password" name="new_password"
                                    class="form-control @error('new_password') is-invalid @enderror" id="inputPhone"
                                    value="{{ old('new_password') }}">
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="inputPassword" class="form-label">Comfirm Password</label>
                                <input type="password" name="confirm_password"
                                    class="form-control @error('confirm_password') is-invalid @enderror" id="inputPassword">
                                @error('confirm_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
