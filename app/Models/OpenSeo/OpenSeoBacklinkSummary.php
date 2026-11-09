<?php

namespace App\Models\OpenSeo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenSeoBacklinkSummary extends Model
{
    use HasFactory;

    protected $table = 'openseo_backlink_summaries';

    protected $fillable = [
        'project_id',
        'total_backlinks',
        'referring_domains',
        'referring_ips',
        'domain_trust_score',
        'dofollow_count',
        'nofollow_count',
        'snapshot_date',
    ];

    protected $casts = [
        'total_backlinks' => 'integer',
        'referring_domains' => 'integer',
        'referring_ips' => 'integer',
        'domain_trust_score' => 'integer',
        'dofollow_count' => 'integer',
        'nofollow_count' => 'integer',
        'snapshot_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(OpenSeoProject::class, 'project_id');
    }
}
