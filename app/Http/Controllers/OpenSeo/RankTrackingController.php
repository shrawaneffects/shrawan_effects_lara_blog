<?php

namespace App\Http\Controllers\OpenSeo;

use App\Http\Controllers\Controller;
use App\Models\OpenSeo\OpenSeoKeyword;
use App\Models\OpenSeo\OpenSeoProject;
use App\Services\OpenSeo\RankTrackingService;
use Illuminate\Http\Request;

class RankTrackingController extends Controller
{
    public function index(Request $request)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) {
            return redirect()->route('openseo.projects.index');
        }

        $query = $activeProject->keywords()->where('is_tracked', true);

        if ($request->filled('search')) {
            $query->where('keyword', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'top3':
                    $query->whereBetween('current_rank', [1, 3]);
                    break;
                case 'top10':
                    $query->whereBetween('current_rank', [1, 10]);
                    break;
                case 'improved':
                    $query->where('rank_change', '>', 0);
                    break;
                case 'declined':
                    $query->where('rank_change', '<', 0);
                    break;
            }
        }

        $trackedKeywords = $query->orderBy('current_rank', 'asc')->paginate(20);

        // Calculate summary metrics
        $allTracked = $activeProject->keywords()->where('is_tracked', true)->get();
        $totalTracked = $allTracked->count();
        $top3Count = $allTracked->whereBetween('current_rank', [1, 3])->count();
        $top10Count = $allTracked->whereBetween('current_rank', [1, 10])->count();
        $improvedCount = $allTracked->where('rank_change', '>', 0)->count();
        $declinedCount = $allTracked->where('rank_change', '<', 0)->count();

        return view('frontend.openseo.rank-tracking', compact(
            'activeProject',
            'trackedKeywords',
            'totalTracked',
            'top3Count',
            'top10Count',
            'improvedCount',
            'declinedCount'
        ));
    }

    public function check($id, RankTrackingService $service)
    {
        $keyword = OpenSeoKeyword::findOrFail($id);
        $service->checkRank($keyword);

        return redirect()->back()->with('success', "Live SERP check updated for '{$keyword->keyword}'.");
    }

    public function refreshAll(RankTrackingService $service)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) return redirect()->back();

        $count = $service->refreshAll($activeProject);

        return redirect()->back()->with('success', "All {$count} tracked keywords refreshed via live SERP checks.");
    }
}
