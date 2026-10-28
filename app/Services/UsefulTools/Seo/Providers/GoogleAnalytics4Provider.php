<?php

namespace App\Services\UsefulTools\Seo\Providers;

use App\Models\Setting;
use App\Services\UsefulTools\Seo\SeoDataProviderInterface;
use Illuminate\Support\Facades\Log;

class GoogleAnalytics4Provider implements SeoDataProviderInterface
{
    protected ?string $propertyId;
    protected ?string $credentialsPath;

    public function __construct()
    {
        $this->propertyId = env('GA4_PROPERTY_ID') ?: Setting::get('ga4_property_id');
        $this->credentialsPath = env('GA4_CREDENTIALS_PATH') ?: Setting::get('ga4_credentials_path');
    }

    public function getIdentifier(): string
    {
        return 'ga4';
    }

    public function getName(): string
    {
        return 'Google Analytics 4 (First-Party Traffic & Sessions)';
    }

    public function isConfigured(): bool
    {
        return !empty($this->propertyId) && !empty($this->credentialsPath) && file_exists(base_path($this->credentialsPath));
    }

    public function getStatusDescription(): string
    {
        return $this->isConfigured()
            ? 'Connected (Authorized GA4 RunReport API ready)'
            : 'Requires GA4_PROPERTY_ID and GA4_CREDENTIALS_PATH in .env or Settings';
    }

    public function getTrafficMetrics(string $startDate, string $endDate): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'data' => null,
                'error' => 'Google Analytics 4 is not configured. Add GA4_PROPERTY_ID and GA4_CREDENTIALS_PATH to .env to connect live sessions.',
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
            ];
        }

        // GA4 integration contract
        return [
            'success' => false,
            'data' => null,
            'error' => 'GA4 Service Account authentication pending property access grant.',
            'credits_used' => 0.0,
            'cost_usd' => 0.0,
        ];
    }
}
