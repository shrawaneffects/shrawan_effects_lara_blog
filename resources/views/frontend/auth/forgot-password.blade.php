@extends('layouts.app')

@section('title', 'Forgot Password - ' . config('app.name', 'Laravel 12 Blog'))

@section('content')
    <div class="container py-5 my-3">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-lg p-4 p-md-5 rounded-4 bg-body hover-lift">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center text-white rounded-circle mb-3 shadow" style="width: 60px; height: 60px; background: var(--gradient-primary);">
                            <i class="bi bi-key-fill fs-3"></i>
                        </div>
                        <h3 class="fw-bold"><span class="text-gradient">Forgot Password?</span></h3>
                        <p class="text-body-secondary small">Enter your registered email and we'll send you instructions to reset your password.</p>
                    </div>

                    @if(session('status'))
                        <div class="alert alert-success border-0 rounded-4 p-3 mb-4 small d-flex align-items-center shadow-xs">
                            <i class="bi bi-check-circle-fill text-success fs-5 me-2 flex-shrink-0"></i>
                            <div>{{ session('status') }}</div>
                        </div>
                    @endif

                    <form action="{{ route('password.email') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-semibold small" for="resetEmail">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary rounded-start-pill ps-3"><i class="bi bi-envelope text-primary"></i></span>
                                <input type="email" name="email" id="resetEmail" class="form-control rounded-end-pill @error('email') is-invalid @enderror" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block small ms-2 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-gradient w-100 py-2.5 fw-semibold rounded-pill shadow-sm">
                            <i class="bi bi-send-fill me-2"></i> Send Password Reset Link
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top small text-body-secondary">
                        Remember your password? <a href="{{ route('login') }}" class="text-gradient fw-bold text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Back to Sign In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

