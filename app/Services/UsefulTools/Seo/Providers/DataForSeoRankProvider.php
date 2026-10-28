<?php

namespace App\Services\UsefulTools\Seo\Providers;

use App\Models\Setting;
use App\Services\UsefulTools\Seo\SeoDataProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DataForSeoRankProvider implements SeoDataProviderInterface
{
    protected ?string $login;
    protected ?string $password;

    public function __construct()
    {
        $this->login = env('DATAFORSEO_LOGIN') ?: Setting::get('dataforseo_login');
        $this->password = env('DATAFORSEO_PASSWORD') ?: Setting::get('dataforseo_password');
    }

    public function getIdentifier(): string
    {
        return 'dataforseo';
    }

    public function getName(): string
    {
        return 'DataForSEO (Keywords & 1–100 Rank)';
    }

    public function isConfigured(): bool
    {
        return !empty($this->login) && !empty($this->password);
    }

    public function getStatusDescription(): string
    {
        return $this->isConfigured()
            ? 'Operational (DataForSEO SERP & Ranked Keywords API active)'
            : 'Requires DATAFORSEO_LOGIN and DATAFORSEO_PASSWORD in .env';
    }

    /**
     * Discover ranked keywords for a domain.
     */
    public function discoverDomainKeywords(string $domain, string $country = 'in', int $limit = 50): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'keywords' => [],
                'error' => 'DataForSEO credentials are not configured.',
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
            ];
        }

        $cleanDomain = strtolower(preg_replace('#^https?://#', '', rtrim($domain, '/')));

        try {
            $response = Http::withBasicAuth($this->login, $this->password)
                ->timeout(25)
                ->post('https://api.dataforseo.com/v3/dataforseo_labs/google/ranked_keywords/live', [
                    [
                        'target' => $cleanDomain,
                        'location_code' => $country === 'in' ? 2356 : 2840,
                        'language_code' => 'en',
                        'limit' => min($limit, 100),
                    ]
                ]);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'keywords' => [],
                    'error' => 'DataForSEO returned HTTP status: ' . $response->status(),
                    'credits_used' => 1.0,
                    'cost_usd' => 0.02,
                ];
            }

            $data = $response->json();
            $tasks = $data['tasks'][0]['result'][0]['items'] ?? [];
            $keywords = [];

            foreach ($tasks as $item) {
                $kwData = $item['keyword_data'] ?? [];
                $rankingInfo = $item['ranked_serp_element']['serp_item'] ?? [];

                $keywords[] = [
                    'keyword' => $kwData['keyword'] ?? '',
                    'current_position' => (int) ($rankingInfo['rank_group'] ?? $rankingInfo['rank_absolute'] ?? 0),
                    'ranking_url' => $rankingInfo['url'] ?? null,
                    'search_volume' => (int) ($kwData['keyword_info']['search_volume'] ?? 0),
                    'keyword_difficulty' => (int) ($kwData['keyword_properties']['keyword_difficulty'] ?? 0),
                    'cpc' => (float) ($kwData['keyword_info']['cpc'] ?? 0.0),
                    'search_intent' => $kwData['search_intent_info']['main_intent'] ?? null,
                ];
            }

            return [
                'success' => true,
                'keywords' => $keywords,
                'error' => null,
                'credits_used' => 1.0,
                'cost_usd' => 0.02,
            ];
        } catch (\Throwable $e) {
            Log::error('DataForSEO Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'keywords' => [],
                'error' => 'Connection error to DataForSEO: ' . $e->getMessage(),
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
            ];
        }
    }
}
