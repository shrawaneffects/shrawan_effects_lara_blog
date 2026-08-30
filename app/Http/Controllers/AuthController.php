<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('home');
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

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

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
            $request->session()->regenerate();

            AuditLog::record(
                'login_success',
                "User '{$user->name}' ({$user->role}) signed in successfully",
                $user->id,
                ['email' => $user->email, 'role' => $user->role],
                'info',
                $request
            );

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome back, ' . $user->name . '!');
            }

            return redirect()->intended(route('home'))->with('success', 'Welcome back, ' . $user->name . '!');
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
}
