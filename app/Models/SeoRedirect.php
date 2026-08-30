<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoRedirect extends Model
{
    protected $table = 'seo_redirects';

    protected $fillable = [
        'old_url',
        'new_url',
        'status_code',
        'hits',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'hits' => 'integer',
            'last_used_at' => 'datetime',
        ];
    }

    /**
     * Record a hit on this redirect
     */
    public function recordHit(): void
    {
        $this->increment('hits');
        $this->update(['last_used_at' => now()]);
    }
}
