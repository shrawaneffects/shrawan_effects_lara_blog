<?php

namespace App\Http\Controllers\OpenSeo;

use App\Http\Controllers\Controller;
use App\Models\OpenSeo\OpenSeoProject;
use App\Services\OpenSeo\SiteAuditService;

class SiteAuditController extends Controller
{
    public function index(SiteAuditService $service)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) {
            return redirect()->route('openseo.projects.index');
        }

        $audit = $service->getLatestAudit($activeProject);
        $issues = $audit ? $audit->issues()->orderByRaw("FIELD(severity, 'high', 'medium', 'low')")->paginate(15) : collect();

        return view('frontend.openseo.audit', compact('activeProject', 'audit', 'issues'));
    }

    public function crawl(SiteAuditService $service)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) return redirect()->back();

        $service->runAudit($activeProject);

        return redirect()->back()->with('success', "Technical site audit crawl completed for {$activeProject->domain}.");
    }
}
