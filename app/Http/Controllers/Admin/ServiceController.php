<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display a listing of services in the admin dashboard.
     */
    public function index(Request $request)
    {
        $query = Service::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('technologies', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $services = $query->orderBy('order')->latest()->paginate(12)->withQueryString();

        $totalServices = Service::count();
        $activeServices = Service::where('is_active', true)->count();
        $featuredServices = Service::where('is_featured', true)->count();

        return view('admin.services.index', compact(
            'services',
            'totalServices',
            'activeServices',
            'featuredServices'
        ));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'icon' => 'nullable|string|max:100',
            'short_description' => 'required|string|max:500',
            'content' => 'nullable|string',
            'features' => 'nullable|string',
            'technologies' => 'nullable|string|max:255',
            'pricing_starts_at' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_url' => 'nullable|url|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Service::generateUniqueSlug($validated['title']);

        // Convert features from newline-delimited text to array
        $featuresArray = [];
        if (!empty($validated['features'])) {
            $lines = explode("\n", str_replace("\r", "", $validated['features']));
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (!empty($trimmed)) {
                    $featuresArray[] = $trimmed;
                }
            }
        }

        $service = new Service();
        $service->title = $validated['title'];
        $service->slug = $slug;
        $service->icon = $validated['icon'] ?? 'bi-code-slash';
        $service->short_description = $validated['short_description'];
        $service->content = $validated['content'] ?? null;
        $service->features = $featuresArray;
        $service->technologies = $validated['technologies'] ?? null;
        $service->pricing_starts_at = $validated['pricing_starts_at'] ?? null;
        $service->order = $validated['order'] ?? 0;
        $service->is_active = $request->boolean('is_active', true);
        $service->is_featured = $request->boolean('is_featured', false);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('services', 'public');
            $service->image = $path;
        } elseif (!empty($validated['image_url'])) {
            $service->image = $validated['image_url'];
        }

        $service->save();

        AuditLog::record(
            'service_created',
            "Service '{$service->title}' created by admin",
            auth()->id(),
            ['service_id' => $service->id],
            'info',
            $request
        );

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully!');
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit($id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug,' . $service->id,
            'icon' => 'nullable|string|max:100',
            'short_description' => 'required|string|max:500',
            'content' => 'nullable|string',
            'features' => 'nullable|string',
            'technologies' => 'nullable|string|max:255',
            'pricing_starts_at' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_url' => 'nullable|url|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        if (!empty($validated['slug']) && $validated['slug'] !== $service->slug) {
            $service->slug = Str::slug($validated['slug']);
        }

        // Convert features from newline-delimited text to array
        $featuresArray = [];
        if (!empty($validated['features'])) {
            $lines = explode("\n", str_replace("\r", "", $validated['features']));
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (!empty($trimmed)) {
                    $featuresArray[] = $trimmed;
                }
            }
        }

        $service->title = $validated['title'];
        $service->icon = $validated['icon'] ?? 'bi-code-slash';
        $service->short_description = $validated['short_description'];
        $service->content = $validated['content'] ?? null;
        $service->features = $featuresArray;
        $service->technologies = $validated['technologies'] ?? null;
        $service->pricing_starts_at = $validated['pricing_starts_at'] ?? null;
        $service->order = $validated['order'] ?? 0;
        $service->is_active = $request->boolean('is_active', true);
        $service->is_featured = $request->boolean('is_featured', false);

        if ($request->hasFile('image')) {
            if ($service->image && !str_starts_with($service->image, 'http') && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            $path = $request->file('image')->store('services', 'public');
            $service->image = $path;
        } elseif (!empty($validated['image_url'])) {
            $service->image = $validated['image_url'];
        }

        $service->save();

        AuditLog::record(
            'service_updated',
            "Service '{$service->title}' updated by admin",
            auth()->id(),
            ['service_id' => $service->id],
            'info',
            $request
        );

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully!');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        if ($service->image && !str_starts_with($service->image, 'http') && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        $title = $service->title;
        $service->delete();

        AuditLog::record(
            'service_deleted',
            "Service '{$title}' deleted by admin",
            auth()->id(),
            ['service_id' => $id],
            'danger',
            $request
        );

        return redirect()->route('admin.services.index')->with('success', "Service '{$title}' deleted successfully!");
    }

    /**
     * Toggle active state.
     */
    public function toggleActive(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->is_active = !$service->is_active;
        $service->save();

        return response()->json([
            'success' => true,
            'is_active' => $service->is_active,
            'message' => "Service status changed to " . ($service->is_active ? 'Active' : 'Inactive'),
        ]);
    }
}
