<?php

namespace App\Services\OpenSeo;

use App\Models\OpenSeo\OpenSeoProject;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DataForSeoClient
{
    protected ?string $login;
    protected ?string $password;
    protected string $baseUrl = 'https://api.dataforseo.com/v3/';

    public function __construct(?OpenSeoProject $project = null)
    {
        $this->login = $project?->dataforseo_login ?: config('services.dataforseo.login', env('DATAFORSEO_LOGIN'));
        $this->password = $project?->dataforseo_password ?: config('services.dataforseo.password', env('DATAFORSEO_PASSWORD'));
    }

    public function isConfigured(): bool
    {
        return !empty($this->login) && !empty($this->password);
    }

    /**
     * Post request to DataForSEO endpoint
     */
    public function post(string $endpoint, array $payload): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->login, $this->password)
                ->timeout(30)
                ->post($this->baseUrl . ltrim($endpoint, '/'), $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("DataForSEO API error: [{$response->status()}] {$response->body()}");
            return null;
        } catch (\Throwable $e) {
            Log::error("DataForSEO exception: " . $e->getMessage());
            return null;
        }
    }
}
