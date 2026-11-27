<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tool;
use App\Services\BulkIpCheckerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BulkIpCheckerController extends Controller
{
    public function __construct(
        private readonly BulkIpCheckerService $ipCheckerService
    ) {
    }

    /**
     * Render the Bulk Website IP Checker tool page.
     */
    public function index(): View
    {
        $tool = null;
        $relatedTools = collect();
        $relatedPosts = collect();

        try {
            $tool = Tool::where('slug', 'bulk-website-ip-checker')->first();
            if ($tool) {
                $relatedTools = $tool->related_tools_list;
                $relatedPosts = $tool->related_posts_list;
            } else {
                $relatedTools = Tool::active()->where('slug', '!=', 'bulk-website-ip-checker')->limit(4)->get();
                if (class_exists(Post::class)) {
                    $relatedPosts = Post::published()->latest('published_at')->limit(4)->get();
                }
            }
        } catch (\Throwable $e) {}

        return view('frontend.services.bulk-ip-checker', [
            'tool' => $tool,
            'relatedTools' => $relatedTools,
            'relatedPosts' => $relatedPosts,
            'maxBatchSize' => BulkIpCheckerService::MAX_BATCH_SIZE,
            'recommendedBatchSize' => BulkIpCheckerService::RECOMMENDED_BATCH_SIZE,
            'recommendedConcurrency' => BulkIpCheckerService::RECOMMENDED_CONCURRENCY,
            'resolveUrl' => route('services.bulk-ip-checker.resolve'),
            'ipTypeLabels' => BulkIpCheckerService::TYPE_LABELS,
            'sampleDomains' => [
                'https://shrawaneffects.com',
                'https://www.shrawaneffects.com',
                'https://google.com',
                'https://cloudflare.com',
                'https://github.com',
                'https://laravel.com',
                'https://digitalocean.com',
                'https://hetzner.com',
                'https://vercel.com',
                'https://microsoft.com',
            ],
        ]);
    }

    /**
     * Stateless batch JSON endpoint resolving up to 50 domains/URLs per request.
     */
    public function resolve(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'domains' => ['required', 'array', 'min:1', 'max:' . BulkIpCheckerService::MAX_BATCH_SIZE],
            'domains.*' => ['nullable', 'string', 'max:2048'],
            'deduplicate' => ['sometimes', 'boolean'],
            'enrich_metadata' => ['sometimes', 'boolean'],
        ]);

        // Prevent PHP max_execution_time termination on slower shared-hosting DNS paths
        if (function_exists('set_time_limit')) {
            @set_time_limit(60);
        }

        $result = $this->ipCheckerService->resolveBatch(
            $validated['domains'],
            (bool) ($validated['deduplicate'] ?? false),
            (bool) ($validated['enrich_metadata'] ?? true)
        );

        return response()->json($result);
    }
}
