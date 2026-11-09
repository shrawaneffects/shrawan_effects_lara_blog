<?php

namespace App\Models\OpenSeo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpenSeoKeyword extends Model
{
    use HasFactory;

    protected $table = 'openseo_keywords';

    protected $fillable = [
        'project_id',
        'keyword',
        'search_volume',
        'cpc',
        'competition',
        'keyword_difficulty',
        'search_intent',
        'current_rank',
        'previous_rank',
        'best_rank',
        'rank_change',
        'target_url',
        'serp_features',
        'is_tracked',
        'tags',
        'last_checked_at',
    ];

    protected $casts = [
        'search_volume' => 'integer',
        'cpc' => 'decimal:2',
        'competition' => 'decimal:2',
        'keyword_difficulty' => 'integer',
        'current_rank' => 'integer',
        'previous_rank' => 'integer',
        'best_rank' => 'integer',
        'rank_change' => 'integer',
        'is_tracked' => 'boolean',
        'serp_features' => 'array',
        'last_checked_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(OpenSeoProject::class, 'project_id');
    }

    public function rankHistories(): HasMany
    {
        return $this->hasMany(OpenSeoRankHistory::class, 'keyword_id')->orderBy('checked_date', 'asc');
    }

    /**
     * Helper for KD badge color
     */
    public function getKdBadgeColorAttribute(): string
    {
        if ($this->keyword_difficulty <= 30) return 'success';
        if ($this->keyword_difficulty <= 60) return 'warning';
        return 'danger';
    }

    /**
     * Helper for KD difficulty label
     */
    public function getKdLabelAttribute(): string
    {
        if ($this->keyword_difficulty <= 15) return 'Very Easy';
        if ($this->keyword_difficulty <= 30) return 'Easy';
        if ($this->keyword_difficulty <= 50) return 'Possible';
        if ($this->keyword_difficulty <= 70) return 'Hard';
        return 'Very Hard';
    }
}
