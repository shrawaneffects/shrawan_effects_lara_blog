<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostSeo;
use App\Models\SeoAudit;
use App\Models\SeoRedirect;
use App\Services\Seo\SeoAuditService;
use App\Services\Seo\SeoFreshnessService;
use App\Services\Seo\SeoInternalLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SeoDashboardController extends Controller
{
    /**
     * SEO Dashboard Overview
     */
    public function index(Request $request)
    {
        $query = Post::with(['author', 'category', 'seo']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhereHas('seo', function ($sq) use ($search) {
                      $sq->where('primary_keyword', 'like', "%{$search}%")
                         ->orWhere('seo_title', 'like', "%{$search}%");
                  });
            });
        }

        $allPosts = Post::with('seo')->get();

        // Calculate Global SEO Metrics
        $totalPosts = $allPosts->count();
        $seoScores = [];
        $missingTitlesCount = 0;
        $missingDescriptionsCount = 0;
        $criticalCount = 0;
        $warningCount = 0;

        foreach ($allPosts as $p) {
            $seo = $p->seo;
            if ($seo) {
                $seoScores[] = $seo->seo_score;
                if ($seo->seo_status === 'critical') $criticalCount++;
                if ($seo->seo_status === 'needs_review') $warningCount++;
                if (empty($seo->seo_title)) $missingTitlesCount++;
                if (empty($seo->meta_description)) $missingDescriptionsCount++;
            } else {
                $missingTitlesCount++;
                $missingDescriptionsCount++;
                $criticalCount++;
            }
        }

        $avgScore = count($seoScores) > 0 ? round(array_sum($seoScores) / count($seoScores)) : 0;

        // Link metrics & orphans
        $linkMetrics = Post::computeLinkMetrics($allPosts);
        $orphanCount = $linkMetrics['orphanCount'];

        // Stale articles count
        $staleCount = 0;
        foreach ($allPosts as $p) {
            $freshness = SeoFreshnessService::evaluate($p);
            if ($freshness['status'] === 'stale') {
                $staleCount++;
            }
        }

        // Duplicate metadata check
        $titleCounts = PostSeo::select('seo_title')->whereNotNull('seo_title')->where('seo_title', '!=', '')->groupBy('seo_title')->havingRaw('count(*) > 1')->pluck('seo_title');
        $duplicateTitlesCount = count($titleCounts);

        $keywordCounts = PostSeo::select('primary_keyword')->whereNotNull('primary_keyword')->where('primary_keyword', '!=', '')->groupBy('primary_keyword')->havingRaw('count(*) > 1')->pluck('primary_keyword');
        $cannibalizationCount = count($keywordCounts);

        if ($request->filled('filter')) {
            $filter = $request->input('filter');
            if ($filter === 'critical') {
                $query->whereHas('seo', fn($q) => $q->where('seo_status', 'critical'));
            } elseif ($filter === 'needs_review') {
                $query->whereHas('seo', fn($q) => $q->where('seo_status', 'needs_review'));
            } elseif ($filter === 'good') {
                $query->whereHas('seo', fn($q) => $q->where('seo_status', 'good'));
            } elseif ($filter === 'orphan') {
                $orphanSlugs = array_keys(array_filter($linkMetrics['inlinksMap'], fn($inlinks) => count($inlinks) === 0));
                $query->whereIn('slug', $orphanSlugs);
            } elseif ($filter === 'missing_meta') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('seo')
                      ->orWhereHas('seo', fn($sq) => $sq->whereNull('meta_description')->orWhere('meta_description', ''));
                });
            }
        }

        $perPage = (int) $request->input('per_page', 10);
        if ($perPage < 5 || $perPage > 100) {
            $perPage = 10;
        }

        $posts = $query->latest('updated_at')->paginate($perPage)->withQueryString();

        // Attach computed link metrics to paginated posts
        foreach ($posts as $p) {
            $inlinks = $linkMetrics['inlinksMap'][$p->slug] ?? [];
            $p->incoming_links_count = count($inlinks);
            $p->is_orphan = ($p->incoming_links_count === 0);
            $p->freshness = SeoFreshnessService::evaluate($p);
        }

        $categories = Category::all();

        return view('admin.seo.index', compact(
            'posts',
            'categories',
            'perPage',
            'totalPosts',
            'avgScore',
            'criticalCount',
            'warningCount',
            'missingTitlesCount',
            'missingDescriptionsCount',
            'orphanCount',
            'staleCount',
            'duplicateTitlesCount',
            'cannibalizationCount'
        ));
    }

    /**
     * Run full batch SEO audit on all published articles
     */
    public function runBatchAudit()
    {
        $posts = Post::with('seo')->get();
        $auditedCount = 0;

        foreach ($posts as $post) {
            $seo = $post->seo_data;

            $data = [
                'title' => $post->title,
                'slug' => $post->slug,
                'content' => $post->content,
                'excerpt' => $post->excerpt,
                'seo_title' => $seo->seo_title,
                'meta_description' => $seo->meta_description,
                'primary_keyword' => $seo->primary_keyword,
                'search_intent' => $seo->search_intent,
                'canonical_url' => $seo->canonical_url,
                'robots_index' => $seo->robots_index,
                'robots_follow' => $seo->robots_follow,
                'include_in_sitemap' => $seo->include_in_sitemap,
                'og_title' => $seo->og_title,
                'og_description' => $seo->og_description,
                'og_image' => $seo->og_image,
                'twitter_card' => $seo->twitter_card,
                'schema_type' => $seo->schema_type,
                'has_featured_image' => !empty($post->featured_image),
            ];

            $audit = SeoAuditService::audit($data, $post->id);

            // Update PostSeo record
            $postSeo = PostSeo::updateOrCreate(
                ['post_id' => $post->id],
                [
                    'seo_score' => $audit['total_score'],
                    'seo_status' => $audit['status'],
                    'last_audited_at' => now(),
                ]
            );

            // Log snapshot in seo_audits
            SeoAudit::create([
                'post_id' => $post->id,
                'score' => $audit['total_score'],
                'status' => $audit['status'],
                'results_json' => $audit,
                'audited_by' => auth()->id(),
                'audited_at' => now(),
            ]);

            $auditedCount++;
        }

        return back()->with('success', "Completed automated SEO audit across {$auditedCount} articles!");
    }

    /**
     * 301 Redirects Manager View
     */
    public function redirects(Request $request)
    {
        $query = SeoRedirect::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('old_url', 'like', "%{$search}%")
                  ->orWhere('new_url', 'like', "%{$search}%");
            });
        }

        $redirects = $query->latest()->paginate(15)->withQueryString();
        $totalHits = SeoRedirect::sum('hits');

        return view('admin.seo.redirects', compact('redirects', 'totalHits'));
    }

    /**
     * Store a manual 301 redirect
     */
    public function storeRedirect(Request $request)
    {
        $validated = $request->validate([
            'old_url' => 'required|string|max:255|unique:seo_redirects,old_url',
            'new_url' => 'required|string|max:255',
            'status_code' => 'required|in:301,302',
        ]);

        $oldUrl = '/' . ltrim($validated['old_url'], '/');
        $newUrl = str_starts_with($validated['new_url'], 'http') ? $validated['new_url'] : '/' . ltrim($validated['new_url'], '/');

        SeoRedirect::create([
            'old_url' => $oldUrl,
            'new_url' => $newUrl,
            'status_code' => (int) $validated['status_code'],
        ]);

        return back()->with('success', "Redirect rule from '{$oldUrl}' to '{$newUrl}' added successfully!");
    }

    /**
     * Delete a 301 redirect
     */
    public function destroyRedirect($id)
    {
        $redirect = SeoRedirect::findOrFail($id);
        $redirect->delete();

        return back()->with('success', 'Redirect rule deleted successfully.');
    }

    /**
     * Dedicated On-Page SEO Grader View
     */
    public function grader(Request $request)
    {
        $posts = Post::with(['category', 'seo'])->latest()->get();
        $selectedPostId = $request->input('post_id');
        $selectedPost = null;
        $gradeResult = null;

        if ($selectedPostId) {
            $selectedPost = Post::with(['category', 'seo', 'author'])->find($selectedPostId);
            if ($selectedPost) {
                $seo = $selectedPost->seo_data;
                $gradeResult = \App\Services\Seo\SeoGraderService::grade([
                    'title' => $selectedPost->title,
                    'slug' => $selectedPost->slug,
                    'content' => $selectedPost->content,
                    'excerpt' => $selectedPost->excerpt,
                    'seo_title' => $seo->seo_title,
                    'meta_description' => $seo->meta_description,
                    'primary_keyword' => $seo->primary_keyword,
                    'search_intent' => $seo->search_intent,
                    'canonical_url' => $seo->canonical_url,
                    'robots_index' => $seo->robots_index,
                    'robots_follow' => $seo->robots_follow,
                    'include_in_sitemap' => $seo->include_in_sitemap,
                    'og_title' => $seo->og_title,
                    'og_description' => $seo->og_description,
                    'og_image' => $seo->og_image,
                    'twitter_card' => $seo->twitter_card,
                    'schema_type' => $seo->schema_type,
                    'has_featured_image' => !empty($selectedPost->featured_image),
                ], $selectedPost->id);
            }
        } elseif ($posts->isNotEmpty()) {
            // Default grade the latest post
            $selectedPost = $posts->first();
            $seo = $selectedPost->seo_data;
            $gradeResult = \App\Services\Seo\SeoGraderService::grade([
                'title' => $selectedPost->title,
                'slug' => $selectedPost->slug,
                'content' => $selectedPost->content,
                'excerpt' => $selectedPost->excerpt,
                'seo_title' => $seo->seo_title,
                'meta_description' => $seo->meta_description,
                'primary_keyword' => $seo->primary_keyword,
                'search_intent' => $seo->search_intent,
                'canonical_url' => $seo->canonical_url,
                'robots_index' => $seo->robots_index,
                'robots_follow' => $seo->robots_follow,
                'include_in_sitemap' => $seo->include_in_sitemap,
                'og_title' => $seo->og_title,
                'og_description' => $seo->og_description,
                'og_image' => $seo->og_image,
                'twitter_card' => $seo->twitter_card,
                'schema_type' => $seo->schema_type,
                'has_featured_image' => !empty($selectedPost->featured_image),
            ], $selectedPost->id);
        }

        return view('admin.seo.grader', compact('posts', 'selectedPost', 'gradeResult'));
    }

    /**
     * AJAX on-demand grading endpoint for custom text or article draft
     */
    public function runGrader(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'slug' => 'nullable|string|max:255',
            'seo_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'primary_keyword' => 'nullable|string|max:255',
            'search_intent' => 'nullable|string|in:informational,commercial,transactional,navigational',
            'canonical_url' => 'nullable|url|max:500',
            'robots_index' => 'nullable|boolean',
            'robots_follow' => 'nullable|boolean',
            'include_in_sitemap' => 'nullable|boolean',
            'schema_type' => 'nullable|string|max:50',
            'post_id' => 'nullable|integer',
        ]);

        $result = \App\Services\Seo\SeoGraderService::grade($validated, $validated['post_id'] ?? null);

        return response()->json([
            'success' => true,
            'result' => $result,
        ]);
    }

    /**
     * Server Config & SEO Files Manager (.htaccess & robots.txt)
     */
    public function serverConfig(Request $request)
    {
        $robotsPath = public_path('robots.txt');
        $htaccessPath = public_path('.htaccess');
        $robotsBackupPath = public_path('robots.txt.backup');
        $htaccessBackupPath = public_path('.htaccess.backup');

        $robotsContent = file_exists($robotsPath) ? file_get_contents($robotsPath) : "User-agent: *\nDisallow:\n\nSitemap: " . url('/sitemap.xml');
        $htaccessContent = file_exists($htaccessPath) ? file_get_contents($htaccessPath) : '';

        $robotsWritable = is_writable($robotsPath) || (!file_exists($robotsPath) && is_writable(public_path()));
        $htaccessWritable = is_writable($htaccessPath) || (!file_exists($htaccessPath) && is_writable(public_path()));

        $robotsSize = file_exists($robotsPath) ? filesize($robotsPath) : 0;
        $htaccessSize = file_exists($htaccessPath) ? filesize($htaccessPath) : 0;

        $robotsModified = file_exists($robotsPath) ? date('Y-m-d H:i:s', filemtime($robotsPath)) : 'N/A';
        $htaccessModified = file_exists($htaccessPath) ? date('Y-m-d H:i:s', filemtime($htaccessPath)) : 'N/A';

        $hasRobotsBackup = file_exists($robotsBackupPath);
        $hasHtaccessBackup = file_exists($htaccessBackupPath);

        return view('admin.seo.server_config', compact(
            'robotsContent',
            'htaccessContent',
            'robotsWritable',
            'htaccessWritable',
            'robotsSize',
            'htaccessSize',
            'robotsModified',
            'htaccessModified',
            'hasRobotsBackup',
            'hasHtaccessBackup'
        ));
    }

    /**
     * Update robots.txt
     */
    public function updateRobots(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $robotsPath = public_path('robots.txt');
        $robotsBackupPath = public_path('robots.txt.backup');

        if (file_exists($robotsPath)) {
            copy($robotsPath, $robotsBackupPath);
        }

        file_put_contents($robotsPath, $request->input('content'));

        return back()->with('success', 'robots.txt updated successfully! Backup saved to robots.txt.backup.');
    }

    /**
     * Update .htaccess
     */
    public function updateHtaccess(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $content = $request->input('content');

        // Basic sanity checks
        if (substr_count($content, '<IfModule') !== substr_count($content, '</IfModule>')) {
            return back()->with('error', 'Warning: .htaccess contains unmatched <IfModule> and </IfModule> tags. Changes were not saved to prevent server error 500.')->withInput();
        }

        $htaccessPath = public_path('.htaccess');
        $htaccessBackupPath = public_path('.htaccess.backup');

        if (file_exists($htaccessPath)) {
            copy($htaccessPath, $htaccessBackupPath);
        }

        file_put_contents($htaccessPath, $content);

        return back()->with('success', '.htaccess updated successfully! Pre-save backup saved to .htaccess.backup.');
    }

    /**
     * Restore robots.txt from backup or default
     */
    public function restoreRobots(Request $request)
    {
        $robotsPath = public_path('robots.txt');
        $robotsBackupPath = public_path('robots.txt.backup');

        if (file_exists($robotsBackupPath)) {
            copy($robotsBackupPath, $robotsPath);
            return back()->with('success', 'robots.txt successfully restored from previous backup.');
        }

        $defaultRobots = "User-agent: *\nDisallow:\n\nSitemap: " . url('/sitemap.xml') . "\n";
        file_put_contents($robotsPath, $defaultRobots);

        return back()->with('success', 'robots.txt restored to recommended default configuration.');
    }

    /**
     * Restore .htaccess from backup or Laravel default
     */
    public function restoreHtaccess(Request $request)
    {
        $htaccessPath = public_path('.htaccess');
        $htaccessBackupPath = public_path('.htaccess.backup');

        if (file_exists($htaccessBackupPath)) {
            copy($htaccessBackupPath, $htaccessPath);
            return back()->with('success', '.htaccess successfully restored from previous backup.');
        }

        $defaultHtaccess = "<IfModule mod_rewrite.c>\n    <IfModule mod_negotiation.c>\n        Options -MultiViews -Indexes\n    </IfModule>\n\n    RewriteEngine On\n\n    # Handle Authorization Header\n    RewriteCond %{HTTP:Authorization} .\n    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]\n\n    # Handle X-XSRF-Token Header\n    RewriteCond %{HTTP:x-xsrf-token} .\n    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]\n\n    # Redirect Trailing Slashes If Not A Folder...\n    RewriteCond %{REQUEST_FILENAME} !-d\n    RewriteCond %{REQUEST_URI} (.+)/$\n    RewriteRule ^ %1 [L,R=301]\n\n    # Send Requests To Front Controller...\n    RewriteCond %{REQUEST_FILENAME} !-d\n    RewriteCond %{REQUEST_FILENAME} !-f\n    RewriteRule ^ index.php [L]\n</IfModule>\n";
        file_put_contents($htaccessPath, $defaultHtaccess);

        return back()->with('success', '.htaccess restored to clean Laravel default configuration.');
    }

    /**
     * Clear SEO & Application Caches
     */
    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');

        try {
            switch ($type) {
                case 'views':
                    Artisan::call('view:clear');
                    $msg = 'Blade views and compiled templates cache cleared successfully!';
                    break;

                case 'routes':
                    Artisan::call('route:clear');
                    $msg = 'Route cache cleared successfully!';
                    break;

                case 'config':
                    Artisan::call('config:clear');
                    $msg = 'Configuration cache cleared successfully!';
                    break;

                case 'application':
                    Artisan::call('cache:clear');
                    Cache::flush();
                    $msg = 'Application data and query cache cleared successfully!';
                    break;

                case 'all':
                default:
                    Artisan::call('optimize:clear');
                    Cache::flush();
                    $msg = 'All System Caches (Views, Routes, Config, Application Data, and SEO Caches) purged successfully!';
                    break;
            }

            if ($request->isMethod('get') || !url()->previous() || str_contains(url()->previous(), 'clear-cache')) {
                return redirect()->route('admin.seo.index')->with('success', $msg);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            if ($request->isMethod('get') || !url()->previous() || str_contains(url()->previous(), 'clear-cache')) {
                return redirect()->route('admin.seo.index')->with('error', 'Cache clear error: ' . $e->getMessage());
            }
            return back()->with('error', 'Cache clear error: ' . $e->getMessage());
        }
    }

    /**
     * Re-optimize System for Production Speed
     */
    public function optimizeSystem(Request $request)
    {
        try {
            if (!app()->environment('testing')) {
                Artisan::call('optimize');
            } else {
                Artisan::call('view:cache');
            }
            $msg = 'System optimized successfully! Configuration and route caching generated for maximum speed and SEO performance.';
            
            if ($request->isMethod('get') || !url()->previous() || str_contains(url()->previous(), 'optimize')) {
                return redirect()->route('admin.seo.index')->with('success', $msg);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            if ($request->isMethod('get') || !url()->previous() || str_contains(url()->previous(), 'optimize')) {
                return redirect()->route('admin.seo.index')->with('error', 'Optimization error: ' . $e->getMessage());
            }
            return back()->with('error', 'Optimization error: ' . $e->getMessage());
        }
    }
}
