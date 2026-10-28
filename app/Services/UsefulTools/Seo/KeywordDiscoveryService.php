<?php

namespace App\Services\UsefulTools\Seo;

use App\Models\UsefulTools\UsefulToolTrackedKeyword;
use App\Models\UsefulTools\UsefulToolUsageLog;
use App\Services\UsefulTools\Seo\Providers\DataForSeoRankProvider;
use Illuminate\Support\Facades\Log;

class KeywordDiscoveryService
{
    protected DataForSeoRankProvider $dataForSeoProvider;

    public function __construct(?DataForSeoRankProvider $dataForSeoProvider = null)
    {
        $this->dataForSeoProvider = $dataForSeoProvider ?? new DataForSeoRankProvider();
    }

    /**
     * Discover keywords for a domain and save to tracked keyword database.
     */
    public function discoverAndSaveKeywords(int $userId, string $domain, ?int $projectId = null, string $country = 'in'): array
    {
        $cleanDomain = RankTrackingService::validateAndCleanDomain($domain);
        if (!$cleanDomain) {
            return [
                'success' => false,
                'count' => 0,
                'error' => 'Invalid domain entered or restricted internal host.',
            ];
        }

        $startTime = microtime(true);
        $result = $this->dataForSeoProvider->discoverDomainKeywords($cleanDomain, $country);
        $executionTimeMs = (int) round((microtime(true) - $startTime) * 1000);

        UsefulToolUsageLog::logUsage(
            userId: $userId,
            toolType: 'seo_keyword_discovery',
            provider: $this->dataForSeoProvider->getIdentifier(),
            operation: 'discoverDomainKeywords',
            query: $cleanDomain,
            recordsCount: count($result['keywords'] ?? []),
            creditsUsed: (float) ($result['credits_used'] ?? 0.0),
            costUsd: (float) ($result['cost_usd'] ?? 0.0),
            responseCode: $result['success'] ? 200 : 400,
            executionTimeMs: $executionTimeMs,
            isSuccess: (bool) ($result['success'] ?? false),
            errorMessage: $result['error'] ?? null
        );

        if (!$result['success']) {
            return [
                'success' => false,
                'count' => 0,
                'error' => $result['error'] ?? 'Keyword discovery provider error.',
            ];
        }

        $addedCount = 0;
        foreach ($result['keywords'] as $item) {
            $existing = UsefulToolTrackedKeyword::where('user_id', $userId)
                ->where('domain', $cleanDomain)
                ->where('keyword', $item['keyword'])
                ->first();

            if (!$existing) {
                UsefulToolTrackedKeyword::create([
                    'user_id' => $userId,
                    'project_id' => $projectId,
                    'domain' => $cleanDomain,
                    'keyword' => $item['keyword'],
                    'country' => $country,
                    'current_position' => $item['current_position'] > 0 ? $item['current_position'] : null,
                    'ranking_url' => $item['ranking_url'] ?? null,
                    'search_volume' => $item['search_volume'] ?? null,
                    'keyword_difficulty' => $item['keyword_difficulty'] ?? null,
                    'cpc' => $item['cpc'] ?? null,
                    'search_intent' => $item['search_intent'] ?? null,
                    'last_checked_at' => now(),
                    'is_active' => true,
                ]);
                $addedCount++;
            }
        }

        return [
            'success' => true,
            'count' => $addedCount,
            'total_discovered' => count($result['keywords']),
            'domain' => $cleanDomain,
        ];
    }
}
