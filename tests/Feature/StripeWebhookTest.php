<?php

namespace Tests\Feature;

use App\Http\Controllers\Billing\StripeWebhookController;
use App\Models\Alias;
use App\Notifications\Account\InvoicePaymentFailed;
use App\Notifications\Account\SubscriptionEnded;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected StripeWebhookController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        parent::setUpSanctum();

        // The controller fail-closes if the webhook secret is missing; set a
        // fake one for the test so the constructor passes.
        config(['cashier.webhook.secret' => 'whsec_test_fake']);

        $this->controller = app(StripeWebhookController::class);

        // Wire a Stripe customer id onto the test user so the webhook can find them.
        $this->user->update(['stripe_id' => 'cus_test123']);

        // Set known stripe price IDs for predictable lookup in tests.
        config([
            'mailflusher.plans.standard.stripe_price_id' => 'price_standard_test',
            'mailflusher.plans.pro.stripe_price_id' => 'price_pro_test',
        ]);
    }

    /**
     * Returns a minimum-viable subscription payload that satisfies Cashier's base
     * handleCustomerSubscriptionUpdated. Use `array_merge_recursive` or splat to
     * customize.
     */
    protected function subscriptionPayload(array $overrides = []): array
    {
        return [
            'data' => ['object' => array_merge([
                'id' => 'sub_test',
                'customer' => 'cus_test123',
                'status' => 'active',
                'items' => ['data' => [[
                    'id' => 'si_test',
                    'price' => ['id' => 'price_pro_test', 'product' => 'prod_test'],
                    'quantity' => 1,
                ]]],
                'cancel_at_period_end' => false,
                'current_period_end' => time() + 30 * 86400,
                'metadata' => [],
            ], $overrides)],
        ];
    }

    #[Test]
    public function subscription_updated_active_grants_plan_via_plan_service()
    {
        $this->controller->handleCustomerSubscriptionUpdated($this->subscriptionPayload());

        $this->user->refresh();

        $this->assertSame('pro', $this->user->plan);
        $this->assertNull($this->user->plan_expires_at, 'paid grants are indefinite');

        $this->assertDatabaseHas('plan_grants', [
            'user_id' => $this->user->id,
            'plan' => 'pro',
            'source' => 'stripe_subscription:price_pro_test',
            'ends_at' => null,
        ]);
    }

    #[Test]
    public function subscription_updated_clears_trial_state_on_conversion()
    {
        $this->user->update([
            'plan' => 'pro',
            'plan_expires_at' => now()->addDays(5),
            'trial_ends_at' => now()->addDays(5),
            'trial_reminder_stage' => 7,
            'has_used_trial' => true,
        ]);

        $this->controller->handleCustomerSubscriptionUpdated($this->subscriptionPayload());

        $this->user->refresh();

        $this->assertNull($this->user->trial_ends_at);
        $this->assertNull($this->user->trial_reminder_stage);
        $this->assertNull($this->user->plan_expires_at);
        $this->assertTrue($this->user->has_used_trial, 'has_used_trial persists across conversion');
    }

    #[Test]
    public function subscription_deleted_runs_downgrade_and_sends_notification()
    {
        Notification::fake();

        Alias::factory()->count(15)->create([
            'user_id' => $this->user->id,
            'active' => true,
        ]);

        $this->user->update(['plan' => 'pro']);

        $this->controller->handleCustomerSubscriptionDeleted(
            $this->subscriptionPayload(['status' => 'canceled'])
        );

        $this->user->refresh();

        $this->assertSame('free', $this->user->plan);
        $this->assertSame(10, $this->user->aliases()->where('active', true)->count(), '5 excess aliases deactivated');

        Notification::assertSentTo(
            $this->user,
            SubscriptionEnded::class,
            fn ($n) => $n->previousPlanName === 'Pro' && $n->deactivated['aliases'] === 5,
        );
    }

    #[Test]
    public function invoice_payment_failed_notifies_user()
    {
        Notification::fake();

        $this->controller->handleInvoicePaymentFailed([
            'data' => ['object' => [
                'customer' => 'cus_test123',
                'amount_due' => 500,
                'currency' => 'eur',
            ]],
        ]);

        Notification::assertSentTo(
            $this->user,
            InvoicePaymentFailed::class,
            fn ($n) => $n->amountCents === 500 && $n->currency === 'eur',
        );
    }

    #[Test]
    public function webhook_for_unknown_customer_is_noop()
    {
        Notification::fake();

        $this->controller->handleInvoicePaymentFailed([
            'data' => ['object' => [
                'customer' => 'cus_nonexistent',
                'amount_due' => 500,
                'currency' => 'eur',
            ]],
        ]);

        Notification::assertNothingSent();
    }
}
