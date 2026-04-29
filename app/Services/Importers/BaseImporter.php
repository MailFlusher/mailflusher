<?php

namespace App\Services\Importers;

use App\Models\Alias;
use App\Models\User;
use App\Models\Username;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * Shared creation helpers for concrete importers. Each importer fetches its
 * own source list and calls the helpers here to turn it into a MailFlusher
 * alias on the user's default username subdomain.
 */
abstract class BaseImporter implements AliasImporter
{
    /**
     * Create a MailFlusher alias under the user's default username subdomain.
     *
     * @return array{created:bool, reason:?string}
     */
    protected function createAlias(User $user, string $sourceEmail, ?string $description, bool $active): array
    {
        if ($user->hasReachedAliasLimit()) {
            return ['created' => false, 'reason' => 'limit_reached'];
        }

        $domain = $user->default_alias_domain ?: ($user->username.'.'.config('mailflusher.domain'));

        $localPart = $this->deriveLocalPart($sourceEmail);
        $email = $localPart.'@'.$domain;

        // Skip if we already have this alias
        if ($user->aliases()->where('email', $email)->exists()) {
            return ['created' => false, 'reason' => 'already_exists'];
        }

        $aliasable = Username::where('username', $user->username)->first();

        $alias = $user->aliases()->create([
            'id' => (string) Uuid::uuid4(),
            'email' => $email,
            'local_part' => $localPart,
            'domain' => $domain,
            'aliasable_id' => $aliasable?->id,
            'aliasable_type' => $aliasable ? Username::class : null,
            'description' => $description,
            'active' => $active,
        ]);

        return ['created' => true, 'reason' => null];
    }

    /**
     * Pull a safe local part out of the source alias email. Falls back to a
     * random string if the source local part is unusable.
     */
    protected function deriveLocalPart(string $sourceEmail): string
    {
        $at = strrpos($sourceEmail, '@');
        $base = $at !== false ? substr($sourceEmail, 0, $at) : $sourceEmail;
        $base = Str::lower($base);
        $base = preg_replace('/[^a-z0-9._-]/', '', $base) ?: '';

        if ($base === '' || strlen($base) > 50) {
            return Str::lower(Str::random(10));
        }

        // Ensure uniqueness by adding a short random suffix if needed
        return $base.'.'.Str::lower(Str::random(4));
    }
}
