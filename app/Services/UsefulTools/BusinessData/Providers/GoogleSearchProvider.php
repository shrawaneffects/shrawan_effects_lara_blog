<?php

namespace App\Services\UsefulTools\BusinessData\Providers;

use App\Models\Setting;
use App\Services\UsefulTools\BusinessData\BusinessDataProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSearchProvider implements BusinessDataProviderInterface
{
    protected ?string $apiKey;
    protected ?string $cx;

    public function __construct()
    {
        $this->apiKey = env('GOOGLE_SEARCH_API_KEY') ?: Setting::get('google_search_api_key');
        $this->cx = env('GOOGLE_SEARCH_CX') ?: Setting::get('google_search_cx');
    }

    public function getIdentifier(): string
    {
        return 'google_search';
    }

    public function getName(): string
    {
        return 'Google Custom Search API';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->cx);
    }

    public function getStatusDescription(): string
    {
        return $this->isConfigured()
            ? 'Operational (Google Custom Search JSON API ready)'
            : 'Requires GOOGLE_SEARCH_API_KEY and GOOGLE_SEARCH_CX in .env';
    }

    public function searchBusinesses(array $params): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'records' => [],
                'total_found' => 0,
                'error' => 'Google Custom Search API key or Search Engine ID (CX) is not configured.',
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
                'metadata' => [],
            ];
        }

        $query = trim(($params['keyword'] ?? '') . ' ' . ($params['city'] ?? '') . ' ' . ($params['country'] ?? ''));

        try {
            $response = Http::timeout(15)->get('https://www.googleapis.com/customsearch/v1', [
                'key' => $this->apiKey,
                'cx' => $this->cx,
                'q' => $query,
                'num' => min((int) ($params['max_results'] ?? 10), 10),
            ]);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'records' => [],
                    'total_found' => 0,
                    'error' => 'Google Custom Search returned HTTP error: ' . $response->status(),
                    'credits_used' => 1.0,
                    'cost_usd' => 0.005,
                    'metadata' => [],
                ];
            }

            $data = $response->json();
            $items = $data['items'] ?? [];
            $records = [];

            foreach ($items as $item) {
                $records[] = [
                    'source_platform' => 'google_search',
                    'source_record_id' => md5($item['link'] ?? Str::random(10)),
                    'name' => $item['title'] ?? 'Business Entity',
                    'category' => $params['category'] ?? null,
                    'description' => $item['snippet'] ?? null,
                    'address' => null,
                    'city' => $params['city'] ?? null,
                    'state' => $params['state'] ?? null,
                    'country' => $params['country'] ?? 'India',
                    'postal_code' => null,
                    'phone' => null,
                    'email' => null,
                    'website' => $item['link'] ?? null,
                    'listing_url' => $item['link'] ?? null,
                    'rating' => null,
                    'review_count' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'data_quality_status' => 'partial',
                ];
            }

            return [
                'success' => true,
                'records' => $records,
                'total_found' => (int) ($data['searchInformation']['totalResults'] ?? count($records)),
                'error' => null,
                'credits_used' => 1.0,
                'cost_usd' => 0.005,
                'metadata' => ['total_results' => $data['searchInformation']['totalResults'] ?? 0],
            ];
        } catch (\Throwable $e) {
            Log::error('Google Search API Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'records' => [],
                'total_found' => 0,
                'error' => 'Failed to connect to Google Search API: ' . $e->getMessage(),
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
                'metadata' => [],
            ];
        }
    }
}
