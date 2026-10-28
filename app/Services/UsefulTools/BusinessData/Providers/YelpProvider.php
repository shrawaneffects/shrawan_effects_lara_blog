<?php

namespace App\Services\UsefulTools\BusinessData\Providers;

use App\Models\Setting;
use App\Services\UsefulTools\BusinessData\BusinessDataProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YelpProvider implements BusinessDataProviderInterface
{
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('YELP_API_KEY') ?: Setting::get('yelp_api_key');
    }

    public function getIdentifier(): string
    {
        return 'yelp';
    }

    public function getName(): string
    {
        return 'Yelp Fusion API';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function getStatusDescription(): string
    {
        return $this->isConfigured()
            ? 'Operational (Yelp Fusion Business Search ready)'
            : 'Requires YELP_API_KEY in .env';
    }

    public function searchBusinesses(array $params): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'records' => [],
                'total_found' => 0,
                'error' => 'Yelp API Key is not configured. Please add YELP_API_KEY to your .env file.',
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
                'metadata' => [],
            ];
        }

        $term = trim(($params['keyword'] ?? '') . ' ' . ($params['category'] ?? ''));
        $location = trim(($params['city'] ?? '') . ', ' . ($params['country'] ?? ''));
        if (empty($location)) {
            $location = 'India';
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(15)
                ->get('https://api.yelp.com/v3/businesses/search', [
                    'term' => $term,
                    'location' => $location,
                    'limit' => min((int) ($params['max_results'] ?? 20), 50),
                ]);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'records' => [],
                    'total_found' => 0,
                    'error' => 'Yelp API returned HTTP status: ' . $response->status(),
                    'credits_used' => 1.0,
                    'cost_usd' => 0.0,
                    'metadata' => [],
                ];
            }

            $data = $response->json();
            $businesses = $data['businesses'] ?? [];
            $records = [];

            foreach ($businesses as $b) {
                $locationData = $b['location'] ?? [];
                $coordinates = $b['coordinates'] ?? [];

                $records[] = [
                    'source_platform' => 'yelp',
                    'source_record_id' => $b['id'] ?? null,
                    'name' => $b['name'] ?? 'Unnamed Business',
                    'category' => !empty($b['categories']) ? implode(', ', array_column($b['categories'], 'title')) : null,
                    'description' => null,
                    'address' => !empty($locationData['display_address']) ? implode(', ', $locationData['display_address']) : ($locationData['address1'] ?? null),
                    'city' => $locationData['city'] ?? ($params['city'] ?? null),
                    'state' => $locationData['state'] ?? ($params['state'] ?? null),
                    'country' => $locationData['country'] ?? ($params['country'] ?? 'India'),
                    'postal_code' => $locationData['zip_code'] ?? null,
                    'phone' => $b['display_phone'] ?? ($b['phone'] ?? null),
                    'email' => null,
                    'website' => $b['url'] ?? null,
                    'listing_url' => $b['url'] ?? null,
                    'rating' => isset($b['rating']) ? (float) $b['rating'] : null,
                    'review_count' => isset($b['review_count']) ? (int) $b['review_count'] : null,
                    'latitude' => $coordinates['latitude'] ?? null,
                    'longitude' => $coordinates['longitude'] ?? null,
                    'data_quality_status' => !empty($b['phone']) ? 'verified' : 'partial',
                ];
            }

            return [
                'success' => true,
                'records' => $records,
                'total_found' => (int) ($data['total'] ?? count($records)),
                'error' => null,
                'credits_used' => 1.0,
                'cost_usd' => 0.0, // Yelp offers free tier calls
                'metadata' => ['total' => $data['total'] ?? 0],
            ];
        } catch (\Throwable $e) {
            Log::error('Yelp API Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'records' => [],
                'total_found' => 0,
                'error' => 'Failed to connect to Yelp Fusion API: ' . $e->getMessage(),
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
                'metadata' => [],
            ];
        }
    }
}
