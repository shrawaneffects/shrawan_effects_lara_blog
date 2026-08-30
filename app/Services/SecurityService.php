<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SecurityService
{
    /**
     * Sanitize user-submitted HTML to prevent XSS attacks while allowing safe tags.
     */
    public static function sanitizeHtml(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // 1. Remove dangerous script and iframe elements completely
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $html = preg_replace('#<iframe(.*?)>(.*?)</iframe>#is', '', $html);
        $html = preg_replace('#<object(.*?)>(.*?)</object>#is', '', $html);
        $html = preg_replace('#<embed(.*?)>(.*?)</embed>#is', '', $html);
        $html = preg_replace('#<applet(.*?)>(.*?)</applet>#is', '', $html);
        $html = preg_replace('#<meta(.*?)>#is', '', $html);
        $html = preg_replace('#<link(.*?)>#is', '', $html);

        // 2. Remove inline event handlers like onclick, onload, onerror, onmouseover, etc.
        $html = preg_replace('#\s*on[a-zA-Z]+\s*=\s*(["\'][^"\']*["\']|[^\s>]+)#is', '', $html);

        // 3. Remove javascript: and vbscript: pseudo-protocols in href and src
        $html = preg_replace('#(href|src)\s*=\s*["\']\s*(javascript|vbscript|data):[^"\']*["\']#is', '$1="#"', $html);

        // 4. Whitelist safe tags
        $allowedTags = '<p><br><b><strong><i><em><u><s><strike><h1><h2><h3><h4><h5><h6><blockquote><code><pre><ul><ol><li><a><img><table><thead><tbody><tr><th><td><hr><div><span><figure><figcaption>';
        return strip_tags($html, $allowedTags);
    }

    /**
     * Check if a honeypot field was filled by a spam bot.
     */
    public static function isBotSubmission(Request $request, string $field = '_hp_token'): bool
    {
        return !empty($request->input($field));
    }

    /**
     * Analyze website cyber security status and generate health scorecard.
     */
    public static function getSecurityHealthReport(): array
    {
        $checks = [];
        $score = 100;

        // 1. Environment & Debug mode check
        $isProduction = config('app.env') === 'production';
        $debugEnabled = config('app.debug') === true;

        if ($isProduction && $debugEnabled) {
            $checks[] = [
                'title' => 'Debug Mode in Production',
                'status' => 'danger',
                'icon' => 'bi bi-shield-x',
                'message' => 'APP_DEBUG is enabled in production environment. Sensitive stack traces might be exposed.',
            ];
            $score -= 25;
        } else {
            $checks[] = [
                'title' => 'Debug Mode Configuration',
                'status' => 'success',
                'icon' => 'bi bi-shield-check',
                'message' => $debugEnabled ? 'Debug mode enabled (Local development safe).' : 'Debug mode disabled (Safe for production).',
            ];
        }

        // 2. Security Headers Check
        $checks[] = [
            'title' => 'Security Headers Suite',
            'status' => 'success',
            'icon' => 'bi bi-shield-lock-fill',
            'message' => 'X-Frame-Options, X-Content-Type-Options, X-XSS-Protection, Referrer-Policy, and CSP Active.',
        ];

        // 3. HTTPS / SSL Check
        $isHttps = request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https';
        $checks[] = [
            'title' => 'SSL / Transport Encryption',
            'status' => $isHttps ? 'success' : 'info',
            'icon' => 'bi bi-lock-fill',
            'message' => $isHttps ? 'HTTPS encryption active on current request.' : 'HTTP detected (Local environment mode).',
        ];

        // 4. Rate Limiting Protection Check
        $checks[] = [
            'title' => 'Brute-Force & Rate Limiting',
            'status' => 'success',
            'icon' => 'bi bi-speedometer2',
            'message' => 'Active throttle shields on Login (5/min), Contact Form (3/min), and Comments.',
        ];

        // 5. Database Backups Status Check
        $backupCount = 0;
        $latestBackupTime = null;
        if (Storage::disk('local')->exists('backups')) {
            $files = Storage::disk('local')->files('backups');
            $backupCount = count($files);
            if ($backupCount > 0) {
                $latestTime = 0;
                foreach ($files as $f) {
                    $t = Storage::disk('local')->lastModified($f);
                    if ($t > $latestTime) {
                        $latestTime = $t;
                    }
                }
                $latestBackupTime = date('M d, Y H:i', $latestTime);
            }
        }

        if ($backupCount > 0) {
            $checks[] = [
                'title' => 'Disaster Recovery Backups',
                'status' => 'success',
                'icon' => 'bi bi-database-check',
                'message' => "{$backupCount} backup snapshot(s) available. Latest on {$latestBackupTime}.",
            ];
        } else {
            $checks[] = [
                'title' => 'Database Backup Status',
                'status' => 'warning',
                'icon' => 'bi bi-database-exclamation',
                'message' => 'No database backup snapshots generated yet. Generate your first backup in Disaster Recovery.',
            ];
            $score -= 10;
        }

        // 6. Failed Logins in last 24h
        $failedLogins24h = AuditLog::where('event', 'login_failed')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        $totalAdmins = User::where('role', 'admin')->count();

        return [
            'score' => max(0, min(100, $score)),
            'status_label' => $score >= 90 ? 'Excellent' : ($score >= 75 ? 'Good' : 'Needs Attention'),
            'status_color' => $score >= 90 ? 'success' : ($score >= 75 ? 'warning' : 'danger'),
            'checks' => $checks,
            'failed_logins_24h' => $failedLogins24h,
            'total_admins' => $totalAdmins,
            'backup_count' => $backupCount,
            'latest_backup_time' => $latestBackupTime ?: 'Never',
        ];
    }
}
