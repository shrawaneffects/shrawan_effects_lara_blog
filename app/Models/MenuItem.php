<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MenuItem extends Model
{
    protected $fillable = [
        'location',
        'parent_id',
        'title',
        'url',
        'icon',
        'target',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function activeChildren()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    public function allActiveChildren()
    {
        return $this->activeChildren()->with('allActiveChildren');
    }

    public function scopeLocation(Builder $query, string $location): Builder
    {
        return $query->where('location', $location);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    /**
     * Get nested active menu tree for frontend layout (supports multi-level dropdowns)
     */
    public static function getMenuTree(string $location = 'header')
    {
        return static::location($location)
            ->active()
            ->root()
            ->with(['children' => function ($q) {
                $q->active()->orderBy('order')->with(['children' => function ($q2) {
                    $q2->active()->orderBy('order')->with(['children' => function ($q3) {
                        $q3->active()->orderBy('order');
                    }]);
                }]);
            }])
            ->ordered()
            ->get();
    }
}
