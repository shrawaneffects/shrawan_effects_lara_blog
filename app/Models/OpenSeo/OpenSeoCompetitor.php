<?php

namespace App\Models\OpenSeo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenSeoCompetitor extends Model
{
    use HasFactory;

    protected $table = 'openseo_competitors';

    protected $fillable = [
        'project_id',
        'competitor_domain',
        'organic_traffic',
        'organic_keywords',
        'common_keywords',
        'keyword_overlap_pct',
        'notes',
    ];

    protected $casts = [
        'organic_traffic' => 'integer',
        'organic_keywords' => 'integer',
        'common_keywords' => 'integer',
        'keyword_overlap_pct' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(OpenSeoProject::class, 'project_id');
    }
}
