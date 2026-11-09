<?php

namespace App\Http\Controllers\OpenSeo;

use App\Http\Controllers\Controller;
use App\Models\OpenSeo\OpenSeoCompetitor;
use App\Models\OpenSeo\OpenSeoProject;
use App\Services\OpenSeo\CompetitorAnalysisService;
use Illuminate\Http\Request;

class CompetitorController extends Controller
{
    public function index(CompetitorAnalysisService $service)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) {
            return redirect()->route('openseo.projects.index');
        }

        $data = $service->getCompetitorData($activeProject);

        return view('frontend.openseo.competitors', [
            'activeProject' => $activeProject,
            'competitors' => $data['competitors'],
            'gapKeywords' => $data['gap_keywords'],
        ]);
    }

    public function store(Request $request)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) return redirect()->back();

        $request->validate([
            'competitor_domain' => 'required|string|max:150',
            'notes' => 'nullable|string|max:255',
        ]);

        $domain = preg_replace('#^https?://#', '', rtrim(trim($request->competitor_domain), '/'));
        $hash = crc32($domain);

        OpenSeoCompetitor::updateOrCreate(
            [
                'project_id' => $activeProject->id,
                'competitor_domain' => $domain,
            ],
            [
                'organic_traffic' => (abs($hash % 35000) + 5000),
                'organic_keywords' => (abs($hash % 2000) + 300),
                'common_keywords' => (abs($hash % 90) + 20),
                'keyword_overlap_pct' => round((abs($hash % 300) / 10) + 8.5, 2),
                'notes' => $request->notes,
            ]
        );

        return redirect()->back()->with('success', "Competitor '{$domain}' added to competitive benchmark.");
    }

    public function destroy($id)
    {
        $comp = OpenSeoCompetitor::findOrFail($id);
        $domain = $comp->competitor_domain;
        $comp->delete();

        return redirect()->back()->with('success', "Competitor '{$domain}' removed.");
    }
}
