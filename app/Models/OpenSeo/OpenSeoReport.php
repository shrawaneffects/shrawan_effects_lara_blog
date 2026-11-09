<?php

namespace App\Models\OpenSeo;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OpenSeoReport extends Model
{
    use HasFactory;

    protected $table = 'openseo_reports';

    protected $fillable = [
        'project_id',
        'user_id',
        'title',
        'report_type',
        'summary_data',
        'file_path',
        'share_token',
        'is_public',
    ];

    protected $casts = [
        'summary_data' => 'array',
        'is_public' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($report) {
            if (empty($report->share_token)) {
                $report->share_token = Str::random(32);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(OpenSeoProject::class, 'project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
