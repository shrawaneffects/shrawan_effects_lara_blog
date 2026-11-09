<?php

namespace App\Services\OpenSeo;

use App\Models\OpenSeo\OpenSeoBacklink;
use App\Models\OpenSeo\OpenSeoBacklinkSummary;
use App\Models\OpenSeo\OpenSeoProject;

class BacklinkService
{
    public function getBacklinkProfile(OpenSeoProject $project): array
    {
        $summary = $project->backlinkSummaries()->latest('snapshot_date')->first();
        $backlinks = $project->backlinks()->latest('first_seen')->paginate(20);

        // Calculate anchor text distribution
        $anchors = $project->backlinks()
            ->selectRaw('anchor_text, count(*) as count, max(domain_rating) as max_dr')
            ->groupBy('anchor_text')
            ->orderByDesc('count')
            ->take(8)
            ->get();

        return [
            'summary' => $summary ?: [
                'total_backlinks' => 2450,
                'referring_domains' => 184,
                'referring_ips' => 142,
                'domain_trust_score' => 64,
                'dofollow_count' => 1980,
                'nofollow_count' => 470,
            ],
            'backlinks' => $backlinks,
            'anchors' => $anchors,
        ];
    }
}
