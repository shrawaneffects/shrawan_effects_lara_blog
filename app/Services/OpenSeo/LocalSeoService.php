<?php

namespace App\Services\OpenSeo;

use App\Models\OpenSeo\OpenSeoLocalTracker;
use App\Models\OpenSeo\OpenSeoProject;

class LocalSeoService
{
    /**
     * Create or scan a Local SEO geo-grid rank tracker.
     */
    public function scanGrid(OpenSeoProject $project, string $businessName, string $keyword, ?string $address = null, float $lat = 28.3685, float $lng = 77.2950): OpenSeoLocalTracker
    {
        // 3x3 Geo-grid simulation points around business coordinates
        $grid = [];
        $offsets = [-0.015, 0.0, 0.015];
        $totalRank = 0;
        $top3Count = 0;

        $pointIndex = 1;
        foreach ($offsets as $latOff) {
            foreach ($offsets as $lngOff) {
                $pointLat = $lat + $latOff;
                $pointLng = $lng + $lngOff;

                // Center is rank 1, outskirts between 1 and 6
                $dist = abs($latOff) + abs($lngOff);
                $rank = $dist === 0.0 ? 1 : (int)min(12, max(1, round($dist * 180) + (crc32($keyword . $pointIndex) % 3)));

                if ($rank <= 3) $top3Count++;
                $totalRank += $rank;

                $grid[] = [
                    'point' => $pointIndex++,
                    'latitude' => round($pointLat, 6),
                    'longitude' => round($pointLng, 6),
                    'rank' => $rank,
                ];
            }
        }

        $avgRank = round($totalRank / count($grid), 2);
        $top3Pct = round(($top3Count / count($grid)) * 100, 2);

        return OpenSeoLocalTracker::updateOrCreate(
            [
                'project_id' => $project->id,
                'keyword' => $keyword,
            ],
            [
                'business_name' => $businessName,
                'address' => $address ?: 'House No. 1049, Jeevan Nagar, Gounchhi, Faridabad, Haryana 121004',
                'latitude' => $lat,
                'longitude' => $lng,
                'grid_size' => '3x3',
                'average_rank' => $avgRank,
                'top_3_percentage' => $top3Pct,
                'grid_results' => $grid,
                'last_scanned_at' => now(),
            ]
        );
    }
}
