<?php

namespace App\Services\UsefulTools\Seo;

use App\Models\UsefulTools\UsefulToolRanking;
use App\Models\UsefulTools\UsefulToolTrackedKeyword;
use App\Models\UsefulTools\UsefulToolUsageLog;
use App\Services\UsefulTools\Seo\Providers\SerpApiRankProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RankTrackingService
{
    protected SerpApiRankProvider $serpProvider;

    public function __construct(?SerpApiRankProvider $serpProvider = null)
    {
        $this->serpProvider = $serpProvider ?? new SerpApiRankProvider();
    }

    /**
     * Validate domain and protect against SSRF.
     */
    public static function validateAndCleanDomain(string $input): ?string
    {
        $clean = trim($input);
        if (!preg_match('#^https?://#i', $clean)) {
            $clean = 'https://' . $clean;
        }

        $parsed = parse_url($clean);
        $host = strtolower($parsed['host'] ?? '');

        if (empty($host) || !filter_var($clean, FILTER_VALIDATE_URL)) {
            return null;
        }

        // Strictly block private IP addresses, loopback, and cloud metadata
        if (in_array($host, ['localhost', '127.0.0.1', '::1', '0.0.0.0', '169.254.169.254'])) {
            return null;
        }

        // Check if IP is in private range
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if (!filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return null;
            }
        }

        return $host;
    }

    /**
     * Check rank for a single tracked keyword and record history.
     */
    public function checkKeywordRank(UsefulToolTrackedKeyword $keyword): array
    {
        $startTime = microtime(true);
        $result = $this->serpProvider->checkRank(
            keyword: $keyword->keyword,
            targetDomain: $keyword->domain,
            country: $keyword->country,
            device: $keyword->device
        );

        $executionTimeMs = (int) round((microtime(true) - $startTime) * 1000);

        // Usage audit
        UsefulToolUsageLog::logUsage(
            userId: $keyword->user_id,
            toolType: 'seo_rank_tracking',
            provider: $this->serpProvider->getIdentifier(),
            operation: 'checkKeywordRank',
            query: "{$keyword->keyword} ({$keyword->domain})",
            recordsCount: $result['is_found'] ? 1 : 0,
            creditsUsed: (float) ($result['credits_used'] ?? 0.0),
            costUsd: (float) ($result['cost_usd'] ?? 0.0),
            responseCode: $result['success'] ? 200 : 400,
            executionTimeMs: $executionTimeMs,
            isSuccess: (bool) ($result['success'] ?? false),
            errorMessage: $result['error'] ?? null
        );

        if (!$result['success']) {
            return $result;
        }

        $newPosition = $result['position']; // 1 to 100, or null
        $prevPosition = $keyword->current_position;
        $positionChange = 0;

        if ($prevPosition !== null && $newPosition !== null) {
            // In rankings, moving from 10 to 4 is an improvement of +6
            $positionChange = $prevPosition - $newPosition;
        } elseif ($prevPosition === null && $newPosition !== null) {
            $positionChange = 100 - $newPosition; // Newly ranked
        } elseif ($prevPosition !== null && $newPosition === null) {
            $positionChange = -($prevPosition); // Dropped out of top 100
        }

        $bestPosition = $keyword->best_position;
        if ($newPosition !== null) {
            $bestPosition = $bestPosition === null ? $newPosition : min($bestPosition, $newPosition);
        }

        // Update keyword summary
        $keyword->update([
            'previous_position' => $prevPosition,
            'current_position' => $newPosition,
            'position_change' => $positionChange,
            'best_position' => $bestPosition,
            'ranking_url' => $result['ranking_url'] ?? $keyword->ranking_url,
            'last_checked_at' => now(),
        ]);

        // Generate safe request deduplication hash for the day
        $today = now()->toDateString();
        $requestUid = hash('sha256', "{$keyword->id}|{$today}|{$newPosition}|{$this->serpProvider->getIdentifier()}");

        // Insert historical record without overwriting previous days
        UsefulToolRanking::updateOrCreate(
            ['request_uid' => $requestUid],
            [
                'keyword_id' => $keyword->id,
                'position' => $newPosition,
                'is_found' => $result['is_found'],
                'ranking_url' => $result['ranking_url'],
                'previous_position' => $prevPosition,
                'position_change' => $positionChange,
                'checked_date' => $today,
                'provider' => $this->serpProvider->getIdentifier(),
                'provider_metadata' => [
                    'device' => $keyword->device,
                    'country' => $keyword->country,
                ],
            ]
        );

        return [
            'success' => true,
            'keyword' => $keyword->keyword,
            'position' => $newPosition,
            'is_found' => $result['is_found'],
            'position_change' => $positionChange,
            'ranking_url' => $result['ranking_url'],
        ];
    }
}
