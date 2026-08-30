<?php

namespace App\Services\Seo;

use App\Models\SeoRedirect;
use Illuminate\Support\Str;

class SeoRedirectService
{
    /**
     * Automatically log a 301 redirect when a published post slug changes
     */
    public static function createSlugRedirect(string $oldSlug, string $newSlug): ?SeoRedirect
    {
        if ($oldSlug === $newSlug || empty($oldSlug) || empty($newSlug)) {
            return null;
        }

        $oldPath = '/blog/' . ltrim($oldSlug, '/');
        $newPath = '/blog/' . ltrim($newSlug, '/');

        // Prevent self-redirect loop
        if ($oldPath === $newPath) {
            return null;
        }

        // Check if there is an existing redirect pointing to this old path, update chain
        SeoRedirect::where('new_url', $oldPath)->update(['new_url' => $newPath]);

        // Remove any reverse redirect to prevent loop
        SeoRedirect::where('old_url', $newPath)->delete();

        return SeoRedirect::updateOrCreate(
            ['old_url' => $oldPath],
            ['new_url' => $newPath, 'status_code' => 301]
        );
    }

    /**
     * Find matching redirect for a given request path
     */
    public static function findRedirect(string $path): ?SeoRedirect
    {
        $normalized = '/' . trim($path, '/');
        $redirect = SeoRedirect::where('old_url', $normalized)->first();

        if ($redirect) {
            $redirect->recordHit();
            return $redirect;
        }

        return null;
    }
}
