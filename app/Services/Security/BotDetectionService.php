<?php

namespace App\Services\Security;

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
        'scrapy', 'headless', 'phantomjs', 'selenium', 'puppeteer', 'playwright',
        'lighthouse', 'pagespeed', 'gtmetrix', 'pingdom', 'uptimerobot', 'statuscake',
        'ahrefs', 'semrush', 'mj12bot', 'dotbot', 'petalbot', 'bytespider',
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

        // Real desktop/mobile browsers always send an Accept-Language header on page/fetch requests
        if (empty($request->header('Accept-Language')) && !$request->ajax()) {
            return true;
        }

        return false;
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
     * Generate a short-lived HMAC-signed view verification token for real JS browser sessions.
     *
     * @return array{token: string, timestamp: int, nonce: string}
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
        ];
    }

    /**
     * Verify token, filter bots/prefetchers, enforce per-visitor deduplication, and increment views_count once.
     */
    public static function recordHumanView(
        Post $post,
        Request $request,
        ?string $token,
        int|string|null $timestamp,
        ?string $nonce
    ): bool {
        if (self::isBot($request)) {
            return false;
        }

        $ts = intval($timestamp);
        $now = time();
        if (empty($token) || empty($nonce) || $ts <= 0 || ($now - $ts) > 600 || $ts > ($now + 60)) {
            return false;
        }

        $ipHash = self::getVisitorFingerprint($request);
        $expected = hash_hmac('sha256', "{$post->id}|{$ipHash}|{$ts}|{$nonce}", self::getSecretKey());
        if (!hash_equals($expected, (string) $token)) {
            return false;
        }

        // Prevent token replay
        if (!Cache::add("view_token_used_{$token}", true, now()->addMinutes(15))) {
            return false;
        }

        // Session-level deduplication
        $sessionKey = 'viewed_post_' . $post->id;
        if ($request->hasSession() && $request->session()->has($sessionKey)) {
            return false;
        }

        // IP + User-Agent Cache deduplication (12 hours) — stops cookie-less bots
        $dedupKey = "post_viewed_{$post->id}_{$ipHash}";
        if (!Cache::add($dedupKey, true, now()->addHours(12))) {
            return false;
        }

        // Per-IP hourly velocity cap (max 30 unique post views per IP per hour)
        $ipKey = 'view_velocity_' . sha1((string) $request->ip());
        $hourlyCount = (int) Cache::get($ipKey, 0);
        if ($hourlyCount >= 30) {
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
