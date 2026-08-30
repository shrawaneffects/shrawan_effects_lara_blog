<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }

    public function publishedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_tag')->where('status', 'published');
    }
}
