<?php

namespace App\Models\OpenSeo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenSeoAuditIssue extends Model
{
    use HasFactory;

    protected $table = 'openseo_audit_issues';

    protected $fillable = [
        'audit_id',
        'page_url',
        'issue_type',
        'severity',
        'description',
        'recommendation',
        'http_status',
    ];

    protected $casts = [
        'http_status' => 'integer',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(OpenSeoAudit::class, 'audit_id');
    }
}
