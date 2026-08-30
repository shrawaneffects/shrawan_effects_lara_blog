<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\PostSeo;
use App\Models\PostSeoKeyword;
use App\Models\PostSeoEntity;
use App\Models\SeoAudit;
use App\Services\Seo\SeoAuditService;
use App\Services\Seo\SeoRedirectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['author', 'category', 'tags']);

        // Author filter if author role
        if (auth()->user()->role === 'author') {
            $query->where('user_id', auth()->id());
        } elseif ($request->filled('author')) {
            $query->where('user_id', $request->input('author'));
        }

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Compute linking metrics across all posts
        $allPostsForMetrics = Post::select('id', 'title', 'slug', 'content')->get();
        $metrics = Post::computeLinkMetrics($allPostsForMetrics);

        if ($request->filled('link_status')) {
            $linkStatus = $request->input('link_status');
            if ($linkStatus === 'orphan') {
                $orphanSlugs = array_keys(array_filter($metrics['inlinksMap'], fn($inlinks) => count($inlinks) === 0));
                $query->whereIn('slug', $orphanSlugs);
            } elseif ($linkStatus === 'inlinked') {
                $linkedSlugs = array_keys(array_filter($metrics['inlinksMap'], fn($inlinks) => count($inlinks) > 0));
                $query->whereIn('slug', $linkedSlugs);
            } elseif ($linkStatus === 'no_outlinks') {
                $noOutIds = array_keys(array_filter($metrics['outgoingCounts'], fn($out) => $out['total'] === 0));
                $query->whereIn('id', $noOutIds);
            }
        }

        $posts = $query->latest()->paginate(10)->withQueryString();

        // Attach computed metrics to paginated items
        foreach ($posts as $p) {
            $incomingList = $metrics['inlinksMap'][$p->slug] ?? [];
            $p->incoming_links = $incomingList;
            $p->incoming_links_count = count($incomingList);

            $outData = $metrics['outgoingCounts'][$p->id] ?? ['total' => 0, 'internal' => 0, 'external' => 0];
            $p->outgoing_links_count = $outData['total'];
            $p->outgoing_internal_count = $outData['internal'];
            $p->outgoing_external_count = $outData['external'];

            $p->is_orphan = ($p->incoming_links_count === 0);
        }

        $categories = Category::all();
        $totalArticlesCount = $metrics['totalPosts'];
        $orphanArticlesCount = $metrics['orphanCount'];
        $linkedArticlesCount = $totalArticlesCount - $orphanArticlesCount;

        return view('admin.posts.index', compact(
            'posts',
            'categories',
            'totalArticlesCount',
            'orphanArticlesCount',
            'linkedArticlesCount'
        ));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'category_id' => 'nullable|exists:categories,id',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_url' => 'nullable|url|max:500',
            'status' => 'required|in:draft,published,scheduled',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'enable_toc' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'schema_type' => 'nullable|string|max:50',
            'custom_schema' => 'nullable|string',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'nullable|string|max:500',
            'faqs.*.answer' => 'nullable|string|max:2000',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'new_tags' => 'nullable|string',
            // SEO Fields
            'seo_title' => 'nullable|string|max:255',
            'primary_keyword' => 'nullable|string|max:150',
            'search_intent' => 'nullable|in:informational,commercial,transactional,navigational',
            'canonical_url' => 'nullable|url|max:500',
            'robots_index' => 'nullable|boolean',
            'robots_follow' => 'nullable|boolean',
            'include_in_sitemap' => 'nullable|boolean',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|string|max:500',
            'twitter_card' => 'nullable|string|max:50',
            'secondary_keywords' => 'nullable|string',
            'seo_entities' => 'nullable|string',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (Post::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $counter++;
        }

        $post = new Post();
        $post->user_id = auth()->id();
        $post->category_id = $validated['category_id'] ?? null;
        $post->title = $validated['title'];
        $post->slug = $uniqueSlug;
        $post->excerpt = $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 150);
        $post->content = $validated['content'];
        $post->status = $validated['status'];
        $post->is_featured = $request->boolean('is_featured');
        $post->is_trending = $request->boolean('is_trending');
        $post->enable_toc = $request->has('enable_toc') ? $request->boolean('enable_toc') : true;
        $post->meta_title = $validated['seo_title'] ?? ($validated['meta_title'] ?? $validated['title']);
        $post->meta_description = $validated['meta_description'] ?? $post->excerpt;
        $post->meta_keywords = $validated['meta_keywords'] ?? null;
        $post->schema_type = $validated['schema_type'] ?? 'BlogPosting';
        $post->custom_schema = $validated['custom_schema'] ?? null;

        // Clean FAQs
        $faqs = [];
        if ($request->has('faqs') && is_array($request->input('faqs'))) {
            foreach ($request->input('faqs') as $faq) {
                if (!empty(trim($faq['question'] ?? '')) && !empty(trim($faq['answer'] ?? ''))) {
                    $faqs[] = [
                        'question' => trim($faq['question']),
                        'answer' => trim($faq['answer']),
                    ];
                }
            }
        }
        $post->faqs = count($faqs) > 0 ? $faqs : null;

        $post->reading_time = Post::calculateReadingTime($validated['content']);

        if ($post->status === 'published') {
            $post->published_at = now();
        }

        // Image Handling
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('posts', 'public');
            $post->featured_image = $path;
        } elseif (!empty($validated['image_url'])) {
            $post->featured_image = $validated['image_url'];
        }

        $post->save();

        // Handle Tags
        $tagIds = $validated['tags'] ?? [];
        if (!empty($validated['new_tags'])) {
            $newTagNames = explode(',', $validated['new_tags']);
            foreach ($newTagNames as $nTag) {
                $nTag = trim($nTag);
                if ($nTag !== '') {
                    $tagObj = Tag::firstOrCreate(
                        ['slug' => Str::slug($nTag)],
                        ['name' => $nTag]
                    );
                    $tagIds[] = $tagObj->id;
                }
            }
        }
        $post->tags()->sync(array_unique($tagIds));

        // Sync SEO Architecture
        $this->syncPostSeo($post, $request);

        return redirect()->route('admin.posts.index')->with('success', 'Article created and SEO metadata analyzed successfully!');
    }

    public function edit($id)
    {
        $post = Post::with(['tags', 'seo.keywords', 'seo.entities'])->findOrFail($id);

        if (auth()->user()->role === 'author' && $post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized. You can only edit your own articles.');
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $oldSlug = $post->slug;

        if (auth()->user()->role === 'author' && $post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized. You can only update your own articles.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('posts', 'slug')->ignore($post->id)],
            'category_id' => 'nullable|exists:categories,id',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_url' => 'nullable|url|max:500',
            'status' => 'required|in:draft,published,scheduled',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'enable_toc' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'schema_type' => 'nullable|string|max:50',
            'custom_schema' => 'nullable|string',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'nullable|string|max:500',
            'faqs.*.answer' => 'nullable|string|max:2000',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'new_tags' => 'nullable|string',
            // SEO Fields
            'seo_title' => 'nullable|string|max:255',
            'primary_keyword' => 'nullable|string|max:150',
            'search_intent' => 'nullable|in:informational,commercial,transactional,navigational',
            'canonical_url' => 'nullable|url|max:500',
            'robots_index' => 'nullable|boolean',
            'robots_follow' => 'nullable|boolean',
            'include_in_sitemap' => 'nullable|boolean',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|string|max:500',
            'twitter_card' => 'nullable|string|max:50',
            'secondary_keywords' => 'nullable|string',
            'seo_entities' => 'nullable|string',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (Post::where('slug', $uniqueSlug)->where('id', '!=', $post->id)->exists()) {
            $uniqueSlug = $slug . '-' . $counter++;
        }

        // Detect slug change and automatically create 301 redirect
        if ($post->status === 'published' && !empty($oldSlug) && $oldSlug !== $uniqueSlug) {
            SeoRedirectService::createSlugRedirect($oldSlug, $uniqueSlug);
        }

        $post->category_id = $validated['category_id'] ?? null;
        $post->title = $validated['title'];
        $post->slug = $uniqueSlug;
        $post->excerpt = $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 150);
        $post->content = $validated['content'];
        $post->status = $validated['status'];
        $post->is_featured = $request->boolean('is_featured');
        $post->is_trending = $request->boolean('is_trending');
        $post->enable_toc = $request->has('enable_toc') ? $request->boolean('enable_toc') : false;
        $post->meta_title = $validated['seo_title'] ?? ($validated['meta_title'] ?? $validated['title']);
        $post->meta_description = $validated['meta_description'] ?? $post->excerpt;
        $post->meta_keywords = $validated['meta_keywords'] ?? null;
        $post->schema_type = $validated['schema_type'] ?? 'BlogPosting';
        $post->custom_schema = $validated['custom_schema'] ?? null;

        // Clean FAQs
        $faqs = [];
        if ($request->has('faqs') && is_array($request->input('faqs'))) {
            foreach ($request->input('faqs') as $faq) {
                if (!empty(trim($faq['question'] ?? '')) && !empty(trim($faq['answer'] ?? ''))) {
                    $faqs[] = [
                        'question' => trim($faq['question']),
                        'answer' => trim($faq['answer']),
                    ];
                }
            }
        }
        $post->faqs = count($faqs) > 0 ? $faqs : null;

        $post->reading_time = Post::calculateReadingTime($validated['content']);

        if ($post->status === 'published' && !$post->published_at) {
            $post->published_at = now();
        }

        // Image Handling
        if ($request->hasFile('featured_image')) {
            if ($post->featured_image && !str_starts_with($post->featured_image, 'http') && Storage::disk('public')->exists($post->featured_image)) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $path = $request->file('featured_image')->store('posts', 'public');
            $post->featured_image = $path;
        } elseif (!empty($validated['image_url'])) {
            $post->featured_image = $validated['image_url'];
        }

        $post->save();

        // Handle Tags
        $tagIds = $validated['tags'] ?? [];
        if (!empty($validated['new_tags'])) {
            $newTagNames = explode(',', $validated['new_tags']);
            foreach ($newTagNames as $nTag) {
                $nTag = trim($nTag);
                if ($nTag !== '') {
                    $tagObj = Tag::firstOrCreate(
                        ['slug' => Str::slug($nTag)],
                        ['name' => $nTag]
                    );
                    $tagIds[] = $tagObj->id;
                }
            }
        }
        $post->tags()->sync(array_unique($tagIds));

        // Sync SEO Architecture
        $this->syncPostSeo($post, $request);

        return redirect()->route('admin.posts.index')->with('success', 'Article and SEO audit updated successfully!');
    }

    /**
     * Synchronize PostSeo model, keywords, entities, and execute SEO audit snapshot
     */
    protected function syncPostSeo(Post $post, Request $request): PostSeo
    {
        $seoTitle = $request->input('seo_title', $post->title);
        $metaDescription = $request->input('meta_description', $post->excerpt);
        $primaryKeyword = $request->input('primary_keyword');
        $searchIntent = $request->input('search_intent', 'informational');
        $canonicalUrl = $request->input('canonical_url');
        $robotsIndex = $request->has('robots_index') ? $request->boolean('robots_index') : true;
        $robotsFollow = $request->has('robots_follow') ? $request->boolean('robots_follow') : true;
        $includeInSitemap = $request->has('include_in_sitemap') ? $request->boolean('include_in_sitemap') : true;
        $ogTitle = $request->input('og_title', $seoTitle);
        $ogDescription = $request->input('og_description', $metaDescription);
        $ogImage = $request->input('og_image', $post->featured_image);
        $twitterTitle = $request->input('twitter_title', $seoTitle);
        $twitterDescription = $request->input('twitter_description', $metaDescription);
        $twitterImage = $request->input('twitter_image', $ogImage);
        $twitterCard = $request->input('twitter_card', 'summary_large_image');
        $schemaType = $request->input('schema_type', 'BlogPosting');

        // Run authoritative server-side SEO Audit
        $auditData = [
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
            'excerpt' => $post->excerpt,
            'seo_title' => $seoTitle,
            'meta_description' => $metaDescription,
            'primary_keyword' => $primaryKeyword,
            'search_intent' => $searchIntent,
            'canonical_url' => $canonicalUrl,
            'robots_index' => $robotsIndex,
            'robots_follow' => $robotsFollow,
            'include_in_sitemap' => $includeInSitemap,
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'og_image' => $ogImage,
            'twitter_card' => $twitterCard,
            'schema_type' => $schemaType,
            'has_featured_image' => !empty($post->featured_image),
        ];

        $audit = SeoAuditService::audit($auditData, $post->id);

        $postSeo = PostSeo::updateOrCreate(
            ['post_id' => $post->id],
            [
                'seo_title' => $seoTitle,
                'meta_description' => $metaDescription,
                'primary_keyword' => $primaryKeyword,
                'search_intent' => $searchIntent,
                'canonical_url' => $canonicalUrl,
                'robots_index' => $robotsIndex,
                'robots_follow' => $robotsFollow,
                'include_in_sitemap' => $includeInSitemap,
                'og_title' => $ogTitle,
                'og_description' => $ogDescription,
                'og_image' => $ogImage,
                'twitter_title' => $twitterTitle,
                'twitter_description' => $twitterDescription,
                'twitter_image' => $twitterImage,
                'twitter_card' => $twitterCard,
                'schema_type' => $schemaType,
                'seo_score' => $audit['total_score'],
                'seo_status' => $audit['status'],
                'last_audited_at' => now(),
            ]
        );

        // Record Audit Snapshot
        SeoAudit::create([
            'post_id' => $post->id,
            'score' => $audit['total_score'],
            'status' => $audit['status'],
            'results_json' => $audit,
            'audited_by' => auth()->id(),
            'audited_at' => now(),
        ]);

        // Sync Secondary Keywords
        if ($request->filled('secondary_keywords')) {
            $postSeo->keywords()->where('type', 'secondary')->delete();
            $secKeywords = explode(',', $request->input('secondary_keywords'));
            foreach ($secKeywords as $sk) {
                $sk = trim($sk);
                if (!empty($sk)) {
                    PostSeoKeyword::create([
                        'post_seo_id' => $postSeo->id,
                        'keyword' => $sk,
                        'type' => 'secondary',
                    ]);
                }
            }
        }

        // Sync Named Entities
        if ($request->filled('seo_entities')) {
            $postSeo->entities()->delete();
            $entities = explode(',', $request->input('seo_entities'));
            foreach ($entities as $ent) {
                $ent = trim($ent);
                if (!empty($ent)) {
                    PostSeoEntity::create([
                        'post_seo_id' => $postSeo->id,
                        'entity' => $ent,
                        'type' => 'General',
                    ]);
                }
            }
        }

        return $postSeo;
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if (auth()->user()->role === 'author' && $post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized. You can only delete your own articles.');
        }

        if ($post->featured_image && !str_starts_with($post->featured_image, 'http') && Storage::disk('public')->exists($post->featured_image)) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Article deleted successfully!');
    }

    public function toggleFeatured($id)
    {
        $post = Post::findOrFail($id);
        $post->is_featured = !$post->is_featured;
        $post->save();

        return redirect()->back()->with('success', 'Featured status updated!');
    }

    public function toggleTrending($id)
    {
        $post = Post::findOrFail($id);
        $post->is_trending = !$post->is_trending;
        $post->save();

        return redirect()->back()->with('success', 'Trending status updated!');
    }
}
