<?php

namespace App\Models\OpenSeo;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpenSeoProject extends Model
{
    use HasFactory;

    protected $table = 'openseo_projects';

    protected $fillable = [
        'user_id',
        'name',
        'domain',
        'target_country',
        'target_language',
        'gsc_property',
        'dataforseo_login',
        'dataforseo_password',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(OpenSeoKeyword::class, 'project_id');
    }

    public function competitors(): HasMany
    {
        return $this->hasMany(OpenSeoCompetitor::class, 'project_id');
    }

    public function backlinks(): HasMany
    {
        return $this->hasMany(OpenSeoBacklink::class, 'project_id');
    }

    public function backlinkSummaries(): HasMany
    {
        return $this->hasMany(OpenSeoBacklinkSummary::class, 'project_id');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(OpenSeoAudit::class, 'project_id');
    }

    public function aiVisibilities(): HasMany
    {
        return $this->hasMany(OpenSeoAiVisibility::class, 'project_id');
    }

    public function localTrackers(): HasMany
    {
        return $this->hasMany(OpenSeoLocalTracker::class, 'project_id');
    }

    public function searchPerformances(): HasMany
    {
        return $this->hasMany(OpenSeoSearchPerformance::class, 'project_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(OpenSeoReport::class, 'project_id');
    }

    /**
     * Get the active project or default fallback.
     */
    public static function getActiveProject(): ?self
    {
        $sessionProjectId = session('openseo_active_project_id');
        if ($sessionProjectId) {
            $project = self::find($sessionProjectId);
            if ($project) {
                return $project;
            }
        }

        // Return default project or the first project
        $default = self::where('is_default', true)->first();
        if ($default) {
            session(['openseo_active_project_id' => $default->id]);
            return $default;
        }

        $first = self::first();
        if ($first) {
            session(['openseo_active_project_id' => $first->id]);
            return $first;
        }

        return null;
    }
}
