<?php

namespace App\Services\UsefulTools\BusinessData\Providers;

use App\Models\Setting;
use App\Services\UsefulTools\BusinessData\BusinessDataProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndiaMartProvider implements BusinessDataProviderInterface
{
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('INDIAMART_API_KEY') ?: Setting::get('indiamart_api_key');
    }

    public function getIdentifier(): string
    {
        return 'indiamart';
    }

    public function getName(): string
    {
        return 'IndiaMART B2B Gateway';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function getStatusDescription(): string
    {
        return $this->isConfigured()
            ? 'Operational (IndiaMART API connected)'
            : 'Requires INDIAMART_API_KEY / CRM Vendor Key in .env';
    }

    public function searchBusinesses(array $params): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'records' => [],
                'total_found' => 0,
                'error' => 'IndiaMART API Key is not configured. Add INDIAMART_API_KEY to your .env to enable authorized B2B catalog lookups.',
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
                'metadata' => ['configured' => false],
            ];
        }

        try {
            // Authorized IndiaMART CRM endpoint
            $response = Http::timeout(15)->post('https://mapi.indiamart.com/wservce/enquiry/listing/GLUSR_MOBILE_KEY/' . urlencode($this->apiKey) . '/');

            if ($response->failed()) {
                return [
                    'success' => false,
                    'records' => [],
                    'total_found' => 0,
                    'error' => 'IndiaMART API returned HTTP status ' . $response->status(),
                    'credits_used' => 1.0,
                    'cost_usd' => 0.0,
                    'metadata' => [],
                ];
            }

            $data = $response->json();
            $records = [];
            $items = $data['RESPONSE'] ?? [];

            foreach ($items as $item) {
                $records[] = [
                    'source_platform' => 'indiamart',
                    'source_record_id' => $item['UNIQUEQUERYID'] ?? null,
                    'name' => $item['SENDERNAME'] ?? ($item['GLUSR_USR_COMPANYNAME'] ?? 'IndiaMART Verified Supplier'),
                    'category' => $item['QUERY_PRODUCT_NAME'] ?? ($params['category'] ?? 'Industrial / Manufacturing'),
                    'description' => $item['ENQ_MESSAGE'] ?? null,
                    'address' => $item['ENQ_ADDRESS'] ?? null,
                    'city' => $item['ENQ_CITY'] ?? ($params['city'] ?? null),
                    'state' => $item['ENQ_STATE'] ?? ($params['state'] ?? null),
                    'country' => 'India',
                    'postal_code' => null,
                    'phone' => $item['MOB'] ?? null,
                    'email' => $item['EMAIL'] ?? null,
                    'website' => null,
                    'listing_url' => 'https://www.indiamart.com',
                    'rating' => null,
                    'review_count' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'data_quality_status' => !empty($item['MOB']) ? 'verified' : 'partial',
                ];
            }

            return [
                'success' => true,
                'records' => $records,
                'total_found' => count($records),
                'error' => null,
                'credits_used' => 1.0,
                'cost_usd' => 0.0,
                'metadata' => ['code' => $data['CODE'] ?? 200],
            ];
        } catch (\Throwable $e) {
            Log::error('IndiaMART API Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'records' => [],
                'total_found' => 0,
                'error' => 'Connection error to IndiaMART API: ' . $e->getMessage(),
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
                'metadata' => [],
            ];
        }
    }
}
