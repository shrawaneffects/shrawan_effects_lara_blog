@extends('layouts.app')

@section('title', 'Sign In - ' . config('app.name', 'Shrawan Effects'))

@section('content')
    <div class="container py-5 my-3">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <div class="card border-0 shadow-lg p-4 p-md-5 rounded-4 bg-body hover-lift auth-card">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center text-white rounded-circle mb-3 shadow auth-icon-wrap" style="width: 58px; height: 58px; background: var(--gradient-primary);">
                            <i class="bi bi-shield-lock-fill fs-3 text-white"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><span class="text-gradient font-cyber">SIGN IN TO PORTAL</span></h3>
                        <p class="text-body-secondary small mb-2 font-mono">Shrawan Effects Security Protocol</p>
                    </div>

                    @php
                        $activeTab = session('active_tab', (session('magic_link_sent') ? 'magic-link' : (session('otp_sent') ? 'email-otp' : 'password')));
                    @endphp

                    <!-- 3 Flexible Login Methods (Tabs) -->
                    <ul class="nav nav-pills nav-fill mb-4 p-1 rounded-pill border" id="authMethodTabs" role="tablist" style="background: rgba(33,54,8,0.12);">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill py-2 small fw-bold font-mono {{ $activeTab === 'password' ? 'active' : '' }}" id="password-tab" data-bs-toggle="pill" data-bs-target="#password-pane" type="button" role="tab" aria-controls="password-pane" aria-selected="{{ $activeTab === 'password' ? 'true' : 'false' }}">
                                <i class="bi bi-key me-1"></i> Password
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill py-2 small fw-bold font-mono {{ $activeTab === 'magic-link' ? 'active' : '' }}" id="magic-tab" data-bs-toggle="pill" data-bs-target="#magic-pane" type="button" role="tab" aria-controls="magic-pane" aria-selected="{{ $activeTab === 'magic-link' ? 'true' : 'false' }}">
                                <i class="bi bi-magic me-1"></i> Magic Link
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill py-2 small fw-bold font-mono {{ $activeTab === 'email-otp' ? 'active' : '' }}" id="otp-tab" data-bs-toggle="pill" data-bs-target="#otp-pane" type="button" role="tab" aria-controls="otp-pane" aria-selected="{{ $activeTab === 'email-otp' ? 'true' : 'false' }}">
                                <i class="bi bi-envelope-check me-1"></i> Email OTP
                            </button>
                        </li>
                    </ul>

                    @if(session('info'))
                        <div class="alert alert-info py-2 small font-mono mb-3 rounded-3">
                            <i class="bi bi-info-circle me-1"></i> {{ session('info') }}
                        </div>
                    @endif

                    @if(session('magic_link_sent'))
                        <div class="alert alert-success py-3 px-3 small font-mono mb-4 rounded-4 shadow-sm border-0" style="background: #f0fdf4; border-left: 4px solid #16a34a !important; color: #166534;">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill fs-5 text-success flex-shrink-0"></i>
                                <div>
                                    <strong class="d-block mb-1">Check Your Email!</strong>
                                    {{ session('magic_link_sent') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="tab-content" id="authMethodTabContent">
                        <!-- TAB 1: Password & Captcha Login -->
                        <div class="tab-pane fade {{ $activeTab === 'password' ? 'show active' : '' }}" id="password-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
                            <form action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill ps-3"><i class="bi bi-envelope text-success"></i></span>
                                        <input type="email" name="email" id="loginEmail" class="form-control rounded-end-pill" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                                    </div>
                                    @error('email')
                                        <div class="text-danger small mt-1 font-mono">
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill ps-3"><i class="bi bi-lock text-success"></i></span>
                                        <input type="password" name="password" id="loginPassword" class="form-control rounded-end-pill" placeholder="••••••••" required>
                                    </div>
                                    @error('password')
                                        <div class="text-danger small mt-1 font-mono">
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Security Math Captcha -->
                                @include('components.captcha')

                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                                        <label class="form-check-label small" for="rememberMe">Remember me</label>
                                    </div>
                                    <a href="{{ route('password.request') }}" class="small text-decoration-none text-success fw-semibold">
                                        Forgot Password?
                                    </a>
                                </div>

                                <button type="submit" class="btn btn-gradient w-100 py-2.5 fw-semibold rounded-pill shadow-sm">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Sign In with Password
                                </button>
                            </form>
                        </div>

                        <!-- TAB 2: Magic Link (Passwordless Email Login) -->
                        <div class="tab-pane fade {{ $activeTab === 'magic-link' ? 'show active' : '' }}" id="magic-pane" role="tabpanel" aria-labelledby="magic-tab" tabindex="0">
                            <p class="text-body-secondary small mb-3">
                                Enter your email and we will send you a passwordless single-use link that signs you in with one click.
                            </p>
                            <form action="{{ route('login.magic-link') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill ps-3"><i class="bi bi-magic text-success"></i></span>
                                        <input type="email" name="email" class="form-control rounded-end-pill" placeholder="name@example.com" value="{{ old('email', session('otp_email')) }}" required>
                                    </div>
                                </div>

                                <!-- Security Math Captcha -->
                                @include('components.captcha')

                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" name="remember" id="magicRemember" checked>
                                    <label class="form-check-label small" for="magicRemember">Remember this device</label>
                                </div>

                                <button type="submit" class="btn btn-gradient w-100 py-2.5 fw-semibold rounded-pill shadow-sm">
                                    <i class="bi bi-send-check-fill me-2"></i> Send Magic Sign-in Link
                                </button>
                            </form>
                        </div>

                        <!-- TAB 3: Email OTP Login -->
                        <div class="tab-pane fade {{ $activeTab === 'email-otp' ? 'show active' : '' }}" id="otp-pane" role="tabpanel" aria-labelledby="otp-tab" tabindex="0">
                            @if(session('otp_sent'))
                                <!-- Step 2: Enter 6-digit OTP -->
                                <form action="{{ route('login.email-otp.verify') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ session('otp_email', old('email')) }}">

                                    <div class="text-center mb-3">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 font-mono small mb-2">
                                            CODE DISPATCHED
                                        </span>
                                        <p class="small text-body-secondary mb-0">
                                            Enter the 6-digit code sent to <strong>{{ session('otp_email') }}</strong>:
                                        </p>
                                    </div>

                                    <div class="mb-3">
                                        <input type="text" name="code" class="form-control text-center font-mono fw-bold fs-3 tracking-widest rounded-4 py-2 border-2 @error('otp_code') is-invalid @enderror" placeholder="••••••" maxlength="6" autofocus required style="letter-spacing: 8px;">
                                        @error('otp_code')
                                            <div class="text-danger small mt-1 font-mono text-center">
                                                <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-gradient w-100 py-2.5 fw-semibold rounded-pill shadow-sm mb-3">
                                        <i class="bi bi-shield-check me-2"></i> Verify & Sign In
                                    </button>

                                    <div class="text-center">
                                        <a href="{{ route('login') }}" class="small text-decoration-none text-muted font-mono">
                                            <i class="bi bi-arrow-left me-1"></i> Change Email or Resend
                                        </a>
                                    </div>
                                </form>
                            @else
                                <!-- Step 1: Request OTP -->
                                <p class="text-body-secondary small mb-3">
                                    Receive a secure 6-digit OTP code directly in your email inbox to verify your identity.
                                </p>
                                <form action="{{ route('login.email-otp') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text rounded-start-pill ps-3"><i class="bi bi-envelope text-success"></i></span>
                                            <input type="email" name="email" class="form-control rounded-end-pill" placeholder="name@example.com" value="{{ old('email') }}" required>
                                        </div>
                                    </div>

                                    <!-- Security Math Captcha -->
                                    @include('components.captcha')

                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" name="remember" id="otpRemember" checked>
                                        <label class="form-check-label small" for="otpRemember">Remember this device</label>
                                    </div>

                                    <button type="submit" class="btn btn-gradient w-100 py-2.5 fw-semibold rounded-pill shadow-sm">
                                        <i class="bi bi-send-fill me-2"></i> Send 6-Digit OTP Code
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="text-center mt-4 pt-3 border-top small text-body-secondary font-mono">
                        Don't have an account? <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none ms-1">Create Account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
