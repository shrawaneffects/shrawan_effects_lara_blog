<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostSeoKeyword extends Model
{
    public $timestamps = false;
    protected $table = 'post_seo_keywords';

    protected $fillable = [
        'post_seo_id',
        'keyword',
        'type',
    ];

    public function seo(): BelongsTo
    {
        return $this->belongsTo(PostSeo::class, 'post_seo_id');
    }
}
