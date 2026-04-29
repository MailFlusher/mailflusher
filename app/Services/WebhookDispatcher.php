<?php

namespace App\Services;

use App\Jobs\DeliverWebhook;
use App\Models\User;
use App\Models\Webhook;
use App\Models\WebhookDelivery;

class WebhookDispatcher
{
    /**
     * Fire an event for a user. Creates a pending delivery row for every
     * active webhook of that user that subscribes to this event, and
     * queues a job to deliver each one.
     *
     * Safe to call on every email — never blocks the caller.
     */
    public function dispatch(User $user, string $event, array $payload): void
    {
        if (! $user->canUseWebhooks()) {
            return;
        }

        $webhooks = Webhook::where('user_id', $user->id)
            ->where('active', true)
            ->get()
            ->filter(fn (Webhook $w) => $w->subscribesTo($event));

        if ($webhooks->isEmpty()) {
            return;
        }

        $enriched = array_merge($payload, [
            'event' => $event,
            'timestamp' => now()->toIso8601String(),
        ]);

        foreach ($webhooks as $webhook) {
            $delivery = WebhookDelivery::create([
                'webhook_id' => $webhook->id,
                'event' => $event,
                'payload' => $enriched,
                'status' => 'pending',
                'attempts' => 0,
            ]);

            DeliverWebhook::dispatch($delivery->id);
        }
    }
}
