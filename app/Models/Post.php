<?php

namespace App\Models;

use App\Packages\DynamicSeeder\Traits\HasDynamicSeeder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasDynamicSeeder;

    protected $fillable = [
        'user_id',
        'title',
        'text',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the comments for the blog post.
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }
}