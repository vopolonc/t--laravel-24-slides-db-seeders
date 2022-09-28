<?php

namespace App\Models;

use App\Packages\DynamicSeeder\Traits\HasDynamicSeeder;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    use HasDynamicSeeder;

    protected $fillable = [
        'post_id',
        'user_id',
        'text',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the post that owns the comment.
     */
    public function post(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get likes of this comment.
     */
    public function likes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Like::class);
    }
}