@extends('layouts.app')

@section('title', 'Register - ' . config('app.name', 'Laravel 12 Blog'))

@section('content')
    <div class="container py-5 my-3">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-lg p-4 p-md-5 rounded-4 bg-body hover-lift" style="border: 1px solid rgba(0,255,102,0.2) !important;">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center text-white rounded-circle mb-3 shadow" style="width: 60px; height: 60px; background: var(--gradient-primary); box-shadow: 0 0 20px rgba(0,255,102,0.4) !important;">
                            <i class="bi bi-person-plus-fill fs-3 text-dark"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><span class="text-gradient font-cyber">INITIATE ENROLLMENT</span></h3>
                        <p class="text-body-secondary small mb-2 font-mono">Register new identity on the neural datastream</p>
                        <span class="badge rounded-pill px-2.5 py-1 font-mono" style="font-size: 0.72rem; background: rgba(33,54,8,0.5); border: 1px solid rgba(0,255,102,0.3); color: #00ff66;">
                            <i class="bi bi-envelope-check me-1"></i> OTP VALIDATION ENFORCED
                        </span>
                    </div>

                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary rounded-start-pill ps-3"><i class="bi bi-person text-primary"></i></span>
                                <input type="text" name="name" class="form-control rounded-end-pill" placeholder="Jane Doe" value="{{ old('name') }}" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary rounded-start-pill ps-3"><i class="bi bi-envelope text-primary"></i></span>
                                <input type="email" name="email" class="form-control rounded-end-pill" placeholder="jane@example.com" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Password (Min 8 characters)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary rounded-start-pill ps-3"><i class="bi bi-lock text-primary"></i></span>
                                <input type="password" name="password" class="form-control rounded-end-pill" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary rounded-start-pill ps-3"><i class="bi bi-lock-fill text-primary"></i></span>
                                <input type="password" name="password_confirmation" class="form-control rounded-end-pill" placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-gradient w-100 py-2 fw-semibold rounded-pill shadow-sm">
                            <i class="bi bi-check2-circle me-2"></i> Register Account
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top small text-body-secondary">
                        Already have an account? <a href="{{ route('login') }}" class="text-gradient fw-bold text-decoration-none">Sign In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
