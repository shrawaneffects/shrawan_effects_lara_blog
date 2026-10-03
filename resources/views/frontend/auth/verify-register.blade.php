@extends('layouts.app')

@section('title', 'Verify Registration Email - ' . \App\Models\Setting::get('site_name', config('app.name', 'Shrawan Effects')))

@section('content')
    <div class="container py-5 my-3">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-lg p-4 p-md-5 rounded-4 bg-body hover-lift" style="border: 1px solid rgba(0,255,102,0.2) !important;">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center text-white rounded-circle mb-3 shadow" style="width: 65px; height: 65px; background: var(--gradient-primary); box-shadow: 0 0 20px rgba(0,255,102,0.4) !important;">
                            <i class="bi bi-envelope-check-fill fs-2 text-dark"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><span class="text-gradient font-cyber">AUTHENTICATE EMAIL</span></h3>
                        <p class="text-body-secondary small mb-0 font-mono">
                            Validation packet transmitted to:
                            <br><strong class="text-success">{{ $email }}</strong>
                        </p>
                    </div>

                    @if(session('info'))
                        <div class="alert alert-info border-0 rounded-3 small py-2 d-flex align-items-center mb-3">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <div>{{ session('info') }}</div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-3 small py-2 d-flex align-items-center mb-3">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    @if(session('fallback_otp'))
                        <div class="alert alert-warning border border-warning-subtle rounded-3 small p-3 mb-3">
                            <div class="d-flex align-items-center mb-1 text-warning-emphasis fw-bold">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                <span>Email Delivery Notice</span>
                            </div>
                            <div class="text-muted small mb-2">
                                {{ session('email_error') }}
                            </div>
                            <div class="p-2 bg-body rounded border text-center">
                                <span class="text-muted small d-block mb-1">Your Temporary Verification Code:</span>
                                <span class="fs-4 fw-bold font-monospace text-success" style="letter-spacing: 4px;">{{ session('fallback_otp') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Remaining Daily Attempts Counter -->
                    <div class="alert text-center py-2 px-3 rounded-pill small mb-4 font-mono" style="background: rgba(33,54,8,0.4); border: 1px solid rgba(0,255,102,0.25); color: #00ff66;">
                        <i class="bi bi-shield-lock me-1 text-success"></i>
                        SESSION ATTEMPTS REMAINING: <strong>{{ $remainingAttempts ?? 5 }} OF 5</strong>
                    </div>

                    <form action="{{ route('register.verify-otp.post') }}" method="POST" id="registerOtpForm">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token ?? '' }}">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-center w-100 mb-2">Enter 6-Digit Verification Code</label>
                            <div class="d-flex justify-content-center">
                                <input type="text" name="code" id="regOtpInput" 
                                       class="form-control text-center font-monospace fs-2 fw-bold tracking-widest rounded-4 border-2 @error('code') is-invalid @enderror" 
                                       style="letter-spacing: 12px; max-width: 280px; height: 65px;" 
                                       maxlength="6" inputmode="numeric" pattern="[0-9]*" 
                                       placeholder="••••••" autofocus required autocomplete="one-time-code">
                            </div>
                            @error('code')
                                <div class="text-danger small text-center mt-2">{{ $message }}</div>
                            @enderror
                            @error('email')
                                <div class="text-danger small text-center mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-gradient w-100 py-2.5 fw-semibold rounded-pill shadow-sm mb-3">
                            <i class="bi bi-check-circle-fill me-1"></i> Complete Registration
                        </button>
                    </form>

                    <!-- Resend OTP Action -->
                    <div class="d-flex align-items-center justify-content-between pt-3 border-top small text-body-secondary">
                        <form action="{{ route('register.resend-otp') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token ?? '' }}">
                            <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0 text-primary fw-semibold" id="resendRegBtn">
                                <i class="bi bi-arrow-repeat me-1"></i> Resend Code
                            </button>
                            <span id="countdownRegText" class="text-muted d-none ms-1">(wait <span id="timerRegSeconds">60</span>s)</span>
                        </form>

                        <a href="{{ route('register') }}" class="text-decoration-none text-muted">
                            <i class="bi bi-arrow-left me-1"></i> Change Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('regOtpInput');
        if (input) {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 6) {
                    document.getElementById('registerOtpForm').submit();
                }
            });
        }

        const resendBtn = document.getElementById('resendRegBtn');
        const countdownText = document.getElementById('countdownRegText');
        const timerSeconds = document.getElementById('timerRegSeconds');

        if (resendBtn && countdownText) {
            let timeLeft = 60;
            resendBtn.classList.add('disabled');
            resendBtn.style.pointerEvents = 'none';
            countdownText.classList.remove('d-none');

            const timer = setInterval(() => {
                timeLeft--;
                timerSeconds.textContent = timeLeft;
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    resendBtn.classList.remove('disabled');
                    resendBtn.style.pointerEvents = 'auto';
                    countdownText.classList.add('d-none');
                }
            }, 1000);
        }
    });
</script>
@endpush
