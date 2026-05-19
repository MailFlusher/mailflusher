<?php

namespace App\Http\Controllers\Billing;

use App\Models\User;
use App\Notifications\Account\InvoicePaymentFailed;
use App\Notifications\Account\SubscriptionEnded;
use App\Services\PlanService;
use Laravel\Cashier\Http\Controllers\WebhookController;

class StripeWebhookController extends WebhookController
{
    public function __construct(protected PlanService $planService)
    {
        // Fail closed if the webhook secret is missing — Cashier's parent only
        // attaches signature verification when this is set, so an unset value
        // silently turns the endpoint into "anyone can forge billing events."
        if (! config('cashier.webhook.secret')) {
            abort(500, 'Stripe webhook secret is not configured.');
        }

        parent::__construct();
    }

    /**
     * Subscription activated / plan changed. Stripe fires this on both initial
     * checkout completion and subsequent plan changes via the customer portal.
     * Also fires on dunning state transitions (active → past_due → unpaid).
     */
    public function handleCustomerSubscriptionUpdated(array $payload): void
    {
        parent::handleCustomerSubscriptionUpdated($payload);

        $stripeSubscription = $payload['data']['object'];
        $user = User::where('stripe_id', $stripeSubscription['customer'])->first();

        if (! $user) {
            return;
        }

        $priceId = $stripeSubscription['items']['data'][0]['price']['id'] ?? null;
        $status = $stripeSubscription['status'];

        // Mirror Stripe's state onto the user so the rest of the app (promo guard,
        // dunning banner) doesn't have to round-trip through Cashier or Stripe.
        $user->update([
            'stripe_subscription_id' => $stripeSubscription['id'],
            'stripe_status' => $status,
        ]);

        if (in_array($status, ['active', 'trialing'])) {
            $planName = $this->getPlanNameFromPriceId($priceId);
            if ($planName === 'free') {
                return;
            }

            $this->planService->grantPlan(
                user: $user,
                plan: $planName,
                duration: null, // indefinite — Cashier manages renewal
                source: "stripe_subscription:{$priceId}",
                isTrial: false,
            );
        }
    }

    /**
     * Subscription cancelled (whether by user or by Stripe after retries failed).
     * Run the downgrade and send the "ended" email.
     */
    public function handleCustomerSubscriptionDeleted(array $payload): void
    {
        parent::handleCustomerSubscriptionDeleted($payload);

        $stripeSubscription = $payload['data']['object'];
        $user = User::where('stripe_id', $stripeSubscription['customer'])->first();

        if (! $user) {
            return;
        }

        $result = $this->planService->expirePlan($user, 'stripe_subscription_cancelled');

        $user->update([
            'stripe_subscription_id' => null,
            'stripe_status' => null,
        ]);

        // Only notify if there was actually a non-free plan to end (avoids
        // spamming users whose subscription was already in the free state).
        if ($result['previous_plan_key'] !== 'free') {
            $user->fresh()->notify(new SubscriptionEnded(
                $result['previous_plan_name'],
                $result['deactivated'],
            ));
        }
    }

    /**
     * Invoice payment failed — Stripe will retry per the dunning settings.
     * Notify the user so they can update their card before retries are exhausted.
     */
    public function handleInvoicePaymentFailed(array $payload): void
    {
        // Cashier's base WebhookController has no default handler for this event,
        // so we don't call parent. We just notify the user — Cashier mirrors the
        // status (past_due, then canceled) via separate customer.subscription.updated
        // and customer.subscription.deleted events.
        $invoice = $payload['data']['object'];
        $user = User::where('stripe_id', $invoice['customer'])->first();

        if (! $user) {
            return;
        }

        $user->notify(new InvoicePaymentFailed(
            amountCents: $invoice['amount_due'] ?? null,
            currency: $invoice['currency'] ?? null,
        ));
    }

    private function getPlanNameFromPriceId(?string $priceId): string
    {
        if (! $priceId) {
            return 'free';
        }

        foreach (config('mailflusher.plans') as $key => $plan) {
            if (isset($plan['stripe_price_id']) && $plan['stripe_price_id'] === $priceId) {
                return $key;
            }
        }

        return 'free';
    }
}
