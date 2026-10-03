<?php

namespace App\Http\Controllers;

use App\Mail\LoginVerificationMail;
use App\Mail\RegistrationOtpMail;
use App\Mail\ResetPasswordMail;
use App\Mail\SecurityAlertMail;
use App\Models\AuditLog;
use App\Models\EmailVerification;
use App\Models\User;
use App\Services\Security\AuthRateLimiterService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show standard login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin() || Auth::user()->isAuthor()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('home');
        }

        return view('frontend.auth.login');
    }

    /**
     * Handle initial credential check (Step 1 of 2FA Login).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = strtolower(trim($credentials['email']));

        // Check strict daily 5-attempt limit for this date
        if (AuthRateLimiterService::isDailyLockedOut($email, 'login')) {
            AuditLog::record(
                'login_daily_locked',
                "Daily attempt limit reached for '{$email}'",
                null,
                ['email' => $email],
                'danger',
                $request
            );

            return back()->withErrors([
                'email' => AuthRateLimiterService::getLockoutMessage(),
            ])->onlyInput('email');
        }

        $throttleKey = Str::transliterate($email . '|' . $request->ip());
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many attempts right now. Please wait {$seconds} seconds.",
            ])->onlyInput('email');
        }

        $user = User::where('email', $email)->first();

        // Check if user exists and password is correct
        if ($user && Hash::check($credentials['password'], $user->password)) {
            if (!$user->is_active) {
                AuditLog::record(
                    'login_failed_deactivated',
                    "Login attempted on deactivated account for '{$user->email}'",
                    $user->id,
                    ['email' => $user->email],
                    'warning',
                    $request
                );

                return back()->withErrors(['email' => 'Your account has been deactivated. Please contact support.'])->onlyInput('email');
            }

            RateLimiter::clear($throttleKey);

            // Generate 6-digit OTP code & session token
            $code = sprintf('%06d', random_int(100000, 999999));
            $token = Str::random(64);

            // Invalidate any previous login OTPs for this user
            EmailVerification::where('email', $user->email)
                ->where('type', 'login')
                ->delete();

            // Create new OTP record (valid for 20 minutes)
            EmailVerification::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'code' => $code,
                'type' => 'login',
                'token' => $token,
                'attempts' => 0,
                'expires_at' => Carbon::now()->addMinutes(20),
            ]);

            // Dispatch 2FA OTP Email from noreply@shrawaneffects.com
            $mailSent = true;
            $mailError = null;
            try {
                Mail::to($user->email)->send(new LoginVerificationMail($user, $code, $request->ip(), $request->userAgent()));
            } catch (\Throwable $e) {
                $mailSent = false;
                $mailError = $e->getMessage();
                Log::error("Failed to send 2FA OTP to {$user->email}: " . $mailError);
            }

            // Bind to session
            $request->session()->put('pending_2fa_user_id', $user->id);
            $request->session()->put('pending_2fa_email', $user->email);
            $request->session()->put('pending_2fa_token', $token);
            $request->session()->put('pending_2fa_remember', $request->boolean('remember'));

            if (!$mailSent) {
                session()->flash('fallback_otp', $code);
                session()->flash('email_error', "SMTP Delivery notice: {$mailError}");
            }

            AuditLog::record(
                'login_otp_dispatched',
                "Login 2FA verification code sent to '{$user->email}'",
                $user->id,
                ['email' => $user->email],
                'info',
                $request
            );

            return redirect()->route('login.verify-otp', ['token' => $token])->with('info', 'A 6-digit verification code was generated for your email. Please enter it below to complete sign-in.');
        }

        // Invalid credentials - increment daily attempts
        RateLimiter::hit($throttleKey, 60);
        $attempts = AuthRateLimiterService::recordFailedAttempt($email, 'login');
        $remaining = AuthRateLimiterService::getRemainingDailyAttempts($email, 'login');

        AuditLog::record(
            'login_failed',
            "Failed login attempt for email '{$email}' (attempt {$attempts}/5 today)",
            null,
            ['email' => $email, 'daily_attempts' => $attempts],
            'warning',
            $request
        );

        $errorMessage = $remaining > 0
            ? "The provided credentials do not match our records. ({$remaining} attempt" . ($remaining === 1 ? '' : 's') . " remaining for today)"
            : AuthRateLimiterService::getLockoutMessage();

        return back()->withErrors([
            'email' => $errorMessage,
        ])->onlyInput('email');
    }

    /**
     * Show 2FA OTP entry screen for Login.
     */
    public function showLoginOtpForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $token = $request->query('token') ?? $request->session()->get('pending_2fa_token');

        if (!$token) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please sign in again.']);
        }

        $verification = EmailVerification::where('token', $token)
            ->where('type', 'login')
            ->first();

        if (!$verification || $verification->isExpired()) {
            return redirect()->route('login')->withErrors(['email' => 'Verification session expired. Please sign in again.']);
        }

        $userId = $verification->user_id;
        $email = $verification->email;

        // Restore / ensure session has credentials
        $request->session()->put('pending_2fa_user_id', $userId);
        $request->session()->put('pending_2fa_token', $token);
        $request->session()->put('pending_2fa_email', $email);

        // Mask email for privacy (e.g. s***@shrawaneffects.com)
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';
        $maskedName = strlen($name) > 2 ? substr($name, 0, 1) . str_repeat('*', strlen($name) - 2) . substr($name, -1) : substr($name, 0, 1) . '*';
        $maskedEmail = $maskedName . '@' . $domain;

        $remainingAttempts = AuthRateLimiterService::getRemainingDailyAttempts($email, 'login');

        return view('frontend.auth.verify-login', compact('maskedEmail', 'remainingAttempts', 'token'));
    }

    /**
     * Verify the 2FA OTP code and complete login.
     */
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $token = $request->input('token') ?? $request->session()->get('pending_2fa_token');

        if (!$token) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please sign in again.']);
        }

        $verification = EmailVerification::where('token', $token)
            ->where('type', 'login')
            ->first();

        if (!$verification) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please sign in again.']);
        }

        $email = $verification->email;
        $userId = $verification->user_id;
        $remember = $request->session()->get('pending_2fa_remember', false);

        // Check daily lock
        if (AuthRateLimiterService::isDailyLockedOut($email, 'login')) {
            return back()->withErrors(['code' => AuthRateLimiterService::getLockoutMessage()]);
        }

        if ($verification->isExpired()) {
            AuthRateLimiterService::recordFailedAttempt($email, 'login');
            $remaining = AuthRateLimiterService::getRemainingDailyAttempts($email, 'login');

            return back()->withErrors([
                'code' => "This verification code has expired or is invalid. Please request a new code. ({$remaining} attempts remaining today)",
            ]);
        }

        // Check if OTP matches
        if ($verification->isValid($request->input('code'))) {
            $user = $verification->user ?: User::find($userId);

            if (!$user || !$user->is_active) {
                return redirect()->route('login')->withErrors(['email' => 'Account not accessible.']);
            }

            // Verification successful
            $verification->delete();
            AuthRateLimiterService::clearDailyAttempts($email, 'login');

            $request->session()->forget(['pending_2fa_user_id', 'pending_2fa_email', 'pending_2fa_token', 'pending_2fa_remember']);

            Auth::login($user, $remember);
            $request->session()->regenerate();

            AuditLog::record(
                'login_2fa_success',
                "User '{$user->name}' ({$user->email}) successfully authenticated via 2FA OTP",
                $user->id,
                ['email' => $user->email, 'role' => $user->role],
                'info',
                $request
            );

            if ($user->isAdmin() || $user->isAuthor()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'));
        }

        // Incorrect OTP code
        $verification->increment('attempts');
        $attempts = AuthRateLimiterService::recordFailedAttempt($email, 'login');
        $remaining = AuthRateLimiterService::getRemainingDailyAttempts($email, 'login');

        AuditLog::record(
            'login_otp_failed',
            "Invalid OTP code submitted for '{$email}' (attempt {$attempts}/5 today)",
            $userId,
            ['email' => $email, 'daily_attempts' => $attempts],
            'warning',
            $request
        );

        $errorMessage = $remaining > 0
            ? "Invalid 6-digit verification code. Please check your email. ({$remaining} attempt" . ($remaining === 1 ? '' : 's') . " remaining for today)"
            : AuthRateLimiterService::getLockoutMessage();

        return back()->withErrors(['code' => $errorMessage]);
    }

    /**
     * Resend Login 2FA OTP.
     */
    public function resendLoginOtp(Request $request)
    {
        $token = $request->input('token') ?? $request->session()->get('pending_2fa_token');
        $verification = $token ? EmailVerification::where('token', $token)->where('type', 'login')->first() : null;

        $userId = $request->session()->get('pending_2fa_user_id') ?? ($verification->user_id ?? null);
        $email = $request->session()->get('pending_2fa_email') ?? ($verification->email ?? null);

        if (!$userId || !$email) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please sign in again.']);
        }

        if (AuthRateLimiterService::isDailyLockedOut($email, 'login')) {
            return back()->withErrors(['code' => AuthRateLimiterService::getLockoutMessage()]);
        }

        // Rate limit resending: max 1 per 60 seconds
        $resendKey = 'resend_otp_' . md5($email . '|' . $request->ip());
        if (RateLimiter::tooManyAttempts($resendKey, 1)) {
            $seconds = RateLimiter::availableIn($resendKey);
            return back()->with('info', "Please wait {$seconds} seconds before requesting a new code.");
        }
        RateLimiter::hit($resendKey, 60);

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login');
        }

        // Generate fresh code & token
        $code = sprintf('%06d', random_int(100000, 999999));
        $token = Str::random(64);

        EmailVerification::where('email', $email)->where('type', 'login')->delete();

        EmailVerification::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'code' => $code,
            'type' => 'login',
            'token' => $token,
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $request->session()->put('pending_2fa_token', $token);

        $mailSent = true;
        $mailError = null;
        try {
            Mail::to($user->email)->send(new LoginVerificationMail($user, $code, $request->ip(), $request->userAgent()));
        } catch (\Throwable $e) {
            $mailSent = false;
            $mailError = $e->getMessage();
            Log::error("Failed to resend 2FA OTP to {$user->email}: " . $mailError);
        }

        if (!$mailSent) {
            session()->flash('fallback_otp', $code);
            session()->flash('email_error', "SMTP Delivery notice: {$mailError}");
        }

        return back()->with('success', 'A fresh verification code has been dispatched to your email.');
    }

    /**
     * Show registration form.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('frontend.auth.register');
    }

    /**
     * Handle initial registration request and dispatch email OTP.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = strtolower(trim($validated['email']));

        // Check daily attempt limit
        if (AuthRateLimiterService::isDailyLockedOut($email, 'register')) {
            return back()->withErrors([
                'email' => AuthRateLimiterService::getLockoutMessage(),
            ])->onlyInput('name', 'email');
        }

        // Generate 6-digit OTP code & token
        $code = sprintf('%06d', random_int(100000, 999999));
        $token = Str::random(64);

        // Delete any prior registration OTPs for this email
        EmailVerification::where('email', $email)
            ->where('type', 'register')
            ->delete();

        EmailVerification::create([
            'email' => $email,
            'code' => $code,
            'type' => 'register',
            'token' => $token,
            'payload' => [
                'name' => trim($validated['name']),
                'password' => Hash::make($validated['password']),
            ],
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send confirmation email from noreply@shrawaneffects.com
        $mailSent = true;
        $mailError = null;
        try {
            Mail::to($email)->send(new RegistrationOtpMail($validated['name'], $code, $email));
        } catch (\Throwable $e) {
            $mailSent = false;
            $mailError = $e->getMessage();
            Log::error("Failed to send registration OTP to {$email}: " . $mailError);
        }

        $request->session()->put('pending_reg_email', $email);
        $request->session()->put('pending_reg_token', $token);

        if (!$mailSent) {
            session()->flash('fallback_otp', $code);
            session()->flash('email_error', "SMTP Delivery notice: {$mailError}");
        }

        return redirect()->route('register.verify-otp', ['token' => $token])->with('info', "A 6-digit confirmation code was generated for {$email}. Please verify to complete your registration.");
    }

    /**
     * Show registration OTP verification screen.
     */
    public function showRegisterOtpForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $token = $request->query('token') ?? $request->session()->get('pending_reg_token');

        if (!$token) {
            return redirect()->route('register')->withErrors(['email' => 'Registration session expired. Please sign up again.']);
        }

        $verification = EmailVerification::where('token', $token)
            ->where('type', 'register')
            ->first();

        if (!$verification || $verification->isExpired()) {
            return redirect()->route('register')->withErrors(['email' => 'Registration session expired. Please sign up again.']);
        }

        $email = $verification->email;
        $request->session()->put('pending_reg_email', $email);
        $request->session()->put('pending_reg_token', $token);

        $remainingAttempts = AuthRateLimiterService::getRemainingDailyAttempts($email, 'register');

        return view('frontend.auth.verify-register', compact('email', 'remainingAttempts', 'token'));
    }

    /**
     * Verify registration OTP and create user in database.
     */
    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $token = $request->input('token') ?? $request->session()->get('pending_reg_token');

        if (!$token) {
            return redirect()->route('register')->withErrors(['email' => 'Registration session expired. Please sign up again.']);
        }

        $verification = EmailVerification::where('token', $token)
            ->where('type', 'register')
            ->first();

        if (!$verification) {
            return redirect()->route('register')->withErrors(['email' => 'Registration session expired. Please sign up again.']);
        }

        $email = $verification->email;

        if (AuthRateLimiterService::isDailyLockedOut($email, 'register')) {
            return back()->withErrors(['code' => AuthRateLimiterService::getLockoutMessage()]);
        }

        if ($verification->isExpired()) {
            AuthRateLimiterService::recordFailedAttempt($email, 'register');
            $remaining = AuthRateLimiterService::getRemainingDailyAttempts($email, 'register');

            return back()->withErrors([
                'code' => "This registration code has expired or is invalid. ({$remaining} attempts remaining today)",
            ]);
        }

        if ($verification->isValid($request->input('code'))) {
            $payload = $verification->payload;

            // Create the verified user
            $user = User::create([
                'name' => $payload['name'] ?? 'User',
                'email' => $email,
                'password' => $payload['password'],
                'role' => 'user',
                'email_verified_at' => Carbon::now(),
                'is_active' => true,
            ]);

            $verification->delete();
            AuthRateLimiterService::clearDailyAttempts($email, 'register');
            $request->session()->forget(['pending_reg_email', 'pending_reg_token']);

            Auth::login($user);
            $request->session()->regenerate();

            AuditLog::record(
                'account_registered',
                "New user account registered and verified via email: '{$user->name}' ({$user->email})",
                $user->id,
                ['email' => $user->email],
                'info',
                $request
            );

            return redirect()->route('home')->with('success', 'Email verified successfully! Welcome to ' . \App\Models\Setting::get('site_name', config('app.name', 'Shrawan Effects')) . '.');
        }

        // Invalid code
        $verification->increment('attempts');
        $attempts = AuthRateLimiterService::recordFailedAttempt($email, 'register');
        $remaining = AuthRateLimiterService::getRemainingDailyAttempts($email, 'register');

        $errorMessage = $remaining > 0
            ? "Invalid 6-digit confirmation code. ({$remaining} attempt" . ($remaining === 1 ? '' : 's') . " remaining today)"
            : AuthRateLimiterService::getLockoutMessage();

        return back()->withErrors(['code' => $errorMessage]);
    }

    /**
     * Resend registration email OTP.
     */
    public function resendRegisterOtp(Request $request)
    {
        $email = $request->session()->get('pending_reg_email');
        if (!$email) {
            return redirect()->route('register');
        }

        if (AuthRateLimiterService::isDailyLockedOut($email, 'register')) {
            return back()->withErrors(['code' => AuthRateLimiterService::getLockoutMessage()]);
        }

        $resendKey = 'resend_reg_otp_' . md5($email . '|' . $request->ip());
        if (RateLimiter::tooManyAttempts($resendKey, 1)) {
            $seconds = RateLimiter::availableIn($resendKey);
            return back()->with('info', "Please wait {$seconds} seconds before requesting a new code.");
        }
        RateLimiter::hit($resendKey, 60);

        $verification = EmailVerification::where('email', $email)
            ->where('type', 'register')
            ->first();

        if (!$verification) {
            return redirect()->route('register');
        }

        $code = sprintf('%06d', random_int(100000, 999999));
        $token = Str::random(64);

        $verification->update([
            'code' => $code,
            'token' => $token,
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $request->session()->put('pending_reg_token', $token);

        $mailSent = true;
        $mailError = null;
        try {
            Mail::to($email)->send(new RegistrationOtpMail($verification->payload['name'] ?? 'User', $code, $email));
        } catch (\Throwable $e) {
            $mailSent = false;
            $mailError = $e->getMessage();
            Log::error("Failed to resend registration OTP to {$email}: " . $mailError);
        }

        if (!$mailSent) {
            session()->flash('fallback_otp', $code);
            session()->flash('email_error', "SMTP Delivery notice: {$mailError}");
        }

        return back()->with('success', 'A new verification code has been dispatched to your email.');
    }

    /**
     * Sign out authenticated user.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            AuditLog::record(
                'logout',
                "User '{$user->name}' signed out",
                $user->id,
                ['email' => $user->email],
                'info',
                $request
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show Forgot Password Form.
     */
    public function showForgotPasswordForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('frontend.auth.forgot-password');
    }

    /**
     * Send Password Reset Link to Email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($validated['email']));

        // Check daily rate limit
        if (AuthRateLimiterService::isDailyLockedOut($email, 'password_reset')) {
            return back()->withErrors([
                'email' => AuthRateLimiterService::getLockoutMessage(),
            ])->onlyInput('email');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            AuthRateLimiterService::recordFailedAttempt($email, 'password_reset');
            return back()->withErrors([
                'email' => 'We could not find a user account with that email address.',
            ])->onlyInput('email');
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user, $resetUrl));
        } catch (\Throwable $e) {
            Log::error('Failed to send password reset email: ' . $e->getMessage());
        }

        AuditLog::record(
            'password_reset_requested',
            "Password reset link requested for '{$user->email}'",
            $user->id,
            ['email' => $user->email],
            'info',
            $request
        );

        return back()->with('status', 'We have emailed your password reset link! Please check your inbox.');
    }

    /**
     * Show Reset Password View.
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $email = $request->query('email', '');

        return view('frontend.auth.reset-password', compact('token', 'email'));
    }

    /**
     * Reset the user password and dispatch security alert.
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = strtolower(trim($validated['email']));

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord || $resetRecord->token !== $validated['token']) {
            return back()->withErrors([
                'email' => 'This password reset token is invalid or has already been used.',
            ])->onlyInput('email');
        }

        // Check if token expired (60 minutes)
        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return back()->withErrors([
                'email' => 'This password reset link has expired. Please request a new one.',
            ])->onlyInput('email');
        }

        $user = User::where('email', $email)->first();
        $user->password = Hash::make($validated['password']);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Dispatch Security Alert Email
        try {
            Mail::to($user->email)->send(new SecurityAlertMail(
                $user,
                'Password Reset Successfully',
                'Your account password was updated via the password reset link.',
                $request->ip()
            ));
        } catch (\Throwable $e) {
            Log::error('Failed to send password reset security alert: ' . $e->getMessage());
        }

        AuditLog::record(
            'password_reset_completed',
            "Password was successfully reset for user '{$user->name}' ({$user->email})",
            $user->id,
            ['email' => $user->email],
            'info',
            $request
        );

        return redirect()->route('login')->with('success', 'Your password has been successfully reset! Please sign in with your new password.');
    }
}
