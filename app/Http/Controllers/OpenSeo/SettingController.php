<?php

namespace App\Http\Controllers\OpenSeo;

use App\Http\Controllers\Controller;
use App\Models\OpenSeo\OpenSeoProject;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) {
            return redirect()->route('openseo.projects.index');
        }

        return view('frontend.openseo.settings', compact('activeProject'));
    }

    public function update(Request $request)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) return redirect()->back();

        $request->validate([
            'name' => 'required|string|max:100',
            'domain' => 'required|string|max:150',
            'target_country' => 'required|string|max:10',
            'target_language' => 'required|string|max:10',
            'gsc_property' => 'nullable|string|max:200',
            'dataforseo_login' => 'nullable|string|max:150',
            'dataforseo_password' => 'nullable|string|max:255',
        ]);

        $activeProject->update([
            'name' => $request->name,
            'domain' => preg_replace('#^https?://#', '', rtrim(trim($request->domain), '/')),
            'target_country' => $request->target_country,
            'target_language' => $request->target_language,
            'gsc_property' => $request->gsc_property,
            'dataforseo_login' => $request->dataforseo_login,
            'dataforseo_password' => $request->dataforseo_password,
        ]);

        return redirect()->back()->with('success', 'Project settings & API integrations updated.');
    }
}
