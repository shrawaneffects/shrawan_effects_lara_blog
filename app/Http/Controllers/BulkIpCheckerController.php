<?php

namespace App\Http\Controllers;

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
        return view('frontend.services.bulk-ip-checker', [
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
