<?php

namespace App\Models\OpenSeo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenSeoLocalTracker extends Model
{
    use HasFactory;

    protected $table = 'openseo_local_trackers';

    protected $fillable = [
        'project_id',
        'business_name',
        'keyword',
        'address',
        'latitude',
        'longitude',
        'grid_size',
        'average_rank',
        'top_3_percentage',
        'grid_results',
        'last_scanned_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'average_rank' => 'decimal:2',
        'top_3_percentage' => 'decimal:2',
        'grid_results' => 'array',
        'last_scanned_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(OpenSeoProject::class, 'project_id');
    }
}
