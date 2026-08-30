<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostSeoEntity extends Model
{
    public $timestamps = false;
    protected $table = 'post_seo_entities';

    protected $fillable = [
        'post_seo_id',
        'entity',
        'type',
    ];

    public function seo(): BelongsTo
    {
        return $this->belongsTo(PostSeo::class, 'post_seo_id');
    }
}
