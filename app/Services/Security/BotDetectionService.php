<?php

namespace App\Services\Security;

use App\Models\AuditLog;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BotDetectionService
{
    protected const VIEW_FALLBACK_SECRET = 'shrawaneffects_view_guard_secret_2026';

    /**
     * Substrings identifying crawlers, scrapers, CLI tools, link previewers, and headless monitors.
     */
    protected const BOT_SIGNATURES = [
        'bot', 'crawl', 'spider', 'slurp', 'mediapartners', 'adsbot',
        'curl/', 'wget/', 'python', 'requests/', 'aiohttp', 'httpx', 'urllib',
        'go-http-client', 'java/', 'ruby', 'php/', 'guzzle', 'node-fetch',
        'axios/', 'undici', 'postman', 'insomnia', 'libwww-perl', 'mechanize',
        'scrapy', 'headless', 'headlesschrome', 'phantom', 'phantomjs', 'selenium',
        'puppeteer', 'playwright', 'jsdom', 'zombie', 'htmlunit',
        'lighthouse', 'chrome-lighthouse', 'pagespeed', 'gtmetrix', 'pingdom',
        'uptimerobot', 'statuscake', 'prerender', 'cf-worker', 'deno/', 'bun/',
        'got/', 'superagent', 'okhttp', 'dalvik', 'cfnetwork',
        'ahrefs', 'semrush', 'mj12bot', 'dotbot', 'petalbot', 'bytespider',
        'amazonbot', 'oai-searchbot', 'googleother', 'google-inspectiontool',
        'gptbot', 'chatgpt-user', 'claudebot', 'anthropic', 'perplexity',
        'applebot', 'bingpreview', 'facebookexternalhit', 'facebot', 'twitterbot',
        'whatsapp', 'telegrambot', 'linkedinbot', 'discordbot', 'slackbot',
        'screaming frog', 'yandex', 'baiduspider', 'sogou', 'exabot',
        'ia_archiver', 'archive.org_bot', 'dataforseo', 'feedfetcher', 'feedparser',
        'nmap', 'nikto', 'sqlmap', 'zgrab', 'nuclei', 'wpscan', 'acunetix',
    ];

    /**
     * Exploit scanners & automated attack signatures to block at the middleware boundary.
     */
    protected const MALICIOUS_SCANNER_SIGNATURES = [
        'sqlmap', 'nikto', 'nmap', 'masscan', 'wpscan', 'acunetix', 'nessus',
        'openvas', 'zgrab', 'dirbuster', 'gobuster', 'feroxbuster', 'nuclei',
        'httprecon', 'havij', 'commix', 'skipfish', 'w3af',
    ];

    /**
     * CLI / scripting user agents blocked on sensitive POST form submissions.
     */
    protected const AUTOMATED_POST_CLIENTS = [
        'curl/', 'wget/', 'python-requests', 'python-urllib', 'aiohttp', 'httpx',
        'scrapy', 'libwww-perl', 'go-http-client', 'php/', 'guzzlehttp', 'mechanize',
        'postmanruntime', 'insomnia/', 'httpie/',
    ];

    /**
     * Detect whether the incoming HTTP request originates from a bot, crawler, prefetch, or headless tool.
     */
    public static function isBot(Request $request): bool
    {
        $ua = strtolower(trim((string) $request->userAgent()));

        // Missing or suspiciously short User-Agent
        if ($ua === '' || strlen($ua) < 10) {
            return true;
        }

        // Known crawler, CLI, or headless signatures
        foreach (self::BOT_SIGNATURES as $sig) {
            if (str_contains($ua, $sig)) {
                return true;
            }
        }

        // Browser link prefetch / prerender requests should never count as article reads
        $purpose = strtolower((string) $request->header('Purpose', $request->header('Sec-Purpose', '')));
        $xMoz = strtolower((string) $request->header('X-Moz', ''));
        if (str_contains($purpose, 'prefetch') || str_contains($purpose, 'preview') || $xMoz === 'prefetch') {
            return true;
        }

        // Real desktop/mobile browsers always send an Accept-Language header on page and fetch requests
        if (empty($request->header('Accept-Language'))) {
            return true;
        }

        return false;
    }

    /**
     * Validate browser-enforced same-origin headers on view-tracking beacons.
     */
    public static function hasValidSameOriginHeaders(Request $request): bool
    {
        $secFetchSite = strtolower(trim((string) $request->header('Sec-Fetch-Site', '')));
        if ($secFetchSite !== '' && $secFetchSite !== 'same-origin') {
            return false;
        }

        $expectedHost = strtolower((string) $request->getHost());
        foreach (['Origin', 'Referer'] as $headerName) {
            $headerVal = trim((string) $request->header($headerName, ''));
            if ($headerVal !== '') {
                $host = strtolower((string) parse_url($headerVal, PHP_URL_HOST));
                if ($host === '' || $host !== $expectedHost) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Detect explicit vulnerability/exploit scanners.
     */
    public static function isMaliciousScanner(Request $request): bool
    {
        $ua = strtolower(trim((string) $request->userAgent()));
        if ($ua === '') {
            return false;
        }

        foreach (self::MALICIOUS_SCANNER_SIGNATURES as $sig) {
            if (str_contains($ua, $sig)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect automated scripting clients hitting form POST endpoints.
     */
    public static function isAutomatedPostClient(Request $request): bool
    {
        $ua = strtolower(trim((string) $request->userAgent()));
        if ($ua === '' || strlen($ua) < 8) {
            return true;
        }

        foreach (self::AUTOMATED_POST_CLIENTS as $sig) {
            if (str_contains($ua, $sig)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a short-lived HMAC-signed view verification challenge for real JS browser sessions.
     *
     * @return array{token: string, timestamp: int, nonce: string, challenge_salt: string}
     */
    public static function generateViewToken(int $postId, Request $request): array
    {
        $timestamp = time();
        $nonce = bin2hex(random_bytes(8));
        $ipHash = self::getVisitorFingerprint($request);
        $token = hash_hmac('sha256', "{$postId}|{$ipHash}|{$timestamp}|{$nonce}", self::getSecretKey());

        return [
            'token' => $token,
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'challenge_salt' => self::getChallengeSalt($postId, $nonce, $timestamp),
        ];
    }

    /**
     * Verify signed challenge, interactive JS proof, dwell time, and per-visitor dedup before counting a view.
     */
    public static function recordHumanView(
        Post $post,
        Request $request,
        ?string $token,
        int|string|null $timestamp,
        ?string $nonce
    ): bool {
        // Already counted in this browser session — silent no-op without warning audit logs
        $sessionKey = 'viewed_post_' . $post->id;
        if ($request->hasSession() && $request->session()->has($sessionKey)) {
            return false;
        }

        if (self::isBot($request) || !self::hasValidSameOriginHeaders($request)) {
            self::recordBlockedViewAudit($request, $post, 'Bot/invalid-origin view beacon blocked');
            return false;
        }

        if ($request->boolean('webdriver') || !$request->boolean('engaged')) {
            self::recordBlockedViewAudit($request, $post, 'Automated or non-interactive view ping rejected');
            return false;
        }

        $ts = intval($timestamp);
        $now = time();
        $elapsedSec = $now - $ts;
        // Require >= 2s elapsed since page render (stops instant scrapers) and <= 1h challenge lifetime
        if (empty($token) || empty($nonce) || $ts <= 0 || $elapsedSec < 2 || $elapsedSec > 3600) {
            self::recordBlockedViewAudit($request, $post, 'Invalid timing/challenge metadata on view beacon');
            return false;
        }

        $ipHash = self::getVisitorFingerprint($request);
        $expected = hash_hmac('sha256', "{$post->id}|{$ipHash}|{$ts}|{$nonce}", self::getSecretKey());
        if (!hash_equals($expected, (string) $token)) {
            self::recordBlockedViewAudit($request, $post, 'Invalid HMAC signature on view beacon');
            return false;
        }

        $dwellMs = (int) $request->input('dwell_ms', 0);
        $scrollY = (int) $request->input('scroll_y', -1);
        $jsProof = $request->input('js_proof');
        if (!self::verifyJsProof($post->id, (string) $nonce, $ts, $dwellMs, $scrollY, is_string($jsProof) ? $jsProof : null)) {
            self::recordBlockedViewAudit($request, $post, 'Failed dynamic JS proof-of-execution challenge');
            return false;
        }

        // Prevent token replay
        if (!Cache::add("view_token_used_{$token}", true, now()->addMinutes(65))) {
            return false;
        }

        // IP + User-Agent Cache deduplication (12 hours) — stops cookie-less bots
        $dedupKey = "post_viewed_{$post->id}_{$ipHash}";
        if (!Cache::add($dedupKey, true, now()->addHours(12))) {
            return false;
        }

        // Hourly velocity cap per IP (or IPv6 /64 subnet): max 25 unique post views per hour
        $ipKey = 'view_velocity_' . sha1(self::getVelocityBucketKey($request));
        $hourlyCount = (int) Cache::get($ipKey, 0);
        if ($hourlyCount >= 25) {
            self::recordBlockedViewAudit($request, $post, 'Exceeded hourly view velocity threshold');
            return false;
        }

        if ($hourlyCount === 0) {
            Cache::put($ipKey, 1, now()->addHour());
        } else {
            Cache::increment($ipKey);
        }

        $post->increment('views_count');

        if ($request->hasSession()) {
            $request->session()->put($sessionKey, true);
        }

        return true;
    }

    /**
     * Derive per-challenge salt bound to the rendered post and nonce.
     */
    public static function getChallengeSalt(int $postId, string $nonce, int $timestamp): string
    {
        return substr(hash_hmac('sha256', "salt|{$postId}|{$nonce}|{$timestamp}", self::getSecretKey()), 0, 16);
    }

    /**
     * Verify dynamic SHA-256 execution proof computed in the reader's browser.
     */
    protected static function verifyJsProof(
        int $postId,
        string $nonce,
        int $timestamp,
        int $dwellMs,
        int $scrollY,
        ?string $jsProof
    ): bool {
        if ($dwellMs < 2500 || $dwellMs > 86400000 || $scrollY < 0 || $scrollY > 1000000 || empty($jsProof)) {
            return false;
        }

        $salt = self::getChallengeSalt($postId, $nonce, $timestamp);
        $expected = hash('sha256', "{$postId}|{$nonce}|{$timestamp}|{$salt}|{$dwellMs}|{$scrollY}");

        return hash_equals($expected, strtolower(trim($jsProof)));
    }

    /**
     * Group IPv6 clients by /64 subnet for rate limiting so rotating host bits cannot bypass velocity caps.
     */
    protected static function getVelocityBucketKey(Request $request): string
    {
        $ip = (string) $request->ip();
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $packed = @inet_pton($ip);
            if ($packed !== false && strlen($packed) === 16) {
                return 'v6_' . bin2hex(substr($packed, 0, 8));
            }
        }

        return 'v4_' . $ip;
    }

    /**
     * Log blocked robotic view attempts to AuditLog (throttled per IP/post every 15 minutes).
     */
    protected static function recordBlockedViewAudit(Request $request, Post $post, string $reason): void
    {
        try {
            $ipHash = self::getVisitorFingerprint($request);
            if (!Cache::add("audit_bot_view_{$post->id}_{$ipHash}", true, now()->addMinutes(15))) {
                return;
            }

            AuditLog::record(
                'bot_view_blocked',
                "{$reason} on post #{$post->id} (UA: " . substr((string) $request->userAgent(), 0, 120) . ')',
                null,
                ['post_id' => $post->id, 'slug' => $post->slug, 'ip' => $request->ip()],
                'info',
                $request
            );
        } catch (\Throwable $e) {
            // Never let audit logging break response flow
        }
    }

    protected static function getVisitorFingerprint(Request $request): string
    {
        return sha1((string) $request->ip() . '|' . strtolower(trim((string) $request->userAgent())));
    }

    protected static function getSecretKey(): string
    {
        $key = config('app.key');
        return !empty($key) ? (string) $key : self::VIEW_FALLBACK_SECRET;
    }
}
