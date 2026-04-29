<?php

namespace App\Services;

use App\Models\Alias;
use App\Models\StoredEmail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Stores a received email for a ghost-mode alias as an OpenPGP-encrypted blob
 * that only the user's private key can decrypt.
 *
 * The server never persists plaintext. The user's private key is held only in
 * their browser, passphrase-protected on our disk. DB compromise yields only
 * the ciphertext blob + optional from/subject previews.
 */
class GhostInbox
{
    /**
     * Encrypt and persist a raw email for this alias. Returns the StoredEmail
     * row on success, or null if the alias/owner isn't eligible.
     *
     * @param  string  $rawEmail  Full MIME bytes (headers + body) as received.
     * @param  string|null  $from  Best-effort plaintext From header for preview.
     * @param  string|null  $subject  Best-effort plaintext Subject header for preview.
     */
    public function store(Alias $alias, string $rawEmail, ?string $from, ?string $subject): ?StoredEmail
    {
        $user = $alias->user;

        if (! $user || ! $user->canUseGhostInbox()) {
            return null;
        }

        if (! $user->hasGhostVault()) {
            Log::warning('Ghost-mode alias received mail but user has no vault; dropping', [
                'alias_id' => $alias->id,
                'user_id' => $user->id,
            ]);

            return null;
        }

        try {
            $ciphertext = $this->encrypt($rawEmail, $user->vault_public_key);
        } catch (\Throwable $e) {
            Log::error('Ghost inbox encryption failed', [
                'alias_id' => $alias->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        [$fromPreview, $subjectPreview] = $this->previews($user->ghost_preview_mode ?? 'preview_10', $from, $subject);

        $stored = StoredEmail::create([
            'alias_id' => $alias->id,
            'from_preview' => $fromPreview,
            'subject_preview' => $subjectPreview,
            'size_bytes' => strlen($rawEmail),
            'encrypted_payload' => $ciphertext,
            'received_at' => now(),
        ]);

        // Best-effort: zero-out the plaintext variable before returning.
        // PHP doesn't give us real memory guarantees here, but we do what we can.
        $rawEmail = str_repeat("\0", strlen($rawEmail));
        unset($rawEmail);

        return $stored;
    }

    /**
     * Encrypt plaintext with the recipient's armored public OpenPGP key.
     * Uses a sandboxed GPG home directory so keys don't persist across calls.
     */
    public function encrypt(string $plaintext, string $publicKeyArmored): string
    {
        if (! extension_loaded('gnupg')) {
            throw new \RuntimeException('gnupg PHP extension is not installed on this server.');
        }

        $home = sys_get_temp_dir().'/mf-ghost-'.Str::random(12);
        File::ensureDirectoryExists($home, 0700);

        try {
            putenv('GNUPGHOME='.$home);
            $gpg = new \gnupg();
            $gpg->setarmor(1);

            $import = $gpg->import($publicKeyArmored);
            if (! $import || empty($import['fingerprint'])) {
                throw new \RuntimeException('Could not import public key.');
            }

            $gpg->addencryptkey($import['fingerprint']);
            $ciphertext = $gpg->encrypt($plaintext);

            if ($ciphertext === false) {
                throw new \RuntimeException('GPG encryption returned false: '.$gpg->geterror());
            }

            return $ciphertext;
        } finally {
            // Clean up the sandboxed keyring
            try {
                File::deleteDirectory($home);
            } catch (\Throwable $e) {
                Log::warning('Could not clean ghost encryption home', ['home' => $home]);
            }
        }
    }

    /**
     * Produce from/subject previews based on the user's ghost_preview_mode.
     */
    private function previews(string $mode, ?string $from, ?string $subject): array
    {
        if ($mode === 'encrypted') {
            return [null, null];
        }

        // preview_10 — first 10 printable chars, stripped of control chars
        $clean = fn ($v) => $v === null ? null : mb_substr(preg_replace('/[\x00-\x1F\x7F]/', '', $v), 0, 10);

        return [$clean($from), $clean($subject)];
    }
}
