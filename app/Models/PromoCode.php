<?php

namespace App\Models;

use Database\Factories\PromoCodeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'plan',
        'duration_days',
        'max_redemptions',
        'redemption_count',
        'expires_at',
        'active',
        'notes',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'max_redemptions' => 'integer',
        'redemption_count' => 'integer',
        'expires_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function redemptions(): HasMany
    {
        return $this->hasMany(PromoRedemption::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isExhausted(): bool
    {
        return $this->max_redemptions !== null && $this->redemption_count >= $this->max_redemptions;
    }

    public function isRedeemable(): bool
    {
        return $this->active && ! $this->isExpired() && ! $this->isExhausted();
    }

    protected static function newFactory(): PromoCodeFactory
    {
        return PromoCodeFactory::new();
    }
}
