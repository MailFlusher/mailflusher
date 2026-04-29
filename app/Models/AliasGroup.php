<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AliasGroup extends Model
{
    use HasFactory;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'description',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'sort_order' => 'integer',
    ];

    /**
     * Allowed color slugs. Keep this aligned with the frontend palette
     * component so we never render an unknown color class.
     */
    public const PALETTE = [
        'indigo',
        'cyan',
        'green',
        'amber',
        'red',
        'purple',
        'pink',
        'grey',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(Alias::class, 'alias_group_id');
    }
}
