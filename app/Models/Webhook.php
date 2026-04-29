<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Webhook extends Model
{
    use HasFactory;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'url',
        'events',
        'secret',
        'description',
        'active',
        'last_delivered_at',
        'last_response_code',
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'events' => 'array',
        'active' => 'boolean',
        'last_delivered_at' => 'datetime',
        'last_response_code' => 'integer',
    ];

    protected $hidden = [
        'secret',
    ];

    public static function generateSecret(): string
    {
        return Str::random(48);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }

    public function subscribesTo(string $event): bool
    {
        return in_array($event, $this->events ?? [], true);
    }
}
