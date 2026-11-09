<?php

namespace App\Models\OpenSeo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenSeoAiVisibility extends Model
{
    use HasFactory;

    protected $table = 'openseo_ai_visibilities';

    protected $fillable = [
        'project_id',
        'brand_name',
        'prompt_query',
        'ai_model',
        'is_mentioned',
        'mention_rank',
        'sentiment',
        'citation_url',
        'response_snippet',
        'last_checked_at',
    ];

    protected $casts = [
        'is_mentioned' => 'boolean',
        'mention_rank' => 'integer',
        'last_checked_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(OpenSeoProject::class, 'project_id');
    }
}
