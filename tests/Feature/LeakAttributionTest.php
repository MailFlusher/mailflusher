<?php

namespace Tests\Feature;

use App\Models\Alias;
use App\Models\AliasLeakEvent;
use App\Models\AliasSenderObservation;
use App\Services\LeakAttributor;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeakAttributionTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected LeakAttributor $attributor;

    protected function setUp(): void
    {
        parent::setUp();
        parent::setUpSanctum();

        $this->attributor = app(LeakAttributor::class);
    }

    #[Test]
    public function extract_domain_handles_plain_and_name_formats()
    {
        $this->assertSame('brand.com', $this->attributor->extractDomain('hello@brand.com'));
        $this->assertSame('brand.com', $this->attributor->extractDomain('Brand <hello@brand.com>'));
        $this->assertSame('brand.com', $this->attributor->extractDomain('"Brand Support" <support@Brand.com>'));
        $this->assertNull($this->attributor->extractDomain(null));
        $this->assertNull($this->attributor->extractDomain(''));
        $this->assertNull($this->attributor->extractDomain('no-at-sign'));
    }

    #[Test]
    public function esp_allowlist_catches_subdomains()
    {
        $this->assertTrue($this->attributor->isEspDomain('sendgrid.net'));
        $this->assertTrue($this->attributor->isEspDomain('email.sendgrid.net'));
        $this->assertTrue($this->attributor->isEspDomain('u123456.list-manage.com'));
        $this->assertFalse($this->attributor->isEspDomain('brand.com'));
    }

    #[Test]
    public function first_observation_is_recorded_and_sets_baseline()
    {
        $alias = Alias::factory()->create(['user_id' => $this->user->id]);

        $this->attributor->record($alias, 'hello@netflix.com');

        $this->assertDatabaseHas('alias_sender_observations', [
            'alias_id' => $alias->id,
            'sender_domain' => 'netflix.com',
            'email_count' => 1,
        ]);

        $alias->refresh();
        $this->assertSame('netflix.com', $alias->baseline_sender_domain);
    }

    #[Test]
    public function repeated_senders_increment_count_not_rows()
    {
        $alias = Alias::factory()->create(['user_id' => $this->user->id]);

        $this->attributor->record($alias, 'hello@netflix.com');
        $this->attributor->record($alias, 'Support <support@netflix.com>');

        $this->assertEquals(1, AliasSenderObservation::where('alias_id', $alias->id)->count());
        $this->assertEquals(2, AliasSenderObservation::where('alias_id', $alias->id)->first()->email_count);
    }

    #[Test]
    public function baseline_locks_after_configured_sender_count()
    {
        config(['leak_attribution.baseline_lock_after_senders' => 2]);

        $alias = Alias::factory()->create(['user_id' => $this->user->id]);

        $this->attributor->record($alias, 'hello@netflix.com');
        $this->assertNull($alias->fresh()->baseline_locked_at);

        $this->attributor->record($alias, 'hello@amazon.com');
        $this->assertNotNull($alias->fresh()->baseline_locked_at);
    }

    #[Test]
    public function unexpected_sender_after_lock_creates_leak_event()
    {
        config(['leak_attribution.baseline_lock_after_senders' => 1]);

        $alias = Alias::factory()->create(['user_id' => $this->user->id]);

        // Lock baseline to netflix.com
        $this->attributor->record($alias, 'hello@netflix.com');
        $this->assertNotNull($alias->fresh()->baseline_locked_at);

        // Unexpected sender
        $this->attributor->record($alias, 'spam@randomhouse.biz');

        $this->assertDatabaseHas('alias_leak_events', [
            'alias_id' => $alias->id,
            'sender_domain' => 'randomhouse.biz',
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function esp_sender_after_lock_does_not_create_leak_event()
    {
        config(['leak_attribution.baseline_lock_after_senders' => 1]);

        $alias = Alias::factory()->create(['user_id' => $this->user->id]);
        $this->attributor->record($alias, 'hello@netflix.com');

        // ESPs are never treated as leaks.
        $this->attributor->record($alias, 'via@sendgrid.net');
        $this->attributor->record($alias, 'mc@u123.list-manage.com');

        $this->assertEquals(0, AliasLeakEvent::where('alias_id', $alias->id)->count());
    }

    #[Test]
    public function same_apex_domain_does_not_trigger_leak_event()
    {
        config(['leak_attribution.baseline_lock_after_senders' => 1]);

        $alias = Alias::factory()->create(['user_id' => $this->user->id]);
        $this->attributor->record($alias, 'hello@netflix.com');

        $this->attributor->record($alias, 'support@email.netflix.com');

        $this->assertEquals(0, AliasLeakEvent::where('alias_id', $alias->id)->count());
    }

    #[Test]
    public function duplicate_leak_events_are_not_created()
    {
        config(['leak_attribution.baseline_lock_after_senders' => 1]);

        $alias = Alias::factory()->create(['user_id' => $this->user->id]);
        $this->attributor->record($alias, 'hello@netflix.com');

        $this->attributor->record($alias, 'spam@randomhouse.biz');
        $this->attributor->record($alias, 'more@randomhouse.biz');

        $this->assertEquals(1, AliasLeakEvent::where('alias_id', $alias->id)->count());
    }

    #[Test]
    public function confirm_endpoint_marks_event_confirmed()
    {
        $alias = Alias::factory()->create(['user_id' => $this->user->id]);
        $event = AliasLeakEvent::create([
            'alias_id' => $alias->id,
            'sender_domain' => 'leaky.com',
            'detected_at' => now(),
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/v1/leak-events/{$event->id}/confirm");

        $response->assertSuccessful();
        $this->assertSame('confirmed', $event->fresh()->status);
    }

    #[Test]
    public function dismiss_endpoint_marks_event_dismissed()
    {
        $alias = Alias::factory()->create(['user_id' => $this->user->id]);
        $event = AliasLeakEvent::create([
            'alias_id' => $alias->id,
            'sender_domain' => 'not-a-leak.com',
            'detected_at' => now(),
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/v1/leak-events/{$event->id}/dismiss");

        $response->assertSuccessful();
        $this->assertSame('dismissed', $event->fresh()->status);
    }

    #[Test]
    public function user_cannot_confirm_another_users_event()
    {
        $otherUser = $this->createUser('other');
        $otherAlias = Alias::factory()->create(['user_id' => $otherUser->id]);
        $event = AliasLeakEvent::create([
            'alias_id' => $otherAlias->id,
            'sender_domain' => 'leak.com',
            'detected_at' => now(),
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/v1/leak-events/{$event->id}/confirm");

        $response->assertNotFound();
        $this->assertSame('pending', $event->fresh()->status);
    }
}
