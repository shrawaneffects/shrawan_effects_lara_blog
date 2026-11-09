<?php

namespace App\Http\Controllers\OpenSeo;

use App\Http\Controllers\Controller;
use App\Models\OpenSeo\OpenSeoProject;
use App\Services\OpenSeo\DomainOverviewService;

class DomainOverviewController extends Controller
{
    public function index(DomainOverviewService $service)
    {
        $activeProject = OpenSeoProject::getActiveProject();

        if (!$activeProject) {
            return redirect()->route('openseo.projects.index')
                ->with('info', 'Please create an SEO project first.');
        }

        $overview = $service->getOverview($activeProject);

        return view('frontend.openseo.domain', compact('activeProject', 'overview'));
    }
}
