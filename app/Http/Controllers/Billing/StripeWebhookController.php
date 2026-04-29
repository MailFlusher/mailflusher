<?php

namespace App\Http\Controllers\Billing;

use App\Models\User;
use Laravel\Cashier\Http\Controllers\WebhookController;

class StripeWebhookController extends WebhookController
{
    /**
     * Handle customer subscription updated.
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

        if (in_array($status, ['active', 'trialing'])) {
            $planName = $this->getPlanNameFromPriceId($priceId);
            $user->update(['plan' => $planName, 'plan_expires_at' => null]);
        }
    }

    /**
     * Handle customer subscription deleted (cancelled and expired).
     */
    public function handleCustomerSubscriptionDeleted(array $payload): void
    {
        parent::handleCustomerSubscriptionDeleted($payload);

        $stripeSubscription = $payload['data']['object'];
        $user = User::where('stripe_id', $stripeSubscription['customer'])->first();

        if ($user) {
            $user->update(['plan' => 'free', 'plan_expires_at' => now()]);
        }
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
