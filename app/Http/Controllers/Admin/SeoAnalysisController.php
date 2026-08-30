<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Seo\SeoAuditService;
use App\Services\Seo\SeoInternalLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeoAnalysisController extends Controller
{
    /**
     * Run live SEO audit and return analysis JSON for the article editor
     */
    public function analyze(Request $request): JsonResponse
    {
        $data = [
            'title' => $request->input('title', ''),
            'slug' => $request->input('slug', ''),
            'content' => $request->input('content', ''),
            'excerpt' => $request->input('excerpt', ''),
            'seo_title' => $request->input('seo_title', ''),
            'meta_description' => $request->input('meta_description', ''),
            'primary_keyword' => $request->input('primary_keyword', ''),
            'search_intent' => $request->input('search_intent', 'informational'),
            'canonical_url' => $request->input('canonical_url', ''),
            'robots_index' => $request->boolean('robots_index', true),
            'robots_follow' => $request->boolean('robots_follow', true),
            'include_in_sitemap' => $request->boolean('include_in_sitemap', true),
            'og_title' => $request->input('og_title', ''),
            'og_description' => $request->input('og_description', ''),
            'og_image' => $request->input('og_image', ''),
            'twitter_card' => $request->input('twitter_card', 'summary_large_image'),
            'schema_type' => $request->input('schema_type', 'BlogPosting'),
            'has_featured_image' => $request->boolean('has_featured_image', false),
        ];

        $postId = $request->input('post_id');
        $categoryId = $request->input('category_id');

        $auditResult = SeoAuditService::audit($data, $postId ? (int) $postId : null);

        // Fetch internal link suggestions
        $linkSuggestions = SeoInternalLinkService::getSuggestions(
            $data['title'],
            $data['content'],
            $categoryId ? (int) $categoryId : null,
            $postId ? (int) $postId : null
        );

        // Deep link grader analysis
        $wordCount = str_word_count(strip_tags($data['content']));
        $linkGrader = \App\Services\Seo\SeoLinkGraderService::grade($data['content'], $wordCount, $postId ? (int) $postId : null);
        $letterGrade = \App\Services\Seo\SeoGraderService::getLetterGrade($auditResult['total_score']);

        return response()->json([
            'success' => true,
            'audit' => $auditResult,
            'link_grader' => $linkGrader,
            'letter_grade' => $letterGrade,
            'link_suggestions' => $linkSuggestions,
        ]);
    }
}
