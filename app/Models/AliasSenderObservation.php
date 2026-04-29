<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AliasSenderObservation extends Model
{
    protected $fillable = [
        'alias_id',
        'sender_domain',
        'email_count',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'alias_id' => 'string',
        'email_count' => 'integer',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function alias(): BelongsTo
    {
        return $this->belongsTo(Alias::class);
    }
}
