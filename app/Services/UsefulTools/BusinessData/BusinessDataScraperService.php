<?php

namespace App\Services\UsefulTools\BusinessData;

use App\Models\UsefulTools\UsefulToolBusiness;
use App\Models\UsefulTools\UsefulToolScrapingJob;
use App\Models\UsefulTools\UsefulToolUsageLog;
use App\Services\UsefulTools\BusinessData\Providers\GooglePlacesProvider;
use App\Services\UsefulTools\BusinessData\Providers\GoogleSearchProvider;
use App\Services\UsefulTools\BusinessData\Providers\IndiaMartProvider;
use App\Services\UsefulTools\BusinessData\Providers\JustdialProvider;
use App\Services\UsefulTools\BusinessData\Providers\YelpProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BusinessDataScraperService
{
    /**
     * @var array<string, BusinessDataProviderInterface>
     */
    protected array $providers = [];

    public function __construct()
    {
        $this->registerProvider(new GooglePlacesProvider());
        $this->registerProvider(new GoogleSearchProvider());
        $this->registerProvider(new YelpProvider());
        $this->registerProvider(new IndiaMartProvider());
        $this->registerProvider(new JustdialProvider());
    }

    public function registerProvider(BusinessDataProviderInterface $provider): void
    {
        $this->providers[$provider->getIdentifier()] = $provider;
    }

    /**
     * Get all registered providers and their live configuration status.
     *
     * @return array<string, array>
     */
    public function getAvailableProviders(): array
    {
        $list = [];
        foreach ($this->providers as $key => $provider) {
            $list[$key] = [
                'identifier' => $provider->getIdentifier(),
                'name' => $provider->getName(),
                'is_configured' => $provider->isConfigured(),
                'description' => $provider->getStatusDescription(),
            ];
        }
        return $list;
    }

    public function getProvider(string $identifier): ?BusinessDataProviderInterface
    {
        return $this->providers[$identifier] ?? null;
    }

    /**
     * Process a scraping job either synchronously or asynchronously.
     */
    public function executeJob(UsefulToolScrapingJob $job): array
    {
        $startTime = microtime(true);
        $provider = $this->getProvider($job->provider);

        if (!$provider) {
            $job->update([
                'status' => 'failed',
                'error_message' => "Requested provider '{$job->provider}' is not supported.",
                'completed_at' => now(),
            ]);
            return ['success' => false, 'error' => "Provider {$job->provider} not found."];
        }

        $job->update([
            'status' => 'running',
            'started_at' => now(),
            'progress_percent' => 15,
        ]);

        $searchParams = [
            'keyword' => $job->query_keyword,
            'category' => $job->category,
            'city' => $job->city,
            'state' => $job->state,
            'country' => $job->country,
            'max_results' => $job->max_results,
        ];

        $result = $provider->searchBusinesses($searchParams);
        $executionTimeMs = (int) round((microtime(true) - $startTime) * 1000);

        // Record usage audit log
        UsefulToolUsageLog::logUsage(
            userId: $job->user_id,
            toolType: 'business_scraper',
            provider: $job->provider,
            operation: 'searchBusinesses',
            query: "{$job->query_keyword} ({$job->city})",
            recordsCount: count($result['records'] ?? []),
            creditsUsed: (float) ($result['credits_used'] ?? 0.0),
            costUsd: (float) ($result['cost_usd'] ?? 0.0),
            responseCode: $result['success'] ? 200 : 400,
            executionTimeMs: $executionTimeMs,
            isSuccess: (bool) ($result['success'] ?? false),
            errorMessage: $result['error'] ?? null
        );

        if (!$result['success']) {
            $job->update([
                'status' => 'failed',
                'error_message' => $result['error'] ?? 'Unknown provider error occurred.',
                'completed_at' => now(),
            ]);
            return $result;
        }

        $rawRecords = $result['records'] ?? [];
        $collectedCount = 0;

        foreach ($rawRecords as $record) {
            $identifier = UsefulToolBusiness::generateIdentifier(
                name: $record['name'],
                city: $record['city'] ?? $job->city,
                phone: $record['phone'] ?? null,
                website: $record['website'] ?? null
            );

            // Safe deduplication: check if already exists for this user
            $existing = UsefulToolBusiness::where('user_id', $job->user_id)
                ->where('normalized_identifier', $identifier)
                ->first();

            if (!$existing) {
                UsefulToolBusiness::create([
                    'user_id' => $job->user_id,
                    'job_id' => $job->id,
                    'project_id' => $job->project_id,
                    'source_platform' => $record['source_platform'],
                    'source_record_id' => $record['source_record_id'] ?? null,
                    'normalized_identifier' => $identifier,
                    'name' => $record['name'],
                    'category' => $record['category'] ?? $job->category,
                    'description' => $record['description'] ?? null,
                    'address' => $record['address'] ?? null,
                    'city' => $record['city'] ?? $job->city,
                    'state' => $record['state'] ?? $job->state,
                    'country' => $record['country'] ?? $job->country,
                    'postal_code' => $record['postal_code'] ?? null,
                    'phone' => $record['phone'] ?? null,
                    'email' => $record['email'] ?? null,
                    'website' => $record['website'] ?? null,
                    'listing_url' => $record['listing_url'] ?? null,
                    'rating' => $record['rating'] ?? null,
                    'review_count' => $record['review_count'] ?? null,
                    'latitude' => $record['latitude'] ?? null,
                    'longitude' => $record['longitude'] ?? null,
                    'data_quality_status' => $record['data_quality_status'] ?? 'partial',
                    'first_discovered_at' => now(),
                    'last_checked_at' => now(),
                ]);
                $collectedCount++;
            } else {
                // Update freshness timestamp and rating without corrupting original data
                $existing->update([
                    'last_checked_at' => now(),
                    'rating' => $record['rating'] ?? $existing->rating,
                    'review_count' => $record['review_count'] ?? $existing->review_count,
                ]);
            }
        }

        $job->update([
            'status' => 'completed',
            'total_found' => (int) ($result['total_found'] ?? count($rawRecords)),
            'total_collected' => $collectedCount,
            'progress_percent' => 100,
            'completed_at' => now(),
        ]);

        return [
            'success' => true,
            'total_found' => $result['total_found'] ?? count($rawRecords),
            'total_collected' => $collectedCount,
            'duplicates_skipped' => count($rawRecords) - $collectedCount,
            'job_id' => $job->id,
        ];
    }
}
