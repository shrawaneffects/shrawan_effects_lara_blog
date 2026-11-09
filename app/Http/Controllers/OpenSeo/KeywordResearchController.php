<?php

namespace App\Http\Controllers\OpenSeo;

use App\Http\Controllers\Controller;
use App\Models\OpenSeo\OpenSeoKeyword;
use App\Models\OpenSeo\OpenSeoProject;
use App\Services\OpenSeo\KeywordResearchService;
use Illuminate\Http\Request;

class KeywordResearchController extends Controller
{
    public function index(Request $request, KeywordResearchService $service)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) {
            return redirect()->route('openseo.projects.index');
        }

        $query = $request->input('q', 'web development');
        $researchData = $service->search($activeProject, $query, $activeProject->target_country);

        // Saved project keywords
        $savedKeywords = $activeProject->keywords()->latest()->paginate(15);

        return view('frontend.openseo.keywords', compact('activeProject', 'query', 'researchData', 'savedKeywords'));
    }

    public function track(Request $request)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) {
            return redirect()->back();
        }

        $request->validate([
            'keyword' => 'required|string|max:150',
            'search_volume' => 'nullable|integer',
            'cpc' => 'nullable|numeric',
            'competition' => 'nullable|numeric',
            'keyword_difficulty' => 'nullable|integer',
            'search_intent' => 'nullable|string',
        ]);

        $kw = OpenSeoKeyword::updateOrCreate(
            [
                'project_id' => $activeProject->id,
                'keyword' => trim($request->keyword),
            ],
            [
                'search_volume' => $request->search_volume ?? 1200,
                'cpc' => $request->cpc ?? 2.50,
                'competition' => $request->competition ?? 0.45,
                'keyword_difficulty' => $request->keyword_difficulty ?? 35,
                'search_intent' => $request->search_intent ?? 'Commercial',
                'is_tracked' => true,
                'last_checked_at' => now(),
            ]
        );

        // Run immediate initial rank check
        app(\App\Services\OpenSeo\RankTrackingService::class)->checkRank($kw);

        return redirect()->back()->with('success', "Keyword '{$kw->keyword}' added to rank tracking.");
    }

    public function untrack($id)
    {
        $kw = OpenSeoKeyword::findOrFail($id);
        $kw->update(['is_tracked' => false]);

        return redirect()->back()->with('success', "Keyword '{$kw->keyword}' removed from tracking.");
    }
}
