<?php

namespace App\Models\OpenSeo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenSeoRankHistory extends Model
{
    use HasFactory;

    protected $table = 'openseo_rank_histories';

    protected $fillable = [
        'keyword_id',
        'rank',
        'target_url',
        'serp_features',
        'checked_date',
    ];

    protected $casts = [
        'rank' => 'integer',
        'serp_features' => 'array',
        'checked_date' => 'date',
    ];

    public function keyword(): BelongsTo
    {
        return $this->belongsTo(OpenSeoKeyword::class, 'keyword_id');
    }
}
