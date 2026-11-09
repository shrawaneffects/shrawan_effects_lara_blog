<?php

namespace App\Http\Controllers\OpenSeo;

use App\Http\Controllers\Controller;
use App\Models\OpenSeo\OpenSeoProject;
use App\Services\OpenSeo\AiVisibilityService;
use Illuminate\Http\Request;

class AiVisibilityController extends Controller
{
    public function index()
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) {
            return redirect()->route('openseo.projects.index');
        }

        $mentions = $activeProject->aiVisibilities()->latest('last_checked_at')->paginate(15);
        $totalChecked = $activeProject->aiVisibilities()->count();
        $citedCount = $activeProject->aiVisibilities()->where('is_mentioned', true)->count();
        $positiveCount = $activeProject->aiVisibilities()->where('sentiment', 'Positive')->count();

        return view('frontend.openseo.ai-visibility', compact(
            'activeProject',
            'mentions',
            'totalChecked',
            'citedCount',
            'positiveCount'
        ));
    }

    public function check(Request $request, AiVisibilityService $service)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) return redirect()->back();

        $request->validate([
            'prompt_query' => 'required|string|max:255',
            'ai_model' => 'nullable|string|in:ChatGPT,Claude,Gemini,Perplexity',
        ]);

        $service->checkAiVisibility(
            $activeProject,
            $request->prompt_query,
            $request->ai_model ?: 'ChatGPT'
        );

        return redirect()->back()->with('success', "AI brand visibility analyzed for: \"{$request->prompt_query}\".");
    }
}
