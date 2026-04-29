<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AliasLeakEvent extends Model
{
    protected $fillable = [
        'alias_id',
        'sender_domain',
        'detected_at',
        'status',
        'notified_at',
    ];

    protected $casts = [
        'alias_id' => 'string',
        'detected_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    public function alias(): BelongsTo
    {
        return $this->belongsTo(Alias::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function confirm(): void
    {
        $this->update(['status' => 'confirmed']);
    }

    public function dismiss(): void
    {
        $this->update(['status' => 'dismissed']);
    }
}
