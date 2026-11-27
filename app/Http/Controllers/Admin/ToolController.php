<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ToolController extends Controller
{
    /**
     * Default built-in tools seed data.
     */
    public const DEFAULT_TOOLS = [
        [
            'title' => 'Convert Case - Online Text Case Converter',
            'slug' => 'convertcase',
            'url' => '/services/convertcase',
            'route_name' => 'services.convertcase',
            'type' => 'frontend',
            'category' => 'Text & Content Utilities',
            'icon' => 'bi-type',
            'badge' => 'NEW',
            'summary' => 'Instantly transforms text into Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, and InVeRsE CaSe with live character and word counters.',
            'content' => '',
            'meta_title' => 'Convert Case - Free Online Text Case Converter | Shrawan Effects',
            'meta_description' => 'Free online text converter tool. Instantly convert text between Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, and InVeRsE CaSe.',
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
            'content' => '',
            'meta_title' => 'Bulk Website IP Checker & Subnet Clustering Tool | Shrawan Effects',
            'meta_description' => 'Resolve up to 10,000+ domains to IPv4 & IPv6 addresses instantly. Detect Cloudflare/CDN proxies, datacenter hosting, and Class-C network footprints.',
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
            'content' => '',
            'meta_title' => 'On-Page SEO Grader & Content Analyzer',
            'meta_description' => 'Real-time On-Page SEO analyzer, assessing title tags, heading structure, word counts, and search snippet rendering.',
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
            'content' => '',
            'meta_title' => 'Server Config & Directives Manager',
            'meta_description' => 'Live editor and syntax validator for server directives, crawler crawl-delay configurations, and .htaccess rules.',
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
            'content' => '',
            'meta_title' => '301 Permanent Redirects Engine',
            'meta_description' => 'Creates, tests, and validates automated 301 and 302 HTTP URL forwarding rules to eliminate 404 crawl errors.',
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
            'content' => '',
            'meta_title' => 'Technical Site Crawler & Auditor',
            'meta_description' => 'Full website recursive crawler scanning for broken internal links, duplicate titles, missing alt tags, and slow TTFB pages.',
            'is_active' => true,
            'is_featured' => false,
            'order' => 6,
        ],
    ];

    /**
     * Ensure default tools are populated if table is empty.
     */
    protected function ensureToolsPopulated(): void
    {
        try {
            if (Schema::hasTable('tools') && Tool::count() === 0) {
                foreach (self::DEFAULT_TOOLS as $item) {
                    Tool::create($item);
                }
            }
        } catch (\Throwable $e) {}
    }

    /**
     * List all tools in the admin panel.
     */
    public function index(Request $request): View
    {
        $this->ensureToolsPopulated();

        $query = Tool::query();

        if ($request->filled('search')) {
            $search = strtolower(trim((string) $request->input('search')));
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

        $totalTools = Tool::count();
        $activeTools = Tool::where('is_active', true)->count();
        $frontendTools = Tool::where('type', 'frontend')->count();
        $adminTools = Tool::where('type', 'admin')->count();

        $tools = $query->orderBy('order', 'asc')->latest('id')->paginate(15)->withQueryString();
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
     * Show form to create new tool content.
     */
    public function create(): View
    {
        $categories = Tool::whereNotNull('category')->distinct()->pluck('category')->filter()->values();

        return view('admin.tools.create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store new tool.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tools,slug',
            'url' => 'nullable|string|max:255',
            'route_name' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:100',
            'badge' => 'nullable|string|max:50',
            'summary' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'type' => 'nullable|string|in:frontend,admin',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Tool::generateUniqueSlug($validated['title']);
        }

        if (empty($validated['url']) && ($validated['type'] ?? 'frontend') === 'frontend') {
            $validated['url'] = '/services/' . $validated['slug'];
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);
        $validated['order'] = (int) ($validated['order'] ?? 0);
        $validated['type'] = $validated['type'] ?? 'frontend';

        $tool = Tool::create($validated);

        if (class_exists(AuditLog::class)) {
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'create_tool',
                    'description' => "Created tool content for '{$tool->title}'",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Throwable $e) {}
        }

        return redirect()->route('admin.tools.index')->with('success', "Tool '{$tool->title}' created successfully.");
    }

    /**
     * Show form to edit tool and its content.
     */
    public function edit(string $id): View
    {
        $tool = Tool::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        $categories = Tool::whereNotNull('category')->distinct()->pluck('category')->filter()->values();

        return view('admin.tools.edit', [
            'tool' => $tool,
            'categories' => $categories,
        ]);
    }

    /**
     * Update tool and its content.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $tool = Tool::where('id', $id)->orWhere('slug', $id)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tools,slug,' . $tool->id,
            'url' => 'nullable|string|max:255',
            'route_name' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:100',
            'badge' => 'nullable|string|max:50',
            'summary' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'type' => 'nullable|string|in:frontend,admin',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Tool::generateUniqueSlug($validated['title'], $tool->id);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['order'] = (int) ($validated['order'] ?? $tool->order);
        $validated['type'] = $validated['type'] ?? $tool->type;

        $tool->update($validated);

        if (class_exists(AuditLog::class)) {
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'update_tool',
                    'description' => "Updated tool content for '{$tool->title}'",
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
        $tool = Tool::where('id', $id)->orWhere('slug', $id)->firstOrFail();
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
        $targetId = $id ?: $request->input('id');
        $tool = Tool::where('id', $targetId)->orWhere('slug', $targetId)->first();

        if ($tool) {
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

        return back()->with('error', 'Tool not found.');
    }

    /**
     * Legacy toggle support.
     */
    public function toggleLegacy(Request $request): RedirectResponse
    {
        return $this->toggle($request, $request->input('id'));
    }

    /**
     * Legacy quick update modal support.
     */
    public function updateLegacy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'badge' => 'nullable|string|max:30',
            'icon' => 'nullable|string|max:60',
        ]);

        $tool = Tool::where('id', $validated['id'])->orWhere('slug', $validated['id'])->first();
        if ($tool) {
            $tool->update([
                'title' => $validated['title'],
                'summary' => $validated['description'] ?? $tool->summary,
                'category' => $validated['category'],
                'badge' => $validated['badge'] ?? '',
                'icon' => $validated['icon'] ?? $tool->icon,
            ]);
            return back()->with('success', 'Tool details updated successfully.');
        }

        return back()->with('error', 'Tool not found.');
    }
}
