<?php

namespace App\Http\Controllers\OpenSeo;

use App\Http\Controllers\Controller;
use App\Models\OpenSeo\OpenSeoProject;
use App\Services\OpenSeo\LocalSeoService;
use Illuminate\Http\Request;

class LocalSeoController extends Controller
{
    public function index()
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) {
            return redirect()->route('openseo.projects.index');
        }

        $trackers = $activeProject->localTrackers()->latest('last_scanned_at')->get();
        $activeTracker = $trackers->first();

        return view('frontend.openseo.local-seo', compact('activeProject', 'trackers', 'activeTracker'));
    }

    public function scan(Request $request, LocalSeoService $service)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) return redirect()->back();

        $request->validate([
            'business_name' => 'required|string|max:150',
            'keyword' => 'required|string|max:150',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $lat = $request->filled('latitude') ? (float)$request->latitude : 28.3685;
        $lng = $request->filled('longitude') ? (float)$request->longitude : 77.2950;

        $tracker = $service->scanGrid(
            $activeProject,
            $request->business_name,
            $request->keyword,
            $request->address,
            $lat,
            $lng
        );

        return redirect()->back()->with('success', "Local SEO geo-grid scanned for '{$tracker->keyword}'.");
    }
}
