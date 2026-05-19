<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanGrant extends Model
{
    protected $fillable = [
        'user_id',
        'plan',
        'started_at',
        'ends_at',
        'source',
    ];

    protected $casts = [
        'user_id' => 'string',
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
