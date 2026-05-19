<?php

namespace App\Models;

use App\Traits\HasEncryptedAttributes;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Alias extends Model
{
    use HasEncryptedAttributes;
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $encrypted = [
        'description',
        'from_name',
    ];

    protected $fillable = [
        'id',
        'user_id',
        'active',
        'pinned',
        'description',
        'from_name',
        'attached_recipients_only',
        'email',
        'local_part',
        'extension',
        'domain',
        'aliasable_id',
        'aliasable_type',
        'alias_group_id',
        'emails_forwarded',
        'emails_blocked',
        'emails_replied',
        'emails_sent',
        'last_forwarded',
        'last_blocked',
        'last_replied',
        'last_sent',
        'expires_at',
        'max_emails',
        'on_expiry',
        'expired_at',
        'baseline_sender_domain',
        'baseline_locked_at',
        'ghost_mode',
        'deleted_at',
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'aliasable_id' => 'string',
        'aliasable_type' => 'string',
        'alias_group_id' => 'string',
        'active' => 'boolean',
        'pinned' => 'boolean',
        'attached_recipients_only' => 'boolean',
        'ghost_mode' => 'boolean',
        'max_emails' => 'integer',
        'last_forwarded' => 'datetime',
        'last_blocked' => 'datetime',
        'last_replied' => 'datetime',
        'last_sent' => 'datetime',
        'expires_at' => 'datetime',
        'expired_at' => 'datetime',
        'baseline_locked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        // Deactivate the alias when it is deleted
        Alias::deleting(function ($alias) {
            if ($alias->active) {
                $alias->deactivate();
            }
        });

        // Activate the alias when it is restored
        Alias::restoring(function ($alias) {
            $alias->activate();
        });
    }

    protected function localPart(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtolower($value),
        );
    }

    protected function domain(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtolower($value),
        );
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtolower($value),
        );
    }

    /**
     * Get the user for the email alias.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the owning aliasable model.
     */
    public function aliasable()
    {
        return $this->morphTo();
    }

    /**
     * Get the recipients for the email alias.
     */
    public function recipients()
    {
        return $this->belongsToMany(Recipient::class, 'alias_recipients')->withPivot('id')->using(AliasRecipient::class);
    }

    public function senderObservations()
    {
        return $this->hasMany(AliasSenderObservation::class);
    }

    public function leakEvents()
    {
        return $this->hasMany(AliasLeakEvent::class);
    }

    public function storedEmails()
    {
        return $this->hasMany(StoredEmail::class);
    }

    public function group()
    {
        return $this->belongsTo(AliasGroup::class, 'alias_group_id');
    }

    /**
     * Detach all recipients from this alias.
     *
     * @return int
     */
    public function detachAllRecipients()
    {
        return DB::table('alias_recipients')->where('alias_id', $this->id)->delete();
    }

    /**
     * Detach specific recipients from this alias.
     *
     * @param  array|string  $recipientIds
     * @return int
     */
    public function detachRecipients($recipientIds)
    {
        $recipientIds = is_array($recipientIds) ? $recipientIds : [$recipientIds];

        return DB::table('alias_recipients')
            ->where('alias_id', $this->id)
            ->whereIn('recipient_id', $recipientIds)
            ->delete();
    }

    /**
     * Get all of the aliases' failed deliveries.
     */
    public function failedDeliveries()
    {
        return $this->hasMany(FailedDelivery::class);
    }

    /**
     * Get all of the aliases' outbound messages.
     */
    public function outboundMessages()
    {
        return $this->hasMany(OutboundMessage::class);
    }

    /**
     * Get all of the verified recipients for the email alias.
     */
    public function verifiedRecipients()
    {
        // Inactive recipients (e.g. deactivated by a plan downgrade) are excluded
        // so the forwarding pipeline falls back to the default recipient instead
        // of trying to deliver to a recipient the user no longer has plan-room for.
        return $this->recipients()
            ->whereNotNull('email_verified_at')
            ->where('recipients.active', true);
    }

    /**
     * Get the verified recipients for the email alias or the default recipient if none are set.
     */
    public function verifiedRecipientsOrDefault()
    {
        $verifiedRecipients = $this
            ->verifiedRecipients()
            ->get();

        if ($verifiedRecipients->count() === 0) {
            // If the alias is for a custom domain or username that has a default recipient set.
            if ($this->aliasable_id) {
                if (isset($this->aliasable->defaultRecipient)) {
                    return $this->aliasable->defaultRecipient();
                }
            }

            return $this->user->hasVerifiedDefaultRecipient() ? $this->user->defaultRecipient() : collect();
        }

        return $verifiedRecipients;
    }

    public function scopeUsesRecipientWithId($query, $id, $isDefault = false)
    {
        return $query->where(function (Builder $q) use ($id) {
            return $q->whereHas('recipients', function (Builder $query) use ($id) {
                $query->where('recipients.id', $id);
            })->orWhere(function (Builder $q) use ($id) {
                return $q->whereHasMorph('aliasable', ['App\Models\Domain', 'App\Models\Username'], function (Builder $query) use ($id) {
                    $query->where('default_recipient_id', $id);
                })->doesntHave('recipients');
            });
        });
    }

    public function scopeBelongsToAliasable($query, $type, $id)
    {
        return $query->where('aliasable_type', $type)->where('aliasable_id', $id);
    }

    /**
     * Is this alias configured as a burner (time-limited or email-count-limited)?
     */
    public function isBurner(): bool
    {
        return ! is_null($this->expires_at) || ! is_null($this->max_emails);
    }

    /**
     * Has this burner passed its expiry time?
     */
    public function isTimeExpired(): bool
    {
        return ! is_null($this->expires_at) && $this->expires_at->isPast();
    }

    /**
     * Has this burner reached its email-count limit?
     */
    public function hasHitEmailLimit(): bool
    {
        return ! is_null($this->max_emails) && $this->emails_forwarded >= $this->max_emails;
    }

    /**
     * Should this alias no longer forward, based on burner limits?
     */
    public function isExpired(): bool
    {
        return $this->isTimeExpired() || $this->hasHitEmailLimit();
    }

    /**
     * Aliases whose expires_at has passed and are still active.
     */
    public function scopeDueForTimeExpiry($query)
    {
        return $query->where('active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    /**
     * Mark as expired (deactivate + stamp expired_at).
     */
    public function markExpired(): void
    {
        $this->update([
            'active' => false,
            'expired_at' => now(),
        ]);
    }

    /**
     * Deactivate the alias.
     */
    public function deactivate()
    {
        $this->update(['active' => false]);
    }

    /**
     * Activate the alias.
     */
    public function activate()
    {
        $this->update(['active' => true]);
    }

    public function isUuid()
    {
        return $this->id === $this->local_part;
    }

    public function hasSharedDomain()
    {
        return in_array($this->domain, config('mailflusher.all_domains'));
    }

    public function getFromName()
    {
        // Check alias from name
        if ($aliasFromName = $this->from_name) {
            return $aliasFromName;
        }

        // Check username / custom domain from name
        if ($aliasableFromName = $this->aliasable?->from_name) {
            return $aliasableFromName;
        }

        // Check user settings global from name
        return $this->user->from_name ?? null;
    }

    public function isCustomDomain()
    {
        return $this->aliasable_type === 'App\Models\Domain';
    }

    public function parentDomain()
    {
        return collect(config('mailflusher.all_domains'))
            ->filter(function ($name) {
                return Str::endsWith($this->domain, $name);
            })
            ->first();
    }
}
