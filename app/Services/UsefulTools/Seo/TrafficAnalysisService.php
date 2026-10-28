<?php

namespace App\Services\UsefulTools\Seo;

use App\Models\UsefulTools\UsefulToolCompetitor;
use App\Models\UsefulTools\UsefulToolTrackedKeyword;
use App\Models\UsefulTools\UsefulToolTrafficSnapshot;
use App\Models\UsefulTools\UsefulToolUsageLog;
use App\Services\UsefulTools\Seo\Providers\GoogleAnalytics4Provider;
use App\Services\UsefulTools\Seo\Providers\GoogleSearchConsoleProvider;
use App\Services\UsefulTools\Seo\Providers\SemrushTrafficProvider;
use Illuminate\Support\Facades\Log;

class TrafficAnalysisService
{
    protected SemrushTrafficProvider $semrushProvider;
    protected GoogleSearchConsoleProvider $gscProvider;
    protected GoogleAnalytics4Provider $ga4Provider;

    public function __construct(
        ?SemrushTrafficProvider $semrushProvider = null,
        ?GoogleSearchConsoleProvider $gscProvider = null,
        ?GoogleAnalytics4Provider $ga4Provider = null
    ) {
        $this->semrushProvider = $semrushProvider ?? new SemrushTrafficProvider();
        $this->gscProvider = $gscProvider ?? new GoogleSearchConsoleProvider();
        $this->ga4Provider = $ga4Provider ?? new GoogleAnalytics4Provider();
    }

    /**
     * Get live configuration status of all traffic & analytics providers.
     */
    public function getProvidersStatus(): array
    {
        return [
            'semrush' => [
                'name' => $this->semrushProvider->getName(),
                'is_configured' => $this->semrushProvider->isConfigured(),
                'type' => 'Third-Party Traffic Estimation',
                'description' => $this->semrushProvider->getStatusDescription(),
            ],
            'gsc' => [
                'name' => $this->gscProvider->getName(),
                'is_configured' => $this->gscProvider->isConfigured(),
                'type' => 'Authorized First-Party Search Console',
                'description' => $this->gscProvider->getStatusDescription(),
            ],
            'ga4' => [
                'name' => $this->ga4Provider->getName(),
                'is_configured' => $this->ga4Provider->isConfigured(),
                'type' => 'Authorized First-Party GA4 Analytics',
                'description' => $this->ga4Provider->getStatusDescription(),
            ],
        ];
    }

    /**
     * Fetch domain traffic snapshot and store in DB.
     */
    public function recordThirdPartyTrafficSnapshot(int $userId, string $domain, string $country = 'in'): array
    {
        $cleanDomain = RankTrackingService::validateAndCleanDomain($domain);
        if (!$cleanDomain) {
            return ['success' => false, 'error' => 'Invalid domain or restricted host.'];
        }

        $startTime = microtime(true);
        $result = $this->semrushProvider->getDomainTrafficOverview($cleanDomain, $country);
        $executionTimeMs = (int) round((microtime(true) - $startTime) * 1000);

        UsefulToolUsageLog::logUsage(
            userId: $userId,
            toolType: 'seo_traffic_analyzer',
            provider: $this->semrushProvider->getIdentifier(),
            operation: 'getDomainTrafficOverview',
            query: $cleanDomain,
            recordsCount: $result['success'] ? 1 : 0,
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

        $data = $result['data'];
        $snapshot = UsefulToolTrafficSnapshot::create([
            'user_id' => $userId,
            'domain' => $cleanDomain,
            'data_type' => 'estimated_third_party',
            'provider' => $data['provider'],
            'reporting_period' => $data['reporting_period'],
            'monthly_visits' => $data['monthly_visits'],
            'unique_visitors' => $data['unique_visitors'],
            'organic_traffic' => $data['organic_traffic'],
            'paid_traffic' => $data['paid_traffic'],
            'bounce_rate' => $data['bounce_rate'],
            'avg_visit_duration' => $data['avg_visit_duration'],
            'pages_per_visit' => $data['pages_per_visit'],
            'top_countries' => $data['top_countries'],
            'snapshot_date' => now()->toDateString(),
        ]);

        return [
            'success' => true,
            'snapshot' => $snapshot,
        ];
    }

    /**
     * Generate competitor comparison metrics.
     */
    public function compareDomainWithCompetitors(int $userId, string $mainDomain): array
    {
        $cleanMain = RankTrackingService::validateAndCleanDomain($mainDomain);
        if (!$cleanMain) {
            return ['error' => 'Invalid main domain'];
        }

        $competitors = UsefulToolCompetitor::where('user_id', $userId)
            ->where('main_domain', $cleanMain)
            ->get();

        $mainKeywords = UsefulToolTrackedKeyword::where('user_id', $userId)
            ->where('domain', $cleanMain)
            ->pluck('keyword')
            ->toArray();

        $mainTraffic = UsefulToolTrafficSnapshot::where('user_id', $userId)
            ->where('domain', $cleanMain)
            ->latest('snapshot_date')
            ->first();

        $comparisonList = [];

        foreach ($competitors as $comp) {
            $compDomain = $comp->competitor_domain;
            $compKeywords = UsefulToolTrackedKeyword::where('user_id', $userId)
                ->where('domain', $compDomain)
                ->pluck('keyword')
                ->toArray();

            $overlap = array_intersect($mainKeywords, $compKeywords);
            $uniqueToCompetitor = array_diff($compKeywords, $mainKeywords);

            $compTraffic = UsefulToolTrafficSnapshot::where('user_id', $userId)
                ->where('domain', $compDomain)
                ->latest('snapshot_date')
                ->first();

            $comparisonList[] = [
                'competitor_domain' => $compDomain,
                'total_keywords' => count($compKeywords),
                'shared_keywords_count' => count($overlap),
                'shared_keywords' => array_slice(array_values($overlap), 0, 5),
                'competitor_unique_count' => count($uniqueToCompetitor),
                'estimated_monthly_visits' => $compTraffic ? $compTraffic->monthly_visits : null,
                'traffic_data_freshness' => $compTraffic ? $compTraffic->snapshot_date->format('M Y') : 'N/A',
            ];
        }

        return [
            'main_domain' => $cleanMain,
            'main_keywords_count' => count($mainKeywords),
            'main_estimated_visits' => $mainTraffic ? $mainTraffic->monthly_visits : null,
            'competitors' => $comparisonList,
        ];
    }
}
