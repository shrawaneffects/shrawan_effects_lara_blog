<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ToolController extends Controller
{
    /**
     * Default catalog of built-in web tools used for bootstrapping.
     */
    public const DEFAULT_TOOLS = [
        [
            'title' => 'Convert Case (Online Text Case Converter)',
            'slug' => 'convertcase',
            'url' => '/services/convertcase',
            'route_name' => 'services.convertcase',
            'type' => 'frontend',
            'category' => 'Text & Content Utilities',
            'icon' => 'bi-type',
            'badge' => 'NEW',
            'summary' => 'Instantly transforms text into Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, and InVeRsE CaSe with live character and word counters.',
            'enable_toc' => true,
            'is_active' => true,
            'is_featured' => true,
            'order' => 1,
        ],
        [
            'title' => 'Bulk Website IP Checker & Subnet Analyzer',
            'slug' => 'bulk-website-ip-checker',
            'url' => '/services/bulk-website-ip-checker',
            'route_name' => 'services.bulk-ip-checker',
            'type' => 'frontend',
            'category' => 'Network & Infrastructure',
            'icon' => 'bi-hdd-network',
            'badge' => 'POPULAR',
            'summary' => 'Asynchronously resolves up to 10,000+ domains into IPv4/IPv6, classifies IP types (CDN, Cloud, Datacenter, ISP), and detects same-IP and Class-C shared hosting footprints.',
            'enable_toc' => true,
            'is_active' => true,
            'is_featured' => true,
            'order' => 2,
        ],
        [
            'title' => 'On-Page SEO Grader',
            'slug' => 'seo-grader',
            'url' => '/admin/seo/grader',
            'route_name' => 'admin.seo.grader',
            'type' => 'admin',
            'category' => 'SEO & Audit',
            'icon' => 'bi-award-fill',
            'badge' => 'PRO',
            'summary' => 'Real-time On-Page SEO analyzer, assessing title tags, heading structure, word counts, and search snippet rendering.',
            'enable_toc' => true,
            'is_active' => true,
            'is_featured' => false,
            'order' => 3,
        ],
        [
            'title' => 'Robots.txt & .htaccess Server Config',
            'slug' => 'server-config',
            'url' => '/admin/seo/server-config',
            'route_name' => 'admin.seo.server-config',
            'type' => 'admin',
            'category' => 'Server & Directives',
            'icon' => 'bi-sliders',
            'badge' => 'CORE',
            'summary' => 'Live editor and syntax validator for server directives, crawler crawl-delay configurations, and .htaccess rules.',
            'enable_toc' => true,
            'is_active' => true,
            'is_featured' => false,
            'order' => 4,
        ],
        [
            'title' => '301 Permanent Redirects Engine',
            'slug' => 'redirects-manager',
            'url' => '/admin/seo/redirects',
            'route_name' => 'admin.seo.redirects',
            'type' => 'admin',
            'category' => 'SEO & Routing',
            'icon' => 'bi-signpost-split',
            'badge' => 'CORE',
            'summary' => 'Creates, tests, and validates automated 301 and 302 HTTP URL forwarding rules to eliminate 404 crawl errors.',
            'enable_toc' => true,
            'is_active' => true,
            'is_featured' => false,
            'order' => 5,
        ],
        [
            'title' => 'Technical Site Crawler & Auditor',
            'slug' => 'openseo-audit',
            'url' => '/openseo/audit',
            'route_name' => 'openseo.audit',
            'type' => 'admin',
            'category' => 'SEO & Audit',
            'icon' => 'bi-search',
            'badge' => 'SUITE',
            'summary' => 'Full website recursive crawler scanning for broken internal links, duplicate titles, missing alt tags, and slow TTFB pages.',
            'enable_toc' => true,
            'is_active' => true,
            'is_featured' => false,
            'order' => 6,
        ],
    ];

    /**
     * Ensure database table is populated with default tools if empty.
     */
    protected function ensureToolsSeeded(): void
    {
        if (!Schema::hasTable('tools')) {
            return;
        }

        foreach (self::DEFAULT_TOOLS as $default) {
            $tool = Tool::where('slug', $default['slug'])->first();
            if (!$tool) {
                Tool::create($default);
            }
        }
    }

    /**
     * List all tools in the admin panel.
     */
    public function index(Request $request): View
    {
        $this->ensureToolsSeeded();

        $query = Tool::query();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $tools = $query->orderBy('order', 'asc')->orderBy('id', 'asc')->paginate(15)->withQueryString();

        $totalTools = Tool::count();
        $activeTools = Tool::where('is_active', true)->count();
        $frontendTools = Tool::where('type', 'frontend')->count();
        $adminTools = Tool::where('type', 'admin')->count();

        $categories = Tool::whereNotNull('category')->distinct()->pluck('category')->filter()->values();

        return view('admin.tools.index', [
            'tools' => $tools,
            'totalTools' => $totalTools,
            'activeTools' => $activeTools,
            'frontendTools' => $frontendTools,
            'adminTools' => $adminTools,
            'categories' => $categories,
            'search' => $request->input('search'),
            'selectedCategory' => $request->input('category'),
            'selectedStatus' => $request->input('status'),
        ]);
    }

    /**
     * Show form to create a new tool.
     */
    public function create(): View
    {
        $this->ensureToolsSeeded();

        $categories = Tool::whereNotNull('category')->distinct()->pluck('category')->filter()->values();
        $allTools = Tool::active()->orderBy('title')->get(['id', 'slug', 'title', 'category']);
        $allPosts = class_exists(Post::class)
            ? Post::published()->latest('published_at')->limit(30)->get(['id', 'title', 'slug'])
            : collect();

        return view('admin.tools.create', [
            'categories' => $categories,
            'allTools' => $allTools,
            'allPosts' => $allPosts,
        ]);
    }

    /**
     * Store a newly created tool.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tools,slug',
            'url' => 'nullable|string|max:255',
            'route_name' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:100',
            'badge' => 'nullable|string|max:50',
            'summary' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'enable_toc' => 'nullable|boolean',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'nullable|string|max:500',
            'faqs.*.answer' => 'nullable|string|max:2000',
            'related_tools' => 'nullable|array',
            'related_posts' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:500',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'type' => 'nullable|string|max:30',
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Tool::generateUniqueSlug($validated['title']);

        // Clean FAQs
        $faqs = [];
        if (!empty($validated['faqs']) && is_array($validated['faqs'])) {
            foreach ($validated['faqs'] as $faq) {
                $q = trim($faq['question'] ?? '');
                $a = trim($faq['answer'] ?? '');
                if ($q !== '' && $a !== '') {
                    $faqs[] = [
                        'question' => $q,
                        'answer' => $a,
                    ];
                }
            }
        }

        $tool = new Tool();
        $tool->title = $validated['title'];
        $tool->slug = $slug;
        $tool->url = $validated['url'] ?? ('/services/' . $slug);
        $tool->route_name = $validated['route_name'] ?? null;
        $tool->category = $validated['category'] ?? 'Utilities';
        $tool->icon = $validated['icon'] ?? 'bi-tools';
        $tool->badge = $validated['badge'] ?? null;
        $tool->summary = $validated['summary'] ?? null;
        $tool->content = $validated['content'] ?? null;
        $tool->enable_toc = $request->boolean('enable_toc', true);
        $tool->faqs = $faqs;
        $tool->related_tools = array_values(array_filter($validated['related_tools'] ?? []));
        $tool->related_posts = array_values(array_filter($validated['related_posts'] ?? []));
        $tool->meta_title = $validated['meta_title'] ?? null;
        $tool->meta_description = $validated['meta_description'] ?? null;
        $tool->meta_keywords = $validated['meta_keywords'] ?? null;
        $tool->canonical_url = $validated['canonical_url'] ?? null;
        $tool->is_active = $request->boolean('is_active', true);
        $tool->is_featured = $request->boolean('is_featured', false);
        $tool->order = (int) ($validated['order'] ?? 0);
        $tool->type = $validated['type'] ?? 'frontend';
        $tool->save();

        if (class_exists(AuditLog::class)) {
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'create_tool',
                    'description' => "Created tool '{$tool->title}'",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Throwable $e) {}
        }

        return redirect()->route('admin.tools.index')->with('success', "Tool '{$tool->title}' created successfully.");
    }

    /**
     * Show form to edit an existing tool.
     */
    public function edit(string $id): View
    {
        $this->ensureToolsSeeded();

        $tool = is_numeric($id)
            ? Tool::findOrFail($id)
            : Tool::where('slug', $id)->firstOrFail();

        $categories = Tool::whereNotNull('category')->distinct()->pluck('category')->filter()->values();
        $allTools = Tool::active()->where('id', '!=', $tool->id)->orderBy('title')->get(['id', 'slug', 'title', 'category']);
        $allPosts = class_exists(Post::class)
            ? Post::published()->latest('published_at')->limit(40)->get(['id', 'title', 'slug'])
            : collect();

        return view('admin.tools.edit', [
            'tool' => $tool,
            'categories' => $categories,
            'allTools' => $allTools,
            'allPosts' => $allPosts,
        ]);
    }

    /**
     * Update an existing tool.
     */
    public function update(Request $request, ?string $id = null): RedirectResponse
    {
        $targetId = $id ?? $request->input('id');

        $tool = is_numeric($targetId)
            ? Tool::findOrFail($targetId)
            : Tool::where('slug', $targetId)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tools,slug,' . $tool->id,
            'url' => 'nullable|string|max:255',
            'route_name' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:100',
            'badge' => 'nullable|string|max:50',
            'summary' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:1000', // backward compatibility
            'content' => 'nullable|string',
            'enable_toc' => 'nullable|boolean',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'nullable|string|max:500',
            'faqs.*.answer' => 'nullable|string|max:2000',
            'related_tools' => 'nullable|array',
            'related_posts' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:500',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'type' => 'nullable|string|max:30',
        ]);

        if (!empty($validated['slug'])) {
            $tool->slug = Str::slug($validated['slug']);
        }

        $tool->title = $validated['title'];
        if (isset($validated['url'])) {
            $tool->url = $validated['url'];
        }
        if (isset($validated['route_name'])) {
            $tool->route_name = $validated['route_name'];
        }
        if (isset($validated['category'])) {
            $tool->category = $validated['category'];
        }
        if (isset($validated['icon'])) {
            $tool->icon = $validated['icon'];
        }
        $tool->badge = $validated['badge'] ?? null;
        $tool->summary = $validated['summary'] ?? ($validated['description'] ?? $tool->summary);

        if (array_key_exists('content', $validated)) {
            $tool->content = $validated['content'];
        }

        if ($request->has('enable_toc')) {
            $tool->enable_toc = $request->boolean('enable_toc');
        }

        if ($request->has('faqs')) {
            $faqs = [];
            if (is_array($validated['faqs'])) {
                foreach ($validated['faqs'] as $faq) {
                    $q = trim($faq['question'] ?? '');
                    $a = trim($faq['answer'] ?? '');
                    if ($q !== '' && $a !== '') {
                        $faqs[] = [
                            'question' => $q,
                            'answer' => $a,
                        ];
                    }
                }
            }
            $tool->faqs = $faqs;
        }

        if ($request->has('related_tools')) {
            $tool->related_tools = array_values(array_filter($validated['related_tools'] ?? []));
        }

        if ($request->has('related_posts')) {
            $tool->related_posts = array_values(array_filter($validated['related_posts'] ?? []));
        }

        if (array_key_exists('meta_title', $validated)) {
            $tool->meta_title = $validated['meta_title'];
        }
        if (array_key_exists('meta_description', $validated)) {
            $tool->meta_description = $validated['meta_description'];
        }
        if (array_key_exists('meta_keywords', $validated)) {
            $tool->meta_keywords = $validated['meta_keywords'];
        }
        if (array_key_exists('canonical_url', $validated)) {
            $tool->canonical_url = $validated['canonical_url'];
        }
        if ($request->has('is_active')) {
            $tool->is_active = $request->boolean('is_active');
        }
        if ($request->has('is_featured')) {
            $tool->is_featured = $request->boolean('is_featured');
        }
        if (isset($validated['order'])) {
            $tool->order = (int) $validated['order'];
        }
        if (isset($validated['type'])) {
            $tool->type = $validated['type'];
        }

        $tool->save();

        if (class_exists(AuditLog::class)) {
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'update_tool',
                    'description' => "Updated tool '{$tool->title}'",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Throwable $e) {}
        }

        return redirect()->route('admin.tools.index')->with('success', "Tool '{$tool->title}' updated successfully.");
    }

    /**
     * Delete a tool.
     */
    public function destroy(string $id): RedirectResponse
    {
        $tool = is_numeric($id)
            ? Tool::findOrFail($id)
            : Tool::where('slug', $id)->firstOrFail();

        $title = $tool->title;
        $tool->delete();

        if (class_exists(AuditLog::class)) {
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'delete_tool',
                    'description' => "Deleted tool '{$title}'",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Throwable $e) {}
        }

        return redirect()->route('admin.tools.index')->with('success', "Tool '{$title}' deleted successfully.");
    }

    /**
     * Toggle tool status (Active / Inactive).
     */
    public function toggle(Request $request, ?string $id = null): RedirectResponse
    {
        $targetId = $id ?? $request->input('id');

        $tool = is_numeric($targetId)
            ? Tool::find($targetId)
            : Tool::where('slug', $targetId)->first();

        if (!$tool) {
            return back()->with('error', 'Tool not found.');
        }

        $tool->is_active = !$tool->is_active;
        $tool->save();

        if (class_exists(AuditLog::class)) {
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'toggle_tool_status',
                    'description' => "Changed status of tool '{$tool->title}' to " . ($tool->is_active ? 'Active' : 'Inactive'),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Throwable $e) {}
        }

        return back()->with('success', "Tool '{$tool->title}' is now " . ($tool->is_active ? 'Active' : 'Inactive') . '.');
    }
}
