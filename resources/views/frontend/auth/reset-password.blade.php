@extends('layouts.app')

@section('title', 'Set New Password - ' . config('app.name', 'Laravel 12 Blog'))

@section('content')
    <div class="container py-5 my-3">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-lg p-4 p-md-5 rounded-4 bg-body hover-lift">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center text-white rounded-circle mb-3 shadow" style="width: 60px; height: 60px; background: var(--gradient-primary);">
                            <i class="bi bi-shield-check fs-3"></i>
                        </div>
                        <h3 class="fw-bold"><span class="text-gradient">Set New Password</span></h3>
                        <p class="text-body-secondary small">Please enter your new password to securely access your account.</p>
                    </div>

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary rounded-start-pill ps-3"><i class="bi bi-envelope text-primary"></i></span>
                                <input type="email" name="email" class="form-control rounded-end-pill @error('email') is-invalid @enderror" placeholder="name@example.com" value="{{ old('email', $email) }}" required readonly>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block small ms-2 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary rounded-start-pill ps-3"><i class="bi bi-lock text-primary"></i></span>
                                <input type="password" name="password" class="form-control rounded-end-pill @error('password') is-invalid @enderror" placeholder="At least 8 characters" required autofocus>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block small ms-2 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary rounded-start-pill ps-3"><i class="bi bi-lock-fill text-primary"></i></span>
                                <input type="password" name="password_confirmation" class="form-control rounded-end-pill" placeholder="Repeat your new password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-gradient w-100 py-2.5 fw-semibold rounded-pill shadow-sm">
                            <i class="bi bi-check2-circle me-2"></i> Update Password & Sign In
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top small text-body-secondary">
                        <a href="{{ route('login') }}" class="text-gradient fw-bold text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Back to Sign In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

