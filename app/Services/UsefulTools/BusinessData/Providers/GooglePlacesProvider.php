<?php

namespace App\Services\UsefulTools\BusinessData\Providers;

use App\Models\Setting;
use App\Services\UsefulTools\BusinessData\BusinessDataProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GooglePlacesProvider implements BusinessDataProviderInterface
{
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google.places_key') 
            ?: env('GOOGLE_PLACES_API_KEY') 
            ?: Setting::get('google_places_api_key');
    }

    public function getIdentifier(): string
    {
        return 'google_places';
    }

    public function getName(): string
    {
        return 'Google Places & Maps API';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function getStatusDescription(): string
    {
        return $this->isConfigured() 
            ? 'Operational (Google Places Text Search API ready)'
            : 'Requires GOOGLE_PLACES_API_KEY in .env or Settings';
    }

    public function searchBusinesses(array $params): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'records' => [],
                'total_found' => 0,
                'error' => 'Google Places API key is not configured. Please add GOOGLE_PLACES_API_KEY to your .env or Website Settings.',
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
                'metadata' => [],
            ];
        }

        $queryParts = array_filter([
            $params['keyword'] ?? '',
            $params['category'] ?? '',
            $params['city'] ?? '',
            $params['state'] ?? '',
            $params['country'] ?? 'India',
        ]);
        $queryText = implode(' ', $queryParts);
        $maxResults = min((int) ($params['max_results'] ?? 20), 60);

        try {
            $response = Http::timeout(15)
                ->get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
                    'query' => $queryText,
                    'key' => $this->apiKey,
                ]);

            if ($response->failed()) {
                Log::warning('Google Places API HTTP request failed', ['status' => $response->status()]);
                return [
                    'success' => false,
                    'records' => [],
                    'total_found' => 0,
                    'error' => 'Google Places API returned HTTP status: ' . $response->status(),
                    'credits_used' => 1.0,
                    'cost_usd' => 0.032,
                    'metadata' => [],
                ];
            }

            $data = $response->json();
            $status = $data['status'] ?? 'UNKNOWN';

            if ($status !== 'OK' && $status !== 'ZERO_RESULTS') {
                return [
                    'success' => false,
                    'records' => [],
                    'total_found' => 0,
                    'error' => $data['error_message'] ?? "Google Places API Error status: {$status}",
                    'credits_used' => 1.0,
                    'cost_usd' => 0.032,
                    'metadata' => ['status' => $status],
                ];
            }

            $rawResults = $data['results'] ?? [];
            $records = [];

            foreach (array_slice($rawResults, 0, $maxResults) as $place) {
                $location = $place['geometry']['location'] ?? [];
                $records[] = [
                    'source_platform' => 'google_places',
                    'source_record_id' => $place['place_id'] ?? null,
                    'name' => $place['name'] ?? 'Unnamed Business',
                    'category' => !empty($place['types']) ? implode(', ', array_slice($place['types'], 0, 3)) : ($params['category'] ?? null),
                    'description' => null,
                    'address' => $place['formatted_address'] ?? null,
                    'city' => $params['city'] ?? null,
                    'state' => $params['state'] ?? null,
                    'country' => $params['country'] ?? 'India',
                    'postal_code' => null,
                    'phone' => null, // Basic text search does not include direct phone without place details lookup
                    'email' => null,
                    'website' => null,
                    'listing_url' => !empty($place['place_id']) ? 'https://www.google.com/maps/place/?q=place_id:' . urlencode($place['place_id']) : null,
                    'rating' => isset($place['rating']) ? (float) $place['rating'] : null,
                    'review_count' => isset($place['user_ratings_total']) ? (int) $place['user_ratings_total'] : null,
                    'latitude' => $location['lat'] ?? null,
                    'longitude' => $location['lng'] ?? null,
                    'data_quality_status' => !empty($place['formatted_address']) ? 'verified' : 'partial',
                ];
            }

            return [
                'success' => true,
                'records' => $records,
                'total_found' => count($rawResults),
                'error' => null,
                'credits_used' => 1.0,
                'cost_usd' => 0.032, // Official Google Places Text Search cost ~$32 per 1000 requests
                'metadata' => ['places_returned' => count($records)],
            ];
        } catch (\Throwable $e) {
            Log::error('Google Places API Exception', ['exception' => $e->getMessage()]);
            return [
                'success' => false,
                'records' => [],
                'total_found' => 0,
                'error' => 'Connection timeout or error connecting to Google Places API.',
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
                'metadata' => ['exception' => $e->getMessage()],
            ];
        }
    }
}
