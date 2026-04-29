<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RedirectToken extends Model
{
    protected $primaryKey = 'token';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'token',
        'alias_id',
        'target_url',
        'clicks',
        'expires_at',
    ];

    protected $casts = [
        'alias_id' => 'string',
        'clicks' => 'integer',
        'expires_at' => 'datetime',
    ];

    /**
     * Create a new redirect token for a target URL.
     */
    public static function mint(?string $aliasId, string $targetUrl): self
    {
        $ttl = (int) config('trackers.redirect_token_ttl_days', 90);

        do {
            $token = Str::random(12);
        } while (self::where('token', $token)->exists());

        return self::create([
            'token' => $token,
            'alias_id' => $aliasId,
            'target_url' => $targetUrl,
            'expires_at' => now()->addDays($ttl),
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function alias(): BelongsTo
    {
        return $this->belongsTo(Alias::class);
    }
}
