<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use App\Services\Security\BotDetectionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BotShieldMiddleware
{
    /**
     * Public form submission URL patterns protected against automated CLI/bot scripts.
     */
    protected const PROTECTED_POST_PATHS = [
        'contact',
        'blog/*/comments',
        'blog/*/track-view',
        'newsletter',
        'login',
        'login/magic-link',
        'login/email-otp',
        'register',
        'forgot-password',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (app()->runningUnitTests()) {
            return $next($request);
        }

        // 1. Block known vulnerability & exploit scanners across all routes
        if (BotDetectionService::isMaliciousScanner($request)) {
            $this->recordBlockedEvent($request, 'scanner_blocked', 'Blocked malicious scanner User-Agent');
            abort(403, 'Automated scanner requests are not permitted.');
        }

        // 2. Block headless CLI / scripting HTTP clients on protected POST forms
        if ($request->isMethod('post') && $request->is(...self::PROTECTED_POST_PATHS)) {
            if (BotDetectionService::isAutomatedPostClient($request)) {
                $this->recordBlockedEvent($request, 'bot_form_blocked', 'Blocked automated bot POST request on ' . $request->path());
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Automated submissions are blocked.'], 403);
                }
                abort(403, 'Automated submissions are not permitted.');
            }
        }

        return $next($request);
    }

    protected function recordBlockedEvent(Request $request, string $event, string $description): void
    {
        try {
            AuditLog::record(
                $event,
                $description . ' (UA: ' . substr((string) $request->userAgent(), 0, 120) . ')',
                null,
                ['path' => $request->path(), 'ip' => $request->ip()],
                'warning',
                $request
            );
        } catch (\Throwable $e) {
            // Never let logging interrupt security enforcement
        }
    }
}
