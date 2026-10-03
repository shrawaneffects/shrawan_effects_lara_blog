<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $recentAuditLogs = AuditLog::where('user_id', $user->id)->latest()->take(5)->get();

        return view('frontend.profile.edit', compact('user', 'recentAuditLogs'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->bio = $validated['bio'] ?? null;

        // Avatar Upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Password change
        if ($request->filled('password')) {
            if (!Hash::check($request->input('current_password'), $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is not correct.'])->withInput();
            }
            $user->password = Hash::make($validated['password']);

            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\SecurityAlertMail(
                    $user,
                    'Password Changed from Profile Settings',
                    'Your account password was updated from the profile management panel.',
                    $request->ip()
                ));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send profile password update alert: ' . $e->getMessage());
            }

            AuditLog::record(
                'password_changed',
                "User '{$user->name}' updated their account password",
                $user->id,
                [],
                'warning',
                $request
            );
        }

        $user->save();

        AuditLog::record(
            'profile_updated',
            "User '{$user->name}' updated profile details",
            $user->id,
            [],
            'info',
            $request
        );

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * GDPR Data Portability: Export personal user data as JSON package.
     */
    public function exportData(Request $request)
    {
        $user = Auth::user();

        $data = [
            'account' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'bio' => $user->bio,
                'avatar' => $user->avatar_url,
                'registered_at' => $user->created_at->toIso8601String(),
            ],
            'authored_articles' => Post::where('user_id', $user->id)
                ->select(['id', 'title', 'slug', 'status', 'views_count', 'created_at'])
                ->get(),
            'comments' => Comment::where('user_id', $user->id)
                ->select(['id', 'post_id', 'content', 'status', 'created_at'])
                ->get(),
            'security_audit_logs' => AuditLog::where('user_id', $user->id)
                ->select(['event', 'description', 'ip_address', 'created_at'])
                ->latest()
                ->take(50)
                ->get(),
            'exported_at' => now()->toIso8601String(),
        ];

        AuditLog::record(
            'gdpr_data_exported',
            "User '{$user->name}' exported personal account data (GDPR)",
            $user->id,
            [],
            'info',
            $request
        );

        $filename = 'personal_data_' . $user->id . '_' . now()->format('Ymd_His') . '.json';
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Session Security: Invalidate all other active logged-in devices.
     */
    public function logoutOtherDevices(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['password' => 'The provided password does not match our records.']);
        }

        Auth::logoutOtherDevices($request->input('password'));

        AuditLog::record(
            'sessions_terminated',
            "User '{$user->name}' logged out all other active device sessions",
            $user->id,
            [],
            'warning',
            $request
        );

        return back()->with('success', 'All other logged-in device sessions have been terminated.');
    }

    /**
     * GDPR Right to be Forgotten: Permanently erase user account and scrub data.
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'confirm_password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->input('confirm_password'), $user->password)) {
            return back()->withErrors(['confirm_password' => 'Incorrect password. Cannot delete account.']);
        }

        // Clean up avatar if present
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Anonymize user comments
        Comment::where('user_id', $user->id)->update([
            'user_id' => null,
            'guest_name' => 'Deleted User',
            'guest_email' => 'deleted@privacy.local',
        ]);

        AuditLog::record(
            'account_deleted',
            "User account '{$user->name}' ({$user->email}) was deleted under GDPR erasure",
            null,
            ['deleted_user_id' => $user->id, 'email' => $user->email],
            'danger',
            $request
        );

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Your account has been permanently deleted.');
    }
}
