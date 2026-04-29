<?php

namespace Tests\Feature;

use App\Jobs\DeliverWebhook;
use App\Models\Alias;
use App\Models\AliasLeakEvent;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use App\Services\LeakAttributor;
use App\Services\WebhookDispatcher;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        parent::setUpSanctum();

        $this->user->update(['plan' => 'standard']);
    }

    #[Test]
    public function free_user_cannot_create_webhook()
    {
        $this->user->update(['plan' => 'free']);

        $response = $this->postJson('/api/v1/webhooks', [
            'url' => 'https://example.com/hook',
            'events' => ['alias.received'],
        ]);

        $response->assertForbidden();
        $this->assertEquals(0, Webhook::count());
    }

    #[Test]
    public function standard_user_can_create_webhook_and_gets_secret_once()
    {
        $response = $this->postJson('/api/v1/webhooks', [
            'url' => 'https://example.com/hook',
            'events' => ['alias.received', 'alias.blocked'],
            'description' => 'My hook',
        ]);

        $response->assertStatus(201);
        $this->assertNotEmpty($response->json('secret'));
        $this->assertEquals(1, Webhook::count());

        $w = Webhook::first();
        $this->assertEquals($this->user->id, $w->user_id);
        $this->assertEqualsCanonicalizing(['alias.received', 'alias.blocked'], $w->events);
    }

    #[Test]
    public function webhook_rejects_non_https_url()
    {
        $response = $this->postJson('/api/v1/webhooks', [
            'url' => 'http://example.com/hook',
            'events' => ['alias.received'],
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function webhook_rejects_loopback_urls()
    {
        $response = $this->postJson('/api/v1/webhooks', [
            'url' => 'https://127.0.0.1/hook',
            'events' => ['alias.received'],
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function webhook_rejects_unknown_events()
    {
        $response = $this->postJson('/api/v1/webhooks', [
            'url' => 'https://example.com/hook',
            'events' => ['alias.totally_made_up'],
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function user_cannot_see_or_modify_other_users_webhooks()
    {
        $other = $this->createUser('other');
        $foreign = Webhook::create([
            'user_id' => $other->id,
            'url' => 'https://example.com/foreign',
            'events' => ['alias.received'],
            'secret' => Webhook::generateSecret(),
            'active' => true,
        ]);

        $this->patchJson("/api/v1/webhooks/{$foreign->id}", ['active' => false])->assertNotFound();
        $this->deleteJson("/api/v1/webhooks/{$foreign->id}")->assertNotFound();
        $this->getJson("/api/v1/webhooks/{$foreign->id}/deliveries")->assertNotFound();
    }

    #[Test]
    public function dispatcher_creates_delivery_and_queues_job()
    {
        Queue::fake();

        $webhook = Webhook::create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com/hook',
            'events' => ['alias.received'],
            'secret' => Webhook::generateSecret(),
            'active' => true,
        ]);

        app(WebhookDispatcher::class)->dispatch($this->user, 'alias.received', [
            'alias_id' => 'abc',
        ]);

        $this->assertEquals(1, WebhookDelivery::count());
        Queue::assertPushed(DeliverWebhook::class);
    }

    #[Test]
    public function dispatcher_skips_inactive_or_non_subscribed_webhooks()
    {
        Queue::fake();

        Webhook::create([
            'user_id' => $this->user->id,
            'url' => 'https://a.com',
            'events' => ['alias.received'],
            'secret' => Webhook::generateSecret(),
            'active' => false,
        ]);

        Webhook::create([
            'user_id' => $this->user->id,
            'url' => 'https://b.com',
            'events' => ['alias.leaked'],
            'secret' => Webhook::generateSecret(),
            'active' => true,
        ]);

        app(WebhookDispatcher::class)->dispatch($this->user, 'alias.received', []);

        $this->assertEquals(0, WebhookDelivery::count());
        Queue::assertNothingPushed();
    }

    #[Test]
    public function dispatcher_does_nothing_for_free_user()
    {
        Queue::fake();
        $this->user->update(['plan' => 'free']);

        Webhook::create([
            'user_id' => $this->user->id,
            'url' => 'https://a.com',
            'events' => ['alias.received'],
            'secret' => Webhook::generateSecret(),
            'active' => true,
        ]);

        app(WebhookDispatcher::class)->dispatch($this->user, 'alias.received', []);

        $this->assertEquals(0, WebhookDelivery::count());
        Queue::assertNothingPushed();
    }

    #[Test]
    public function delivery_job_signs_body_with_hmac_and_marks_success()
    {
        Http::fake([
            'https://example.com/hook' => Http::response('ok', 200),
        ]);

        $webhook = Webhook::create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com/hook',
            'events' => ['alias.received'],
            'secret' => 'mysecret',
            'active' => true,
        ]);

        $delivery = WebhookDelivery::create([
            'webhook_id' => $webhook->id,
            'event' => 'alias.received',
            'payload' => ['alias_id' => 'abc', 'event' => 'alias.received', 'timestamp' => now()->toIso8601String()],
            'status' => 'pending',
            'attempts' => 0,
        ]);

        (new DeliverWebhook($delivery->id))->handle();

        $delivery->refresh();
        $this->assertEquals('success', $delivery->status);
        $this->assertEquals(200, $delivery->response_code);
        $this->assertEquals(1, $delivery->attempts);

        Http::assertSent(function ($request) {
            $sig = $request->header('X-MailFlusher-Signature')[0] ?? '';
            $body = $request->body();
            $expected = 'sha256='.hash_hmac('sha256', $body, 'mysecret');

            return $sig === $expected
                && ($request->header('X-MailFlusher-Event')[0] ?? '') === 'alias.received';
        });
    }

    #[Test]
    public function delivery_job_marks_failed_on_5xx_and_schedules_retry()
    {
        // Prevent the handler's self-dispatch retry from running inline on the sync queue
        Queue::fake();

        Http::fake([
            'https://example.com/hook' => Http::response('boom', 503),
        ]);

        $webhook = Webhook::create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com/hook',
            'events' => ['alias.received'],
            'secret' => 's',
            'active' => true,
        ]);

        $delivery = WebhookDelivery::create([
            'webhook_id' => $webhook->id,
            'event' => 'alias.received',
            'payload' => [],
            'status' => 'pending',
            'attempts' => 0,
        ]);

        (new DeliverWebhook($delivery->id))->handle();

        $delivery->refresh();
        $this->assertEquals('failed', $delivery->status);
        $this->assertEquals(503, $delivery->response_code);
        $this->assertNotNull($delivery->next_retry_at);

        // And a retry was scheduled via Queue::fake
        Queue::assertPushed(DeliverWebhook::class);
    }

    #[Test]
    public function delivery_job_gives_up_after_max_attempts()
    {
        Http::fake([
            'https://example.com/hook' => Http::response('boom', 500),
        ]);

        $webhook = Webhook::create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com/hook',
            'events' => ['alias.received'],
            'secret' => 's',
            'active' => true,
        ]);

        $delivery = WebhookDelivery::create([
            'webhook_id' => $webhook->id,
            'event' => 'alias.received',
            'payload' => [],
            'status' => 'pending',
            'attempts' => DeliverWebhook::MAX_ATTEMPTS - 1, // next attempt hits the cap
        ]);

        (new DeliverWebhook($delivery->id))->handle();

        $delivery->refresh();
        $this->assertEquals('giving_up', $delivery->status);
        $this->assertNull($delivery->next_retry_at);
    }

    #[Test]
    public function leak_attribution_dispatches_webhook_event()
    {
        Queue::fake();

        Webhook::create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com/hook',
            'events' => ['alias.leaked'],
            'secret' => 's',
            'active' => true,
        ]);

        config(['leak_attribution.baseline_lock_after_senders' => 1]);
        $alias = Alias::factory()->create(['user_id' => $this->user->id]);

        $attributor = app(LeakAttributor::class);
        $attributor->record($alias, 'hello@brand.com');
        $attributor->record($alias, 'spam@leaker.com');

        $this->assertEquals(1, AliasLeakEvent::count());
        $this->assertEquals(1, WebhookDelivery::where('event', 'alias.leaked')->count());
        Queue::assertPushed(DeliverWebhook::class);
    }
}
