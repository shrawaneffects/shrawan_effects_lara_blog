<?php

namespace App\Http\Middleware;

use App\Services\Seo\SeoRedirectService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectMiddleware
{
    /**
     * Handle an incoming request and check for active 301 SEO redirects.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET')) {
            $path = '/' . ltrim($request->path(), '/');
            $redirect = SeoRedirectService::findRedirect($path);

            if ($redirect) {
                return redirect($redirect->new_url, $redirect->status_code ?: 301);
            }
        }

        return $next($request);
    }
}
