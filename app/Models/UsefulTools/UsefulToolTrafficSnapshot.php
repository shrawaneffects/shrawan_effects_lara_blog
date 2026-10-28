<?php

namespace App\Models\UsefulTools;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsefulToolTrafficSnapshot extends Model
{
    use HasFactory;

    protected $table = 'useful_tool_traffic_snapshots';

    protected $fillable = [
        'user_id',
        'domain',
        'data_type',
        'provider',
        'reporting_period',
        'monthly_visits',
        'unique_visitors',
        'organic_traffic',
        'paid_traffic',
        'direct_traffic',
        'referral_traffic',
        'social_traffic',
        'bounce_rate',
        'avg_visit_duration',
        'pages_per_visit',
        'top_countries',
        'top_pages',
        'traffic_growth_pct',
        'snapshot_date',
    ];

    protected $casts = [
        'monthly_visits' => 'integer',
        'unique_visitors' => 'integer',
        'organic_traffic' => 'integer',
        'paid_traffic' => 'integer',
        'direct_traffic' => 'integer',
        'referral_traffic' => 'integer',
        'social_traffic' => 'integer',
        'bounce_rate' => 'decimal:2',
        'avg_visit_duration' => 'integer',
        'pages_per_visit' => 'decimal:2',
        'top_countries' => 'array',
        'top_pages' => 'array',
        'traffic_growth_pct' => 'decimal:2',
        'snapshot_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
