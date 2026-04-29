<?php

namespace App\Http\Controllers\Api\GhostInbox;

use App\Http\Controllers\Controller;
use App\Models\StoredEmail;
use Illuminate\Http\Request;

class GhostVaultController extends Controller
{
    /**
     * Return the user's vault keys (public key + passphrase-encrypted private key).
     * The browser unlocks the private key locally with the user's passphrase.
     */
    public function show()
    {
        $user = user();

        if (! $user->canUseGhostInbox()) {
            return response()->json(['error' => 'Ghost Inbox is a Pro feature.'], 403);
        }

        return response()->json([
            'has_vault' => $user->hasGhostVault(),
            'vault_public_key' => $user->vault_public_key,
            'vault_encrypted_private_key' => $user->vault_encrypted_private_key,
            'vault_created_at' => $user->vault_created_at,
            'ghost_lock_minutes' => $user->ghost_lock_minutes ?? 15,
            'ghost_preview_mode' => $user->ghost_preview_mode ?? 'preview_10',
        ]);
    }

    /**
     * Setup or rotate the vault. Replaces any existing vault.
     * WARNING to caller: rotating the vault renders any previously-stored
     * ciphertext unreadable, so we delete it as part of the operation.
     */
    public function store(Request $request)
    {
        $user = user();

        if (! $user->canUseGhostInbox()) {
            return response()->json(['error' => 'Ghost Inbox is a Pro feature.'], 403);
        }

        $request->validate([
            'vault_public_key' => ['required', 'string', 'min:100', 'max:20000'],
            'vault_encrypted_private_key' => ['required', 'string', 'min:100', 'max:40000'],
        ]);

        // If a vault already exists, rotating it makes old ciphertext unreadable.
        // Purge stored emails first so users don't see permanently-unreadable entries.
        if ($user->hasGhostVault()) {
            StoredEmail::whereIn('alias_id', $user->aliases()->pluck('id'))->delete();
        }

        $user->update([
            'vault_public_key' => $request->input('vault_public_key'),
            'vault_encrypted_private_key' => $request->input('vault_encrypted_private_key'),
            'vault_created_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'vault_created_at' => $user->fresh()->vault_created_at,
        ]);
    }

    /**
     * Update user preferences (lock minutes, preview mode).
     */
    public function updateSettings(Request $request)
    {
        $user = user();

        $request->validate([
            'ghost_lock_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'ghost_preview_mode' => ['nullable', 'in:preview_10,encrypted'],
        ]);

        $user->update($request->only(['ghost_lock_minutes', 'ghost_preview_mode']));

        return response()->json(['ok' => true]);
    }

    /**
     * Tear down the vault. Deletes all stored emails (they'd be unreadable anyway).
     */
    public function destroy()
    {
        $user = user();

        StoredEmail::whereIn('alias_id', $user->aliases()->pluck('id'))->delete();

        $user->update([
            'vault_public_key' => null,
            'vault_encrypted_private_key' => null,
            'vault_created_at' => null,
        ]);

        return response()->noContent();
    }
}
