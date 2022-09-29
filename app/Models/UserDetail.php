<?php

namespace App\Models;

use App\Packages\DynamicSeeder\Traits\HasDynamicSeeder;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasDynamicSeeder;

    protected $table = 'user_details';

    protected $fillable = [
        'user_id',
        'phone_number',
        'address',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the post that owns the comment.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
