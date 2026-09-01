<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
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
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin() || Auth::user()->isAuthor() ? redirect()->route('admin.dashboard') : redirect()->route('home');
        }
        return view('frontend.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        // Check if brute force rate limit exceeded (5 attempts per minute)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            AuditLog::record(
                'login_throttled',
                "Rate limit exceeded for email '{$request->input('email')}' (locked for {$seconds}s)",
                null,
                ['email' => $request->input('email'), 'lockout_seconds' => $seconds],
                'danger',
                $request
            );

            return back()->withErrors([
                'email' => "Too many login attempts. For security reasons, please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');
        $user = User::where('email', $credentials['email'])->first();

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
            Auth::login($user, $remember);
            $request->session()->regenerate();

            AuditLog::record(
                'login_success',
                "User '{$user->name}' ({$user->email}) signed in successfully",
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

        RateLimiter::hit($throttleKey, 60);

        AuditLog::record(
            'login_failed',
            "Failed login attempt for email '{$request->input('email')}'",
            null,
            ['email' => $request->input('email'), 'attempts' => RateLimiter::attempts($throttleKey)],
            'warning',
            $request
        );

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('frontend.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        AuditLog::record(
            'account_registered',
            "New user account registered: '{$user->name}' ({$user->email})",
            $user->id,
            ['email' => $user->email],
            'info',
            $request
        );

        return redirect()->route('home')->with('success', 'Your account has been created successfully!');
    }

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

    public function showForgotPasswordForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('frontend.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'We could not find a user account with that email address.',
            ])->onlyInput('email');
        }

        $token = Str::random(64);

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => \Carbon\Carbon::now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ResetPasswordMail($user, $resetUrl));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send password reset email: ' . $e->getMessage());
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

    public function showResetPasswordForm(Request $request, $token)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $email = $request->query('email', '');

        return view('frontend.auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (!$resetRecord || $resetRecord->token !== $validated['token']) {
            return back()->withErrors([
                'email' => 'This password reset token is invalid or has already been used.',
            ])->onlyInput('email');
        }

        // Check if token expired (60 minutes)
        if (\Carbon\Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')
                ->where('email', $validated['email'])
                ->delete();

            return back()->withErrors([
                'email' => 'This password reset link has expired. Please request a new one.',
            ])->onlyInput('email');
        }

        $user = User::where('email', $validated['email'])->first();
        $user->password = Hash::make($validated['password']);
        $user->save();

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->delete();

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
