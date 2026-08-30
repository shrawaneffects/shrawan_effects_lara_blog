<?php

namespace App\Services\Seo;

use App\Models\Post;
use Illuminate\Support\Carbon;

class SeoFreshnessService
{
    /**
     * Evaluate freshness of a post
     */
    public static function evaluate(Post $post): array
    {
        $publishedAt = $post->published_at ?: $post->created_at;
        $updatedAt = $post->updated_at ?: $publishedAt;
        
        $monthsSincePublished = $publishedAt ? $publishedAt->diffInMonths(now()) : 0;
        $monthsSinceUpdated = $updatedAt ? $updatedAt->diffInMonths(now()) : 0;

        $content = $post->content ?? '';
        $currentYear = (int) now()->format('Y');
        
        // Detect outdated years like 2018, 2019, 2020, 2021, 2022 in text
        $outdatedYears = [];
        for ($y = 2018; $y <= $currentYear - 3; $y++) {
            if (preg_match("/\b{$y}\b/", $content)) {
                $outdatedYears[] = $y;
            }
        }

        $status = 'fresh';
        $label = 'Fresh & Up to date';
        $color = 'success';
        $recommendations = [];

        if ($monthsSinceUpdated >= 24 || count($outdatedYears) >= 2) {
            $status = 'stale';
            $label = 'Stale / Needs Comprehensive Update';
            $color = 'danger';
            $recommendations[] = "Article has not been updated in {$monthsSinceUpdated} months and references older years (" . implode(', ', $outdatedYears) . "). Review statistics, tools, and outdated guidance.";
        } elseif ($monthsSinceUpdated >= 12) {
            $status = 'review_recommended';
            $label = 'Review Recommended';
            $color = 'warning';
            $recommendations[] = "Article was last updated {$monthsSinceUpdated} months ago. Verify links and current relevance.";
        }

        return [
            'status' => $status,
            'label' => $label,
            'color' => $color,
            'months_since_updated' => $monthsSinceUpdated,
            'months_since_published' => $monthsSincePublished,
            'outdated_year_references' => $outdatedYears,
            'recommendations' => $recommendations,
        ];
    }
}
