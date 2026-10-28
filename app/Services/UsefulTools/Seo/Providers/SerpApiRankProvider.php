<?php

namespace App\Services\UsefulTools\Seo\Providers;

use App\Models\Setting;
use App\Services\UsefulTools\Seo\SeoDataProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SerpApiRankProvider implements SeoDataProviderInterface
{
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('SERPAPI_API_KEY') ?: Setting::get('serpapi_api_key');
    }

    public function getIdentifier(): string
    {
        return 'serpapi';
    }

    public function getName(): string
    {
        return 'SerpApi Google SERP Engine (1–100)';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function getStatusDescription(): string
    {
        return $this->isConfigured()
            ? 'Operational (Google SERP 1–100 Organic positions ready)'
            : 'Requires SERPAPI_API_KEY in .env or Settings';
    }

    /**
     * Check organic Google rank position between 1 and 100.
     *
     * @param string $keyword
     * @param string $targetDomain
     * @param string $country (e.g. 'in', 'us')
     * @param string $device ('desktop', 'mobile')
     * @return array [
     *     'success' => bool,
     *     'position' => ?int, // 1–100, or null if not in top 100
     *     'is_found' => bool,
     *     'ranking_url' => ?string,
     *     'error' => ?string,
     *     'raw_top_results' => array,
     *     'credits_used' => float,
     *     'cost_usd' => float
     * ]
     */
    public function checkRank(string $keyword, string $targetDomain, string $country = 'in', string $device = 'desktop'): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'position' => null,
                'is_found' => false,
                'ranking_url' => null,
                'error' => 'SerpApi is not configured. Please add SERPAPI_API_KEY to your .env file.',
                'raw_top_results' => [],
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
            ];
        }

        $cleanDomain = strtolower(preg_replace('#^https?://#', '', rtrim($targetDomain, '/')));

        try {
            $params = [
                'engine' => 'google',
                'q' => $keyword,
                'gl' => strtolower($country),
                'hl' => 'en',
                'num' => 100, // Top 100 results depth
                'device' => $device === 'mobile' ? 'mobile' : 'desktop',
                'api_key' => $this->apiKey,
            ];

            $response = Http::timeout(25)->get('https://serpapi.com/search.json', $params);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'position' => null,
                    'is_found' => false,
                    'ranking_url' => null,
                    'error' => 'SerpApi returned HTTP error ' . $response->status(),
                    'raw_top_results' => [],
                    'credits_used' => 1.0,
                    'cost_usd' => 0.01,
                ];
            }

            $data = $response->json();
            $organicResults = $data['organic_results'] ?? [];

            $foundPosition = null;
            $foundUrl = null;

            foreach ($organicResults as $index => $result) {
                $pos = $result['position'] ?? ($index + 1);
                $link = $result['link'] ?? '';

                if (!empty($link)) {
                    $host = strtolower(parse_url($link, PHP_URL_HOST) ?? '');
                    if (str_contains($host, $cleanDomain) || str_contains($cleanDomain, $host)) {
                        $foundPosition = (int) $pos;
                        $foundUrl = $link;
                        break;
                    }
                }
            }

            return [
                'success' => true,
                'position' => $foundPosition, // null if > 100
                'is_found' => $foundPosition !== null,
                'ranking_url' => $foundUrl,
                'error' => null,
                'raw_top_results' => array_slice($organicResults, 0, 10),
                'credits_used' => 1.0,
                'cost_usd' => 0.01,
            ];
        } catch (\Throwable $e) {
            Log::error('SerpApi checkRank Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'position' => null,
                'is_found' => false,
                'ranking_url' => null,
                'error' => 'Failed to connect to SerpApi: ' . $e->getMessage(),
                'raw_top_results' => [],
                'credits_used' => 0.0,
                'cost_usd' => 0.0,
            ];
        }
    }
}
