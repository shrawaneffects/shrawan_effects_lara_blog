<?php

namespace App\Http\Controllers\OpenSeo;

use App\Http\Controllers\Controller;
use App\Models\OpenSeo\OpenSeoProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = OpenSeoProject::withCount(['keywords', 'competitors', 'backlinks'])->get();
        $activeProject = OpenSeoProject::getActiveProject();

        return view('frontend.openseo.projects', compact('projects', 'activeProject'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'domain' => 'required|string|max:150',
            'target_country' => 'nullable|string|max:10',
            'target_language' => 'nullable|string|max:10',
        ]);

        $domain = preg_replace('#^https?://#', '', rtrim(trim($request->domain), '/'));

        $project = OpenSeoProject::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'domain' => $domain,
            'target_country' => $request->target_country ?: 'US',
            'target_language' => $request->target_language ?: 'en',
            'is_default' => OpenSeoProject::count() === 0,
        ]);

        session(['openseo_active_project_id' => $project->id]);

        return redirect()->route('openseo.domain')
            ->with('success', "Project '{$project->name}' created and set as active.");
    }

    public function switch($id)
    {
        $project = OpenSeoProject::findOrFail($id);
        session(['openseo_active_project_id' => $project->id]);

        return redirect()->back()
            ->with('success', "Active project switched to: {$project->name} ({$project->domain})");
    }

    public function destroy($id)
    {
        $project = OpenSeoProject::findOrFail($id);
        $name = $project->name;
        $project->delete();

        if (session('openseo_active_project_id') == $id) {
            session()->forget('openseo_active_project_id');
        }

        return redirect()->route('openseo.projects.index')
            ->with('success', "Project '{$name}' deleted.");
    }
}
