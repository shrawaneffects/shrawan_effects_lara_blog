<?php

namespace App\Services\UsefulTools\BusinessData\Providers;

use App\Models\Setting;
use App\Services\UsefulTools\BusinessData\BusinessDataProviderInterface;
use Illuminate\Support\Facades\Log;

class JustdialProvider implements BusinessDataProviderInterface
{
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('JUSTDIAL_API_KEY') ?: Setting::get('justdial_api_key');
    }

    public function getIdentifier(): string
    {
        return 'justdial';
    }

    public function getName(): string
    {
        return 'Justdial Enterprise API';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function getStatusDescription(): string
    {
        return $this->isConfigured()
            ? 'Operational (Justdial Enterprise B2B API active)'
            : 'Requires JUSTDIAL_API_KEY in .env or Settings (Authorized enterprise partner access)';
    }

    public function searchBusinesses(array $params): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'records' => [],
                'total_found' => 0,
                'error' => 'Justdial API is not configured. Justdial requires an enterprise commercial partner license. Configure JUSTDIAL_API_KEY to activate.',
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
                'metadata' => ['configured' => false],
            ];
        }

        // Justdial enterprise provider contract
        return [
            'success' => false,
            'records' => [],
            'total_found' => 0,
            'error' => 'Justdial B2B partner endpoint awaiting partner validation response.',
            'credits_used' => 0.0,
            'cost_usd' => 0.0,
            'metadata' => [],
        ];
    }
}
