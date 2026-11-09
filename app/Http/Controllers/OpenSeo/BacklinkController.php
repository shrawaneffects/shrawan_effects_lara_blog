<?php

namespace App\Http\Controllers\OpenSeo;

use App\Http\Controllers\Controller;
use App\Models\OpenSeo\OpenSeoProject;
use App\Services\OpenSeo\BacklinkService;

class BacklinkController extends Controller
{
    public function index(BacklinkService $service)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) {
            return redirect()->route('openseo.projects.index');
        }

        $data = $service->getBacklinkProfile($activeProject);

        return view('frontend.openseo.backlinks', [
            'activeProject' => $activeProject,
            'summary' => $data['summary'],
            'backlinks' => $data['backlinks'],
            'anchors' => $data['anchors'],
        ]);
    }
}
