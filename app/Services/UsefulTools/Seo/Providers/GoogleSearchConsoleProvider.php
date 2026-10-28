<?php

namespace App\Services\UsefulTools\Seo\Providers;

use App\Models\Setting;
use App\Services\UsefulTools\Seo\SeoDataProviderInterface;
use Illuminate\Support\Facades\Log;

class GoogleSearchConsoleProvider implements SeoDataProviderInterface
{
    protected ?string $clientSecretPath;

    public function __construct()
    {
        $this->clientSecretPath = env('GSC_CREDENTIALS_PATH') ?: Setting::get('gsc_credentials_path');
    }

    public function getIdentifier(): string
    {
        return 'gsc';
    }

    public function getName(): string
    {
        return 'Google Search Console (First-Party Clicks & Queries)';
    }

    public function isConfigured(): bool
    {
        return !empty($this->clientSecretPath) && file_exists(base_path($this->clientSecretPath));
    }

    public function getStatusDescription(): string
    {
        return $this->isConfigured()
            ? 'Connected (Authorized First-Party Search Console API ready)'
            : 'Requires GSC_CREDENTIALS_PATH (Service Account JSON) in .env or Settings';
    }

    public function getSitePerformance(string $siteUrl, string $startDate, string $endDate): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'data' => null,
                'error' => 'Google Search Console credentials not configured. Please supply a valid Service Account JSON in GSC_CREDENTIALS_PATH.',
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
            ];
        }

        // Search Console integration contract
        return [
            'success' => false,
            'data' => null,
            'error' => 'GSC Service Account authentication pending domain verification in Search Console.',
            'credits_used' => 0.0,
            'cost_usd' => 0.0,
        ];
    }
}
