<?php

namespace App\Models\UsefulTools;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsefulToolRanking extends Model
{
    use HasFactory;

    protected $table = 'useful_tool_rankings';

    protected $fillable = [
        'keyword_id',
        'request_uid',
        'position',
        'is_found',
        'ranking_url',
        'previous_position',
        'position_change',
        'checked_date',
        'provider',
        'provider_metadata',
    ];

    protected $casts = [
        'position' => 'integer',
        'is_found' => 'boolean',
        'previous_position' => 'integer',
        'position_change' => 'integer',
        'checked_date' => 'date',
        'provider_metadata' => 'array',
    ];

    public function trackedKeyword(): BelongsTo
    {
        return $this->belongsTo(UsefulToolTrackedKeyword::class, 'keyword_id');
    }
}
