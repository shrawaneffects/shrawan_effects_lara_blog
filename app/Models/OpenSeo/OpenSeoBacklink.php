<?php

namespace App\Models\OpenSeo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenSeoBacklink extends Model
{
    use HasFactory;

    protected $table = 'openseo_backlinks';

    protected $fillable = [
        'project_id',
        'source_url',
        'source_title',
        'target_url',
        'anchor_text',
        'domain_rating',
        'is_dofollow',
        'is_lost',
        'first_seen',
        'last_seen',
    ];

    protected $casts = [
        'domain_rating' => 'integer',
        'is_dofollow' => 'boolean',
        'is_lost' => 'boolean',
        'first_seen' => 'date',
        'last_seen' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(OpenSeoProject::class, 'project_id');
    }
}
