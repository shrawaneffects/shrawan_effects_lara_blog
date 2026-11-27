<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToolController extends Controller
{
    /**
     * Default catalog of built-in web tools.
     */
    private const DEFAULT_TOOLS = [
        [
            'id' => 'convertcase',
            'title' => 'Convert Case (Online Text Case Converter)',
            'slug' => 'convertcase',
            'url' => '/services/convertcase',
            'route_name' => 'services.convertcase',
            'type' => 'frontend',
            'category' => 'Text & Content Utilities',
            'icon' => 'bi-type',
            'badge' => 'NEW',
            'description' => 'Instantly transforms text into Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, and InVeRsE CaSe with live character and word counters.',
            'is_active' => true,
            'is_featured' => true,
        ],
        [
            'id' => 'bulk-ip-checker',
            'title' => 'Bulk Website IP Checker & Subnet Analyzer',
            'slug' => 'bulk-website-ip-checker',
            'url' => '/services/bulk-website-ip-checker',
            'route_name' => 'services.bulk-ip-checker',
            'type' => 'frontend',
            'category' => 'Network & Infrastructure',
            'icon' => 'bi-hdd-network',
            'badge' => 'POPULAR',
            'description' => 'Asynchronously resolves up to 10,000+ domains into IPv4/IPv6, classifies IP types (CDN, Cloud, Datacenter, ISP), and detects same-IP and Class-C shared hosting footprints.',
            'is_active' => true,
            'is_featured' => true,
        ],
        [
            'id' => 'seo-grader',
            'title' => 'On-Page SEO Grader',
            'slug' => 'seo-grader',
            'url' => '/admin/seo/grader',
            'route_name' => 'admin.seo.grader',
            'type' => 'admin',
            'category' => 'SEO & Audit',
            'icon' => 'bi-award-fill',
            'badge' => 'PRO',
            'description' => 'Real-time On-Page SEO analyzer, assessing title tags, heading structure, word counts, and search snippet rendering.',
            'is_active' => true,
            'is_featured' => false,
        ],
        [
            'id' => 'server-config',
            'title' => 'Robots.txt & .htaccess Server Config',
            'slug' => 'server-config',
            'url' => '/admin/seo/server-config',
            'route_name' => 'admin.seo.server-config',
            'type' => 'admin',
            'category' => 'Server & Directives',
            'icon' => 'bi-sliders',
            'badge' => 'CORE',
            'description' => 'Live editor and syntax validator for server directives, crawler crawl-delay configurations, and .htaccess rules.',
            'is_active' => true,
            'is_featured' => false,
        ],
        [
            'id' => 'redirects-manager',
            'title' => '301 Permanent Redirects Engine',
            'slug' => 'redirects-manager',
            'url' => '/admin/seo/redirects',
            'route_name' => 'admin.seo.redirects',
            'type' => 'admin',
            'category' => 'SEO & Routing',
            'icon' => 'bi-signpost-split',
            'badge' => 'CORE',
            'description' => 'Creates, tests, and validates automated 301 and 302 HTTP URL forwarding rules to eliminate 404 crawl errors.',
            'is_active' => true,
            'is_featured' => false,
        ],
        [
            'id' => 'openseo-audit',
            'title' => 'Technical Site Crawler & Auditor',
            'slug' => 'openseo-audit',
            'url' => '/openseo/audit',
            'route_name' => 'openseo.audit',
            'type' => 'admin',
            'category' => 'SEO & Audit',
            'icon' => 'bi-search',
            'badge' => 'SUITE',
            'description' => 'Full website recursive crawler scanning for broken internal links, duplicate titles, missing alt tags, and slow TTFB pages.',
            'is_active' => true,
            'is_featured' => false,
        ],
    ];

    /**
     * Retrieve all configured tools (defaults merged with saved customizations).
     */
    protected function getTools(): array
    {
        $saved = Setting::get('admin_tools_catalog');
        if ($saved) {
            $decoded = json_decode($saved, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }
        return self::DEFAULT_TOOLS;
    }

    /**
     * Save the tools catalog.
     */
    protected function saveTools(array $tools): void
    {
        Setting::set('admin_tools_catalog', json_encode(array_values($tools), JSON_PRETTY_PRINT));
    }

    /**
     * List all tools in the admin panel.
     */
    public function index(Request $request): View
    {
        $allTools = $this->getTools();

        $search = strtolower(trim((string) $request->input('search', '')));
        $category = trim((string) $request->input('category', ''));
        $status = trim((string) $request->input('status', ''));

        $filtered = array_filter($allTools, function ($tool) use ($search, $category, $status) {
            if ($search !== '') {
                $haystack = strtolower($tool['title'] . ' ' . $tool['description'] . ' ' . $tool['category'] . ' ' . $tool['slug']);
                if (!str_contains($haystack, $search)) {
                    return false;
                }
            }
            if ($category !== '' && $tool['category'] !== $category) {
                return false;
            }
            if ($status === 'active' && empty($tool['is_active'])) {
                return false;
            }
            if ($status === 'inactive' && !empty($tool['is_active'])) {
                return false;
            }
            return true;
        });

        $totalTools = count($allTools);
        $activeTools = count(array_filter($allTools, fn($t) => !empty($t['is_active'])));
        $frontendTools = count(array_filter($allTools, fn($t) => ($t['type'] ?? '') === 'frontend'));
        $adminTools = count(array_filter($allTools, fn($t) => ($t['type'] ?? '') === 'admin'));

        $categories = array_unique(array_column($allTools, 'category'));

        return view('admin.tools.index', [
            'tools' => $filtered,
            'totalTools' => $totalTools,
            'activeTools' => $activeTools,
            'frontendTools' => $frontendTools,
            'adminTools' => $adminTools,
            'categories' => $categories,
            'search' => $request->input('search'),
            'selectedCategory' => $category,
            'selectedStatus' => $status,
        ]);
    }

    /**
     * Toggle tool status (Active / Inactive).
     */
    public function toggle(Request $request): RedirectResponse
    {
        $request->validate([
            'id' => 'required|string',
        ]);

        $id = $request->input('id');
        $tools = $this->getTools();
        $updated = false;
        $newStatus = false;
        $toolTitle = $id;

        foreach ($tools as &$tool) {
            if ($tool['id'] === $id) {
                $tool['is_active'] = empty($tool['is_active']);
                $newStatus = $tool['is_active'];
                $toolTitle = $tool['title'];
                $updated = true;
                break;
            }
        }
        unset($tool);

        if ($updated) {
            $this->saveTools($tools);

            if (class_exists(AuditLog::class)) {
                try {
                    AuditLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'toggle_tool_status',
                        'description' => "Changed status of tool '{$toolTitle}' to " . ($newStatus ? 'Active' : 'Inactive'),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                } catch (\Throwable $e) {}
            }

            return back()->with('success', "Tool '{$toolTitle}' is now " . ($newStatus ? 'Active' : 'Inactive') . '.');
        }

        return back()->with('error', 'Tool not found.');
    }

    /**
     * Update tool details.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category' => 'required|string|max:100',
            'badge' => 'nullable|string|max:30',
            'icon' => 'nullable|string|max:60',
        ]);

        $tools = $this->getTools();
        $updated = false;

        foreach ($tools as &$tool) {
            if ($tool['id'] === $validated['id']) {
                $tool['title'] = $validated['title'];
                $tool['description'] = $validated['description'];
                $tool['category'] = $validated['category'];
                $tool['badge'] = $validated['badge'] ?? '';
                $tool['icon'] = $validated['icon'] ?? $tool['icon'];
                $updated = true;
                break;
            }
        }
        unset($tool);

        if ($updated) {
            $this->saveTools($tools);
            return back()->with('success', 'Tool details updated successfully.');
        }

        return back()->with('error', 'Tool not found.');
    }
}
