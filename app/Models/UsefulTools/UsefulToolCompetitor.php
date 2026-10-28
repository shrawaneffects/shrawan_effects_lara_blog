<?php

namespace App\Models\UsefulTools;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsefulToolCompetitor extends Model
{
    use HasFactory;

    protected $table = 'useful_tool_competitors';

    protected $fillable = [
        'user_id',
        'project_id',
        'main_domain',
        'competitor_domain',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(UsefulToolProject::class, 'project_id');
    }
}
