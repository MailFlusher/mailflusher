<?php

namespace Tests\Feature;

use App\Models\Alias;
use App\Models\StoredEmail;
use App\Services\GhostInbox;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GhostInboxTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        parent::setUpSanctum();

        $this->user->update(['plan' => 'pro']);
    }

    #[Test]
    public function vault_setup_requires_pro_plan()
    {
        $this->user->update(['plan' => 'free']);

        $response = $this->postJson('/api/v1/ghost-vault', [
            'vault_public_key' => str_repeat('a', 200),
            'vault_encrypted_private_key' => str_repeat('b', 200),
        ]);

        $response->assertForbidden();
        $this->assertNull($this->user->fresh()->vault_public_key);
    }

    #[Test]
    public function pro_user_can_create_vault()
    {
        $response = $this->postJson('/api/v1/ghost-vault', [
            'vault_public_key' => str_repeat('a', 200),
            'vault_encrypted_private_key' => str_repeat('b', 200),
        ]);

        $response->assertSuccessful();
        $user = $this->user->fresh();
        $this->assertSame(str_repeat('a', 200), $user->vault_public_key);
        $this->assertSame(str_repeat('b', 200), $user->vault_encrypted_private_key);
        $this->assertNotNull($user->vault_created_at);
        $this->assertTrue($user->hasGhostVault());
    }

    #[Test]
    public function rotating_vault_clears_stored_emails()
    {
        // Seed a vault and an alias with a stored email
        $this->user->update([
            'vault_public_key' => str_repeat('a', 200),
            'vault_encrypted_private_key' => str_repeat('b', 200),
            'vault_created_at' => now(),
        ]);
        $alias = Alias::factory()->create(['user_id' => $this->user->id, 'ghost_mode' => true]);
        StoredEmail::create([
            'alias_id' => $alias->id,
            'from_preview' => 'Brand',
            'subject_preview' => 'Hello',
            'size_bytes' => 100,
            'encrypted_payload' => 'OLD_CIPHERTEXT',
            'received_at' => now(),
        ]);
        $this->assertEquals(1, StoredEmail::count());

        // Rotate by uploading a new vault
        $this->postJson('/api/v1/ghost-vault', [
            'vault_public_key' => str_repeat('c', 200),
            'vault_encrypted_private_key' => str_repeat('d', 200),
        ])->assertSuccessful();

        $this->assertEquals(0, StoredEmail::count());
    }

    #[Test]
    public function destroy_vault_deletes_stored_emails_and_keys()
    {
        $this->user->update([
            'vault_public_key' => 'pk',
            'vault_encrypted_private_key' => 'ep',
            'vault_created_at' => now(),
        ]);
        $alias = Alias::factory()->create(['user_id' => $this->user->id, 'ghost_mode' => true]);
        StoredEmail::create([
            'alias_id' => $alias->id,
            'encrypted_payload' => 'x',
            'size_bytes' => 1,
            'received_at' => now(),
        ]);

        $this->deleteJson('/api/v1/ghost-vault')->assertNoContent();

        $user = $this->user->fresh();
        $this->assertNull($user->vault_public_key);
        $this->assertNull($user->vault_encrypted_private_key);
        $this->assertEquals(0, StoredEmail::count());
    }

    #[Test]
    public function show_returns_vault_fields_to_user()
    {
        $this->user->update([
            'vault_public_key' => 'publickey',
            'vault_encrypted_private_key' => 'encprivkey',
            'vault_created_at' => now(),
            'ghost_lock_minutes' => 15,
            'ghost_preview_mode' => 'preview_10',
        ]);

        $response = $this->getJson('/api/v1/ghost-vault');

        $response->assertSuccessful()
            ->assertJsonFragment([
                'has_vault' => true,
                'vault_public_key' => 'publickey',
                'vault_encrypted_private_key' => 'encprivkey',
                'ghost_lock_minutes' => 15,
                'ghost_preview_mode' => 'preview_10',
            ]);
    }

    #[Test]
    public function settings_update_validates_lock_minutes()
    {
        $response = $this->patchJson('/api/v1/ghost-vault/settings', [
            'ghost_lock_minutes' => 99999,
        ]);
        $response->assertStatus(422);

        $response = $this->patchJson('/api/v1/ghost-vault/settings', [
            'ghost_lock_minutes' => 5,
            'ghost_preview_mode' => 'encrypted',
        ]);
        $response->assertSuccessful();
        $this->assertSame(5, $this->user->fresh()->ghost_lock_minutes);
        $this->assertSame('encrypted', $this->user->fresh()->ghost_preview_mode);
    }

    #[Test]
    public function cannot_enable_ghost_mode_on_alias_without_vault()
    {
        config(['mailflusher.all_domains' => ['example.com']]);
        $this->user->defaultUsername->update(['username' => 'tester']);

        // No vault set
        $response = $this->postJson('/api/v1/aliases', [
            'domain' => 'tester.example.com',
            'description' => 'test',
            'ghost_mode' => true,
        ]);

        // Without a vault set we get a 422 with error message
        $response->assertStatus(422);
    }

    #[Test]
    public function stored_email_index_lists_previews_only()
    {
        $alias = Alias::factory()->create(['user_id' => $this->user->id]);
        StoredEmail::create([
            'alias_id' => $alias->id,
            'from_preview' => 'ACME',
            'subject_preview' => 'Hi',
            'size_bytes' => 42,
            'encrypted_payload' => 'ciphertext-should-not-leak',
            'received_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/ghost-emails');

        $response->assertSuccessful();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('ACME', $data[0]['from_preview']);
        // Ciphertext must not appear in list response
        $this->assertArrayNotHasKey('encrypted_payload', $data[0]);
    }

    #[Test]
    public function stored_email_show_returns_ciphertext()
    {
        $alias = Alias::factory()->create(['user_id' => $this->user->id]);
        $stored = StoredEmail::create([
            'alias_id' => $alias->id,
            'from_preview' => 'X',
            'subject_preview' => 'Y',
            'size_bytes' => 1,
            'encrypted_payload' => '-----BEGIN PGP MESSAGE-----OK-----END PGP MESSAGE-----',
            'received_at' => now(),
        ]);

        $response = $this->getJson("/api/v1/ghost-emails/{$stored->id}");

        $response->assertSuccessful()
            ->assertJsonFragment(['encrypted_payload' => '-----BEGIN PGP MESSAGE-----OK-----END PGP MESSAGE-----']);
    }

    #[Test]
    public function user_cannot_access_other_users_stored_emails()
    {
        $otherUser = $this->createUser('other');
        $otherAlias = Alias::factory()->create(['user_id' => $otherUser->id]);
        $stored = StoredEmail::create([
            'alias_id' => $otherAlias->id,
            'from_preview' => 'x',
            'subject_preview' => 'y',
            'size_bytes' => 1,
            'encrypted_payload' => 'cipher',
            'received_at' => now(),
        ]);

        $this->getJson("/api/v1/ghost-emails/{$stored->id}")->assertNotFound();
        $this->deleteJson("/api/v1/ghost-emails/{$stored->id}")->assertNotFound();

        // Index should not leak it either
        $response = $this->getJson('/api/v1/ghost-emails');
        $response->assertSuccessful();
        $this->assertCount(0, $response->json('data'));
    }

    #[Test]
    public function ghost_inbox_service_skips_users_without_vault()
    {
        $alias = Alias::factory()->create([
            'user_id' => $this->user->id,
            'ghost_mode' => true,
        ]);
        // No vault

        $result = app(GhostInbox::class)->store($alias, 'raw email', 'from@a', 'subj');

        $this->assertNull($result);
        $this->assertEquals(0, StoredEmail::count());
    }

    #[Test]
    public function ghost_inbox_service_skips_non_pro_users()
    {
        $this->user->update(['plan' => 'free', 'vault_public_key' => 'pk']);
        $alias = Alias::factory()->create(['user_id' => $this->user->id, 'ghost_mode' => true]);

        $result = app(GhostInbox::class)->store($alias, 'raw', 'f', 's');

        $this->assertNull($result);
        $this->assertEquals(0, StoredEmail::count());
    }

    #[Test]
    public function prune_command_deletes_old_stored_emails()
    {
        $alias = Alias::factory()->create(['user_id' => $this->user->id]);

        $old = StoredEmail::create([
            'alias_id' => $alias->id,
            'from_preview' => 'x',
            'subject_preview' => 'y',
            'size_bytes' => 1,
            'encrypted_payload' => 'c',
            'received_at' => now()->subDays(60),
        ]);

        $fresh = StoredEmail::create([
            'alias_id' => $alias->id,
            'from_preview' => 'x',
            'subject_preview' => 'y',
            'size_bytes' => 1,
            'encrypted_payload' => 'c',
            'received_at' => now()->subDays(5),
        ]);

        $this->artisan('mailflusher:prune-ghost-emails --days=30')->assertSuccessful();

        $this->assertNull(StoredEmail::find($old->id));
        $this->assertNotNull(StoredEmail::find($fresh->id));
    }

    #[Test]
    public function preview_mode_encrypted_stores_no_previews()
    {
        if (! extension_loaded('gnupg')) {
            $this->markTestSkipped('gnupg extension required');
        }

        // This only tests the preview logic — we use a fake/test public key fingerprint
        // by bypassing encryption via reflection on the private previews() helper.
        $service = app(GhostInbox::class);
        $ref = new \ReflectionClass($service);
        $method = $ref->getMethod('previews');
        $method->setAccessible(true);

        $this->assertSame([null, null], $method->invoke($service, 'encrypted', 'hello', 'world'));
        $this->assertSame(['hello', 'world'], $method->invoke($service, 'preview_10', 'hello', 'world'));
        $this->assertSame(['1234567890', 'abcdefghij'], $method->invoke($service, 'preview_10', '1234567890XXXX', 'abcdefghijYYY'));
    }
}
