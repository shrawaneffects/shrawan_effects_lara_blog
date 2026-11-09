<?php

namespace App\Models\OpenSeo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpenSeoAudit extends Model
{
    use HasFactory;

    protected $table = 'openseo_audits';

    protected $fillable = [
        'project_id',
        'health_score',
        'total_pages_crawled',
        'total_errors',
        'total_warnings',
        'total_notices',
        'crawl_status',
        'completed_at',
    ];

    protected $casts = [
        'health_score' => 'integer',
        'total_pages_crawled' => 'integer',
        'total_errors' => 'integer',
        'total_warnings' => 'integer',
        'total_notices' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(OpenSeoProject::class, 'project_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(OpenSeoAuditIssue::class, 'audit_id');
    }
}
