<?php

namespace App\Http\Controllers\OpenSeo;

use App\Http\Controllers\Controller;
use App\Models\OpenSeo\OpenSeoProject;
use App\Services\OpenSeo\SearchConsoleService;
use Illuminate\Http\Request;

class SearchPerformanceController extends Controller
{
    public function index(Request $request, SearchConsoleService $service)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) {
            return redirect()->route('openseo.projects.index');
        }

        $days = (int)$request->input('days', 28);
        $data = $service->getPerformance($activeProject, $days);

        return view('frontend.openseo.search-performance', array_merge(['activeProject' => $activeProject, 'days' => $days], $data));
    }
}
