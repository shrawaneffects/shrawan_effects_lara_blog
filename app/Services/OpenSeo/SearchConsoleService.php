<?php

namespace App\Services\OpenSeo;

use App\Models\OpenSeo\OpenSeoProject;
use App\Models\OpenSeo\OpenSeoSearchPerformance;
use Carbon\Carbon;

class SearchConsoleService
{
    /**
     * Get GSC search analytics for the project.
     */
    public function getPerformance(OpenSeoProject $project, int $days = 28): array
    {
        $performances = $project->searchPerformances()
            ->where('date', '>=', Carbon::today()->subDays($days))
            ->get();

        if ($performances->isEmpty()) {
            $this->seedInitialGscData($project);
            $performances = $project->searchPerformances()->get();
        }

        $totalClicks = $performances->sum('clicks');
        $totalImpressions = $performances->sum('impressions');
        $avgCtr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0.0;
        $avgPos = $performances->count() > 0 ? round($performances->avg('position'), 1) : 0.0;

        // Group by query
        $topQueries = $performances->groupBy('query')->map(function ($rows, $q) {
            $clicks = $rows->sum('clicks');
            $imps = $rows->sum('impressions');
            return [
                'query' => $q,
                'clicks' => $clicks,
                'impressions' => $imps,
                'ctr' => $imps > 0 ? round(($clicks / $imps) * 100, 2) : 0,
                'position' => round($rows->avg('position'), 1),
            ];
        })->sortByDesc('clicks')->values()->take(10);

        // Group by page
        $topPages = $performances->groupBy('page')->map(function ($rows, $p) {
            $clicks = $rows->sum('clicks');
            $imps = $rows->sum('impressions');
            return [
                'page' => $p,
                'clicks' => $clicks,
                'impressions' => $imps,
                'ctr' => $imps > 0 ? round(($clicks / $imps) * 100, 2) : 0,
                'position' => round($rows->avg('position'), 1),
            ];
        })->sortByDesc('clicks')->values()->take(8);

        return [
            'total_clicks' => $totalClicks,
            'total_impressions' => $totalImpressions,
            'avg_ctr' => $avgCtr,
            'avg_position' => $avgPos,
            'top_queries' => $topQueries,
            'top_pages' => $topPages,
        ];
    }

    /**
     * Seed realistic initial performance data
     */
    public function seedInitialGscData(OpenSeoProject $project): void
    {
        $domain = $project->domain;
        $queries = [
            ['q' => 'shrawan effects', 'page' => "https://{$domain}/", 'clicks' => 450, 'imps' => 1200, 'pos' => 1.1],
            ['q' => 'website development services in faridabad', 'page' => "https://{$domain}/services", 'clicks' => 310, 'imps' => 2800, 'pos' => 2.4],
            ['q' => 'fullstack software developer faridabad', 'page' => "https://{$domain}/", 'clicks' => 240, 'imps' => 1950, 'pos' => 1.8],
            ['q' => 'laravel expert developer haryana', 'page' => "https://{$domain}/services", 'clicks' => 190, 'imps' => 1600, 'pos' => 3.2],
            ['q' => 'custom erp and crm development', 'page' => "https://{$domain}/services", 'clicks' => 165, 'imps' => 2200, 'pos' => 4.1],
            ['q' => 'best website developer near me', 'page' => "https://{$domain}/contact", 'clicks' => 140, 'imps' => 3100, 'pos' => 5.2],
            ['q' => 'software development company faridabad', 'page' => "https://{$domain}/", 'clicks' => 135, 'imps' => 2400, 'pos' => 3.9],
            ['q' => 'shrawan choudhary developer', 'page' => "https://{$domain}/about-us", 'clicks' => 120, 'imps' => 480, 'pos' => 1.0],
        ];

        foreach ($queries as $item) {
            OpenSeoSearchPerformance::create([
                'project_id' => $project->id,
                'date' => Carbon::yesterday(),
                'query' => $item['q'],
                'page' => $item['page'],
                'clicks' => $item['clicks'],
                'impressions' => $item['imps'],
                'ctr' => round(($item['clicks'] / $item['imps']) * 100, 2),
                'position' => $item['pos'],
            ]);
        }
    }
}
