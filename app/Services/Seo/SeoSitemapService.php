<?php

namespace App\Services\Seo;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;

class SeoSitemapService
{
    /**
     * Generate dynamic XML sitemap string
     */
    public static function generateXml(): string
    {
        $urls = [];

        // 1. Home page
        $urls[] = [
            'loc' => route('home'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // 2. Articles Index page
        $urls[] = [
            'loc' => route('blog.index'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '0.9',
        ];

        // 3. Category pages
        $categories = Category::where('is_active', true)->get();
        foreach ($categories as $cat) {
            $urls[] = [
                'loc' => route('blog.category', $cat->slug),
                'lastmod' => $cat->updated_at ? $cat->updated_at->toAtomString() : now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // 4. Published Pages (About Us, Privacy Policy, Terms, etc.)
        $pages = Page::published()->get();
        foreach ($pages as $page) {
            $urls[] = [
                'loc' => route('page.show', $page->slug),
                'lastmod' => $page->updated_at ? $page->updated_at->toAtomString() : now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        // 5. Published, indexable, canonical Blog Posts
        $posts = Post::published()
            ->with('seo')
            ->where(function ($q) {
                $q->whereDoesntHave('seo')
                  ->orWhereHas('seo', function ($sq) {
                      $sq->where('robots_index', true)
                         ->where('include_in_sitemap', true);
                  });
            })
            ->latest('updated_at')
            ->get();

        foreach ($posts as $post) {
            $urls[] = [
                'loc' => route('blog.show', $post->slug),
                'lastmod' => $post->updated_at ? $post->updated_at->toAtomString() : ($post->published_at ? $post->published_at->toAtomString() : now()->toAtomString()),
                'changefreq' => 'weekly',
                'priority' => $post->is_featured ? '0.9' : '0.8',
            ];
        }

        // Build XML output
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($u['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>{$u['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$u['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
