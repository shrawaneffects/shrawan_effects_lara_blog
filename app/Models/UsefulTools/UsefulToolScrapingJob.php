<?php

namespace App\Models\UsefulTools;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UsefulToolScrapingJob extends Model
{
    use HasFactory;

    protected $table = 'useful_tool_scraping_jobs';

    protected $fillable = [
        'user_id',
        'project_id',
        'provider',
        'query_keyword',
        'category',
        'city',
        'state',
        'country',
        'max_results',
        'status',
        'total_found',
        'total_collected',
        'progress_percent',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'max_results' => 'integer',
        'total_found' => 'integer',
        'total_collected' => 'integer',
        'progress_percent' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(UsefulToolProject::class, 'project_id');
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(UsefulToolBusiness::class, 'job_id');
    }

    public function isRunning(): bool
    {
        return in_array($this->status, ['pending', 'running']);
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, ['completed', 'partially_completed']);
    }
}
