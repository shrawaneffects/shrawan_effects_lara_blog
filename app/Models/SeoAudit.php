<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoAudit extends Model
{
    public $timestamps = false;
    protected $table = 'seo_audits';

    protected $fillable = [
        'post_id',
        'score',
        'status',
        'results_json',
        'audited_by',
        'audited_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'results_json' => 'array',
            'audited_at' => 'datetime',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'audited_by');
    }
}
