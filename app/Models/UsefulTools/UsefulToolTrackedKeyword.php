<?php

namespace App\Models\UsefulTools;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UsefulToolTrackedKeyword extends Model
{
    use HasFactory;

    protected $table = 'useful_tool_tracked_keywords';

    protected $fillable = [
        'user_id',
        'project_id',
        'domain',
        'keyword',
        'country',
        'location',
        'language',
        'device',
        'search_engine',
        'current_position',
        'previous_position',
        'position_change',
        'best_position',
        'ranking_url',
        'search_volume',
        'keyword_difficulty',
        'cpc',
        'search_intent',
        'last_checked_at',
        'is_active',
    ];

    protected $casts = [
        'current_position' => 'integer',
        'previous_position' => 'integer',
        'position_change' => 'integer',
        'best_position' => 'integer',
        'search_volume' => 'integer',
        'keyword_difficulty' => 'integer',
        'cpc' => 'decimal:2',
        'last_checked_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(UsefulToolProject::class, 'project_id');
    }

    public function rankings(): HasMany
    {
        return $this->hasMany(UsefulToolRanking::class, 'keyword_id')->orderBy('checked_date', 'desc');
    }

    /**
     * Scope for ranking tier filters.
     */
    public function scopePositionTier($query, string $tier)
    {
        return match ($tier) {
            'top3' => $query->whereBetween('current_position', [1, 3]),
            'top10' => $query->whereBetween('current_position', [1, 10]),
            'top20' => $query->whereBetween('current_position', [11, 20]),
            'top50' => $query->whereBetween('current_position', [21, 50]),
            'top100' => $query->whereBetween('current_position', [51, 100]),
            'improved' => $query->where('position_change', '>', 0),
            'declined' => $query->where('position_change', '<', 0),
            'unranked' => $query->whereNull('current_position'),
            default => $query,
        };
    }
}
