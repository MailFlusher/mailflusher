<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookDelivery extends Model
{
    protected $fillable = [
        'webhook_id',
        'event',
        'payload',
        'status',
        'response_code',
        'response_body',
        'attempts',
        'next_retry_at',
        'delivered_at',
    ];

    protected $casts = [
        'webhook_id' => 'string',
        'payload' => 'array',
        'attempts' => 'integer',
        'response_code' => 'integer',
        'next_retry_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }

    public function markSuccess(int $responseCode, ?string $responseBody = null): void
    {
        $this->update([
            'status' => 'success',
            'response_code' => $responseCode,
            'response_body' => $responseBody ? mb_substr($responseBody, 0, 500) : null,
            'delivered_at' => now(),
            'next_retry_at' => null,
        ]);
    }

    public function markFailed(?int $responseCode, ?string $responseBody, bool $givingUp = false): void
    {
        $this->update([
            'status' => $givingUp ? 'giving_up' : 'failed',
            'response_code' => $responseCode,
            'response_body' => $responseBody ? mb_substr($responseBody, 0, 500) : null,
            'next_retry_at' => $givingUp ? null : $this->nextRetryAt(),
        ]);
    }

    public function nextRetryAt(): \Illuminate\Support\Carbon
    {
        // Exponential backoff: 1min, 5min, 30min, 2h, 12h (attempts 1..5)
        $delayMinutes = match ((int) $this->attempts) {
            0, 1 => 1,
            2 => 5,
            3 => 30,
            4 => 120,
            default => 720,
        };

        return now()->addMinutes($delayMinutes);
    }
}
