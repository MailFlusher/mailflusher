<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoRedemption extends Model
{
    protected $fillable = [
        'user_id',
        'promo_code_id',
        'plan',
        'duration_days',
        'redeemed_at',
    ];

    protected $casts = [
        'user_id' => 'string',
        'promo_code_id' => 'integer',
        'duration_days' => 'integer',
        'redeemed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }
}
