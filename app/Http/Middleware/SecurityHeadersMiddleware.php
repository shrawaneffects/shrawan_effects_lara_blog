<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and attach cyber security headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Safe Content Security Policy for blog assets (Bootstrap, Google Fonts, CDNs, AdSense, YouTube)
        $csp = "default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval'; " .
               "img-src 'self' data: https: blob:; " .
               "font-src 'self' https: data:; " .
               "frame-src 'self' https: https://www.youtube.com https://player.vimeo.com https://googleads.g.doubleclick.net https://tpc.googlesyndication.com; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: https://pagead2.googlesyndication.com https://cdn.jsdelivr.net; " .
               "object-src 'none';";
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
