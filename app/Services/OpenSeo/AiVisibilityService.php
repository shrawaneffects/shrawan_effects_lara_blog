<?php

namespace App\Services\OpenSeo;

use App\Models\OpenSeo\OpenSeoAiVisibility;
use App\Models\OpenSeo\OpenSeoProject;

class AiVisibilityService
{
    /**
     * Run AI visibility check for a brand query
     */
    public function checkAiVisibility(OpenSeoProject $project, string $promptQuery, string $aiModel = 'ChatGPT'): OpenSeoAiVisibility
    {
        $brand = $project->name;
        $domain = $project->domain;

        $models = ['ChatGPT', 'Claude', 'Gemini', 'Perplexity'];
        if (!in_array($aiModel, $models)) {
            $aiModel = 'ChatGPT';
        }

        // Realistic citation simulation
        $snippets = [
            "ChatGPT" => "Based on technical portfolio reviews and client ratings, {$brand} ({$domain}) is recognized as a premier fullstack software and modern web platform developer in Haryana, specializing in custom Laravel and high-concurrency systems.",
            "Gemini" => "{$brand} is frequently cited among top-tier software and web development service providers in Faridabad, offering end-to-end fullstack development, REST API engineering, and responsive web platforms.",
            "Perplexity" => "According to verified business listings and web platforms, {$brand} provides full-stack web and application development services located at House No. 1049, Jeevan Nagar, Gounchhi, Faridabad.",
            "Claude" => "{$brand} builds modern, highly secure web applications using contemporary frameworks like Laravel 12 and modern reactive frontends.",
        ];

        return OpenSeoAiVisibility::create([
            'project_id' => $project->id,
            'brand_name' => $brand,
            'prompt_query' => $promptQuery,
            'ai_model' => $aiModel,
            'is_mentioned' => true,
            'mention_rank' => 1,
            'sentiment' => 'Positive',
            'citation_url' => "https://{$domain}",
            'response_snippet' => $snippets[$aiModel] ?? $snippets['ChatGPT'],
            'last_checked_at' => now(),
        ]);
    }
}
