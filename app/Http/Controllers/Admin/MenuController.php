<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $location = $request->input('location', 'header');
        if (!in_array($location, ['header', 'footer'])) {
            $location = 'header';
        }

        $menuItems = MenuItem::location($location)
            ->root()
            ->with(['children' => function ($q) {
                $q->orderBy('order')->with(['children' => function ($q2) {
                    $q2->orderBy('order')->with(['children' => function ($q3) {
                        $q3->orderBy('order');
                    }]);
                }]);
            }])
            ->ordered()
            ->get();

        $allLocationItems = MenuItem::location($location)->orderBy('order')->get();
        $pages = Page::published()->orderBy('title')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.menus.index', compact('menuItems', 'allLocationItems', 'pages', 'categories', 'location'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type', 'custom');
        $location = $request->input('location', 'header');

        if (!in_array($location, ['header', 'footer'])) {
            $location = 'header';
        }

        $parentId = $request->input('parent_id') ? (int) $request->input('parent_id') : null;
        if ($parentId) {
            $parentExists = MenuItem::where('location', $location)->where('id', $parentId)->exists();
            if (!$parentExists) {
                $parentId = null;
            }
        }

        $maxOrder = MenuItem::location($location)
            ->where('parent_id', $parentId)
            ->max('order') ?? 0;

        if ($type === 'custom') {
            $validated = $request->validate([
                'title' => 'required|string|max:100',
                'url' => 'required|string|max:500',
                'icon' => 'nullable|string|max:50',
                'target' => 'nullable|in:_self,_blank',
                'parent_id' => 'nullable',
            ]);

            $cleanUrl = $this->normalizeMenuUrl($validated['url']);

            MenuItem::create([
                'location' => $location,
                'parent_id' => $parentId,
                'title' => $validated['title'],
                'url' => $cleanUrl,
                'icon' => $validated['icon'] ?? null,
                'target' => $validated['target'] ?? '_self',
                'order' => $maxOrder + 1,
                'is_active' => true,
            ]);
        } elseif ($type === 'pages') {
            $validated = $request->validate([
                'pages' => 'required|array',
                'pages.*' => 'exists:pages,id',
                'parent_id' => 'nullable',
            ]);

            $pages = Page::whereIn('id', $validated['pages'])->get();
            $addedCount = 0;
            foreach ($pages as $p) {
                $pageUrl = $this->normalizeMenuUrl(route('page.show', $p->slug, false));
                $maxOrder++;
                MenuItem::create([
                    'location' => $location,
                    'parent_id' => $parentId,
                    'title' => $p->title,
                    'url' => $pageUrl,
                    'icon' => 'bi bi-file-earmark-text',
                    'target' => '_self',
                    'order' => $maxOrder,
                    'is_active' => true,
                ]);
                $addedCount++;
            }
        } elseif ($type === 'categories') {
            $validated = $request->validate([
                'categories' => 'required|array',
                'categories.*' => 'exists:categories,id',
                'parent_id' => 'nullable',
            ]);

            $categories = Category::whereIn('id', $validated['categories'])->get();
            $addedCount = 0;
            foreach ($categories as $c) {
                $catUrl = $this->normalizeMenuUrl(route('blog.category', $c->slug, false));
                $maxOrder++;
                MenuItem::create([
                    'location' => $location,
                    'parent_id' => $parentId,
                    'title' => $c->name,
                    'url' => $catUrl,
                    'icon' => 'bi bi-folder2',
                    'target' => '_self',
                    'order' => $maxOrder,
                    'is_active' => true,
                ]);
                $addedCount++;
            }
        } elseif ($type === 'standard') {
            $validated = $request->validate([
                'standards' => 'required|array',
                'parent_id' => 'nullable',
            ]);

            $standardMap = [
                'home' => ['title' => 'Home', 'url' => '/', 'icon' => 'bi bi-house-door'],
                'articles' => ['title' => 'Articles', 'url' => '/blog', 'icon' => 'bi bi-grid'],
                'services' => ['title' => 'Services', 'url' => '/services', 'icon' => 'bi bi-briefcase'],
                'openseo' => ['title' => 'OpenSEO Suite', 'url' => '/openseo', 'icon' => 'bi bi-radar'],
                'faq' => ['title' => 'FAQ', 'url' => '/faq', 'icon' => 'bi bi-question-circle'],
                'contact' => ['title' => 'Contact Us', 'url' => '/contact', 'icon' => 'bi bi-envelope'],
            ];

            foreach ($validated['standards'] as $key) {
                if (isset($standardMap[$key])) {
                    $stdUrl = $this->normalizeMenuUrl($standardMap[$key]['url']);
                    $maxOrder++;
                    MenuItem::create([
                        'location' => $location,
                        'parent_id' => $parentId,
                        'title' => $standardMap[$key]['title'],
                        'url' => $stdUrl,
                        'icon' => $standardMap[$key]['icon'],
                        'target' => '_self',
                        'order' => $maxOrder,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.menus.index', ['location' => $location])
            ->with('success', 'Menu updated successfully!');
    }

    public function update(Request $request, $id)
    {
        $menuItem = MenuItem::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'url' => 'required|string|max:500',
            'icon' => 'nullable|string|max:50',
            'target' => 'required|in:_self,_blank',
            'parent_id' => 'nullable',
            'is_active' => 'nullable|boolean',
        ]);

        $cleanUrl = $this->normalizeMenuUrl($validated['url']);

        $parentId = $request->filled('parent_id') && (int)$request->input('parent_id') > 0
            ? (int)$request->input('parent_id')
            : null;

        // An item cannot be its own parent
        if ($parentId === (int)$id) {
            $parentId = null;
        }

        $menuItem->update([
            'title' => $validated['title'],
            'url' => $cleanUrl,
            'icon' => $validated['icon'] ?? null,
            'target' => $validated['target'],
            'parent_id' => $parentId,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.menus.index', ['location' => $menuItem->location])
            ->with('success', 'Menu item updated successfully!');
    }

    /**
     * Normalize Menu URLs to clean relative paths (e.g., /shrawan-effects) or external protocols
     */
    private function normalizeMenuUrl(string $url): string
    {
        $url = trim($url);

        // If empty
        if ($url === '') {
            return '/';
        }

        // If it starts with http:// or https://
        if (preg_match('/^https?:\/\//i', $url)) {
            $parsed = parse_url($url);
            $targetHost = strtolower($parsed['host'] ?? '');
            $currentHost = strtolower(parse_url(config('app.url'), PHP_URL_HOST) ?? request()->getHost());

            // If it is localhost, 127.0.0.1, or the current app domain, strip to clean relative text URL
            if (in_array($targetHost, [$currentHost, 'localhost', '127.0.0.1'])) {
                $path = $parsed['path'] ?? '/';
                if (!str_starts_with($path, '/')) {
                    $path = '/' . $path;
                }
                if (!empty($parsed['query'])) {
                    $path .= '?' . $parsed['query'];
                }
                if (!empty($parsed['fragment'])) {
                    $path .= '#' . $parsed['fragment'];
                }
                return $path;
            }

            return $url;
        }

        // If it's a hash, mailto, or tel, leave as is
        if (str_starts_with($url, '#') || str_starts_with($url, 'mailto:') || str_starts_with($url, 'tel:')) {
            return $url;
        }

        // Ensure clean text relative URL starts with / (e.g. /shrawan-effects)
        if (!str_starts_with($url, '/')) {
            $url = '/' . $url;
        }

        return $url;
    }

    public function destroy($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $location = $menuItem->location;
        $menuItem->delete();

        return redirect()->route('admin.menus.index', ['location' => $location])
            ->with('success', 'Menu item deleted successfully!');
    }

    /**
     * AJAX endpoint to save drag-and-drop order and parent-child hierarchy
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|in:header,footer',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.order' => 'required|integer',
            'items.*.parent_id' => 'nullable',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['items'] as $itemData) {
                $parentId = (!empty($itemData['parent_id']) && (int)$itemData['parent_id'] > 0)
                    ? (int)$itemData['parent_id']
                    : null;

                if ($parentId === (int)$itemData['id']) {
                    $parentId = null;
                }

                MenuItem::where('id', $itemData['id'])->update([
                    'order' => $itemData['order'],
                    'parent_id' => $parentId,
                    'location' => $validated['location'],
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Menu arrangement & hierarchy saved successfully!',
        ]);
    }

    public function resetDefaults(Request $request)
    {
        $location = $request->input('location', 'header');

        MenuItem::location($location)->delete();

        if ($location === 'header') {
            MenuItem::create([
                'location' => 'header',
                'title' => 'Home',
                'url' => '/',
                'icon' => 'bi bi-house-door',
                'order' => 1,
                'is_active' => true,
            ]);

            $services = MenuItem::create([
                'location' => 'header',
                'title' => 'Services',
                'url' => '/services',
                'icon' => 'bi bi-briefcase',
                'order' => 2,
                'is_active' => true,
            ]);

            // Sub-item 1 under Services
            $webDev = MenuItem::create([
                'location' => 'header',
                'parent_id' => $services->id,
                'title' => 'Web Development',
                'url' => '/services#web-development',
                'icon' => 'bi bi-code-slash',
                'order' => 1,
                'is_active' => true,
            ]);

            // Sub-sub item under Web Dev (Dropdown inside Dropdown!)
            MenuItem::create([
                'location' => 'header',
                'parent_id' => $webDev->id,
                'title' => 'Laravel & PHP Systems',
                'url' => '/services#laravel',
                'icon' => 'bi bi-terminal',
                'order' => 1,
                'is_active' => true,
            ]);

            MenuItem::create([
                'location' => 'header',
                'parent_id' => $webDev->id,
                'title' => 'Frontend & Vue Apps',
                'url' => '/services#frontend',
                'icon' => 'bi bi-cpu',
                'order' => 2,
                'is_active' => true,
            ]);

            MenuItem::create([
                'location' => 'header',
                'parent_id' => $services->id,
                'title' => 'AI & Automation',
                'url' => '/services#ai-solutions',
                'icon' => 'bi bi-robot',
                'order' => 2,
                'is_active' => true,
            ]);

            MenuItem::create([
                'location' => 'header',
                'parent_id' => $services->id,
                'title' => 'Cloud & DevOps',
                'url' => '/services#cloud-devops',
                'icon' => 'bi bi-cloud-arrow-up',
                'order' => 3,
                'is_active' => true,
            ]);

            $openSeo = MenuItem::create([
                'location' => 'header',
                'title' => 'OpenSEO Suite',
                'url' => '/openseo',
                'icon' => 'bi bi-radar',
                'order' => 3,
                'is_active' => true,
            ]);

            MenuItem::create([
                'location' => 'header',
                'parent_id' => $openSeo->id,
                'title' => 'Domain Overview',
                'url' => '/openseo',
                'icon' => 'bi bi-speedometer2',
                'order' => 1,
                'is_active' => true,
            ]);

            MenuItem::create([
                'location' => 'header',
                'parent_id' => $openSeo->id,
                'title' => 'Keyword Research',
                'url' => '/openseo/keywords',
                'icon' => 'bi bi-key',
                'order' => 2,
                'is_active' => true,
            ]);

            MenuItem::create([
                'location' => 'header',
                'parent_id' => $openSeo->id,
                'title' => 'Rank Tracker',
                'url' => '/openseo/rank-tracking',
                'icon' => 'bi bi-graph-up-arrow',
                'order' => 3,
                'is_active' => true,
            ]);

            MenuItem::create([
                'location' => 'header',
                'parent_id' => $openSeo->id,
                'title' => 'Competitor Insights',
                'url' => '/openseo/competitors',
                'icon' => 'bi bi-crosshair',
                'order' => 4,
                'is_active' => true,
            ]);

            MenuItem::create([
                'location' => 'header',
                'title' => 'Articles',
                'url' => '/blog',
                'icon' => 'bi bi-grid',
                'order' => 4,
                'is_active' => true,
            ]);

            MenuItem::create([
                'location' => 'header',
                'title' => 'FAQ',
                'url' => '/faq',
                'icon' => 'bi bi-question-circle',
                'order' => 5,
                'is_active' => true,
            ]);

            MenuItem::create([
                'location' => 'header',
                'title' => 'Contact Us',
                'url' => '/contact',
                'icon' => 'bi bi-envelope',
                'order' => 6,
                'is_active' => true,
            ]);
        } else {
            $defaults = [
                ['title' => 'Home', 'url' => '/', 'icon' => 'bi bi-house-door', 'order' => 1],
                ['title' => 'Our Services', 'url' => '/services', 'icon' => 'bi bi-briefcase', 'order' => 2],
                ['title' => 'OpenSEO Suite', 'url' => '/openseo', 'icon' => 'bi bi-radar', 'order' => 3],
                ['title' => 'All Articles', 'url' => '/blog', 'icon' => 'bi bi-grid', 'order' => 4],
                ['title' => 'FAQ', 'url' => '/faq', 'icon' => 'bi bi-question-circle', 'order' => 5],
                ['title' => 'Contact Us', 'url' => '/contact', 'icon' => 'bi bi-envelope', 'order' => 6],
                ['title' => 'Privacy Policy', 'url' => '/page/privacy-policy', 'icon' => 'bi bi-shield-check', 'order' => 7],
                ['title' => 'Terms of Service', 'url' => '/page/terms-of-service', 'icon' => 'bi bi-file-text', 'order' => 8],
            ];

            foreach ($defaults as $d) {
                MenuItem::create([
                    'location' => $location,
                    'title' => $d['title'],
                    'url' => $d['url'],
                    'icon' => $d['icon'] ?? null,
                    'target' => '_self',
                    'order' => $d['order'],
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('admin.menus.index', ['location' => $location])
            ->with('success', 'Default menu layout restored!');
    }

    public function toggleVisibility($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $menuItem->is_active = !$menuItem->is_active;
        $menuItem->save();

        $status = $menuItem->is_active ? 'visible' : 'hidden';
        return back()->with('success', "Menu item '{$menuItem->title}' is now {$status}!");
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:hide,show,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:menu_items,id',
            'location' => 'required|in:header,footer',
        ]);

        $ids = $validated['ids'];
        $location = $validated['location'];
        $count = count($ids);

        if ($validated['action'] === 'hide') {
            MenuItem::whereIn('id', $ids)->update(['is_active' => false]);
            $message = "{$count} selected menu items hidden from website.";
        } elseif ($validated['action'] === 'show') {
            MenuItem::whereIn('id', $ids)->update(['is_active' => true]);
            $message = "{$count} selected menu items are now visible.";
        } elseif ($validated['action'] === 'delete') {
            MenuItem::whereIn('id', $ids)->delete();
            $message = "{$count} selected menu items deleted.";
        }

        return redirect()->route('admin.menus.index', ['location' => $location])
            ->with('success', $message);
    }
}
