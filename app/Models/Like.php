<?php

namespace App\Models;

use App\Packages\DynamicSeeder\Traits\HasDynamicSeeder;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    const UPDATED_AT = null;

    use HasDynamicSeeder;

    protected $fillable = [
        'comment_id',
        'user_id',
        'created_at',
    ];

    /**
     * Get the post that owns the like.
     */
    public function comment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Get the user that owns the like.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}