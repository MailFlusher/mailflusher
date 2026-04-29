<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoredEmail extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'alias_id',
        'from_preview',
        'subject_preview',
        'size_bytes',
        'encrypted_payload',
        'received_at',
    ];

    protected $casts = [
        'alias_id' => 'string',
        'size_bytes' => 'integer',
        'received_at' => 'datetime',
    ];

    /**
     * The ciphertext should never be included by default — pulled only on the
     * single-email read endpoint.
     */
    protected $hidden = [
        'encrypted_payload',
    ];

    public function alias(): BelongsTo
    {
        return $this->belongsTo(Alias::class);
    }
}
