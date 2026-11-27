<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConvertCaseController extends Controller
{
    /**
     * Display the Convert Case online utility page.
     */
    public function index(): View
    {
        $tool = null;
        $relatedTools = collect();
        $relatedPosts = collect();

        try {
            $tool = Tool::where('slug', 'convertcase')->first();
            if ($tool) {
                $relatedTools = $tool->related_tools_list;
                $relatedPosts = $tool->related_posts_list;
            } else {
                $relatedTools = Tool::active()->where('slug', '!=', 'convertcase')->limit(4)->get();
                if (class_exists(Post::class)) {
                    $relatedPosts = Post::published()->latest('published_at')->limit(4)->get();
                }
            }
        } catch (\Throwable $e) {}

        return view('frontend.services.convertcase', [
            'tool' => $tool,
            'relatedTools' => $relatedTools,
            'relatedPosts' => $relatedPosts,
            'pageTitle' => $tool->meta_title ?? ($tool->title ?? 'Convert Case - Online Text Case Converter | Shrawan Effects'),
            'metaDescription' => $tool->meta_description ?? ($tool->summary ?? 'Free online Convert Case tool. Instantly convert text between Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, and InVeRsE CaSe with live character and word counters.'),
            'canonicalUrl' => $tool->canonical_url ?? route('services.convertcase'),
        ]);
    }
}
