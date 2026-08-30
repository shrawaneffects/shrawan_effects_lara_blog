@extends('layouts.app')

@section('title', 'Sign In - ' . config('app.name', 'Laravel 12 Blog'))

@section('content')
    <div class="container py-5 my-3">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-lg p-4 p-md-5 rounded-4 bg-body hover-lift">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center text-white rounded-circle mb-3 shadow" style="width: 60px; height: 60px; background: var(--gradient-primary);">
                            <i class="bi bi-shield-lock-fill fs-3"></i>
                        </div>
                        <h3 class="fw-bold"><span class="text-gradient">Welcome Back</span></h3>
                        <p class="text-body-secondary small">Access your account or administrative portal</p>
                    </div>

                    <!-- Quick Demo Credentials Helper -->
                    <div class="alert alert-info border-0 rounded-4 p-3 mb-4 small bg-primary-subtle">
                        <div class="fw-bold mb-1"><i class="bi bi-info-circle me-1 text-primary"></i> Demo Credentials (Click to auto-fill):</div>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <button type="button" class="btn btn-primary btn-sm py-1 px-3 rounded-pill" onclick="fillCredentials('admin@blog.com', 'password')">Admin</button>
                            <button type="button" class="btn btn-outline-primary btn-sm py-1 px-3 rounded-pill" onclick="fillCredentials('author@blog.com', 'password')">Author</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-3 rounded-pill" onclick="fillCredentials('user@blog.com', 'password')">User</button>
                        </div>
                    </div>

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary rounded-start-pill ps-3"><i class="bi bi-envelope text-primary"></i></span>
                                <input type="email" name="email" id="loginEmail" class="form-control rounded-end-pill" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary rounded-start-pill ps-3"><i class="bi bi-lock text-primary"></i></span>
                                <input type="password" name="password" id="loginPassword" class="form-control rounded-end-pill" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                                <label class="form-check-label small" for="rememberMe">Remember me</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-gradient w-100 py-2 fw-semibold rounded-pill shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top small text-body-secondary">
                        Don't have an account? <a href="{{ route('register') }}" class="text-gradient fw-bold text-decoration-none">Create Account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function fillCredentials(email, password) {
            document.getElementById('loginEmail').value = email;
            document.getElementById('loginPassword').value = password;
        }
    </script>
    @endpush
@endsection
