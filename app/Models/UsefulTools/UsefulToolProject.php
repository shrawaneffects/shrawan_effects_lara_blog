<?php

namespace App\Models\UsefulTools;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UsefulToolProject extends Model
{
    use HasFactory;

    protected $table = 'useful_tool_projects';

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'target_domain',
        'target_country',
        'target_location',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scrapingJobs(): HasMany
    {
        return $this->hasMany(UsefulToolScrapingJob::class, 'project_id');
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(UsefulToolBusiness::class, 'project_id');
    }

    public function trackedKeywords(): HasMany
    {
        return $this->hasMany(UsefulToolTrackedKeyword::class, 'project_id');
    }

    public function competitors(): HasMany
    {
        return $this->hasMany(UsefulToolCompetitor::class, 'project_id');
    }
}
