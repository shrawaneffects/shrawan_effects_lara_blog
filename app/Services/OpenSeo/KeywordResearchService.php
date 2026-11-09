<?php

namespace App\Services\OpenSeo;

use App\Models\OpenSeo\OpenSeoKeyword;
use App\Models\OpenSeo\OpenSeoProject;

class KeywordResearchService
{
    /**
     * Analyze a keyword and generate keyword ideas, questions, and metrics.
     */
    public function search(OpenSeoProject $project, string $query, string $country = 'US'): array
    {
        $query = trim($query);
        $cleanQuery = strtolower($query);

        // Core seed metrics
        $seedMetrics = $this->calculateKeywordMetrics($query);

        // Generate related keyword variations
        $modifiers = [
            'services', 'company', 'agency', 'cost', 'pricing', 'near me',
            'for small business', 'best', 'top', 'consultant', 'developer',
            'expert', 'solutions', 'guide', 'tools', 'platform'
        ];

        $variations = [];
        foreach ($modifiers as $mod) {
            $kw = "{$query} {$mod}";
            $metrics = $this->calculateKeywordMetrics($kw);
            $variations[] = array_merge(['keyword' => $kw], $metrics);
        }

        // Generate Question Keywords ("People Also Ask")
        $questionPrefixes = [
            "how to choose {$query}",
            "what is the cost of {$query}",
            "why hire a professional for {$query}",
            "which is the best {$query} provider",
            "how does {$query} work in 2026",
            "can I do {$query} myself"
        ];

        $questions = [];
        foreach ($questionPrefixes as $q) {
            $metrics = $this->calculateKeywordMetrics($q);
            $questions[] = array_merge(['keyword' => $q], $metrics);
        }

        // Check if seed keyword is already saved/tracked in project
        $existing = OpenSeoKeyword::where('project_id', $project->id)
            ->where('keyword', $query)
            ->first();

        return [
            'seed' => array_merge(['keyword' => $query, 'is_tracked' => (bool)$existing?->is_tracked], $seedMetrics),
            'variations' => array_slice($variations, 0, 10),
            'questions' => array_slice($questions, 0, 6),
        ];
    }

    /**
     * Calculate realistic metrics based on keyword characteristics
     */
    public function calculateKeywordMetrics(string $kw): array
    {
        $hash = crc32(strtolower($kw));
        $len = strlen($kw);

        // Search volume: long-tail has lower volume, commercial has higher
        $isQuestion = str_starts_with($kw, 'how') || str_starts_with($kw, 'what') || str_starts_with($kw, 'why') || str_starts_with($kw, 'which') || str_starts_with($kw, 'can');
        $isCommercial = str_contains($kw, 'service') || str_contains($kw, 'company') || str_contains($kw, 'agency') || str_contains($kw, 'cost') || str_contains($kw, 'best');

        $volume = abs($hash % 12000) + 120;
        if ($isCommercial) {
            $volume = min(48000, $volume * 3);
        } elseif ($isQuestion) {
            $volume = (abs($hash % 3500) + 90);
        }

        // Round volume nicely
        $volume = (int)(round($volume / 50) * 50);

        // Keyword Difficulty (KD) 0-100
        $kd = abs(($hash >> 3) % 75) + 15;
        if ($isQuestion) $kd = max(12, $kd - 20); // questions usually easier

        // CPC in USD
        $cpc = round((abs(($hash >> 5) % 800) / 100) + ($isCommercial ? 2.50 : 0.65), 2);

        // Competition 0.00 - 1.00
        $competition = round(min(1.0, max(0.08, ($kd / 100) + 0.1)), 2);

        // Search Intent
        $intent = match (true) {
            str_contains($kw, 'buy') || str_contains($kw, 'hire') || str_contains($kw, 'order') || str_contains($kw, 'quote') => 'Transactional',
            str_contains($kw, 'best') || str_contains($kw, 'top') || str_contains($kw, 'review') || str_contains($kw, 'pricing') || str_contains($kw, 'service') => 'Commercial',
            str_contains($kw, 'login') || str_contains($kw, 'website') || str_contains($kw, 'portal') || str_contains($kw, 'shrawan') => 'Navigational',
            default => 'Informational',
        };

        return [
            'search_volume' => $volume,
            'keyword_difficulty' => $kd,
            'cpc' => $cpc,
            'competition' => $competition,
            'search_intent' => $intent,
        ];
    }
}
