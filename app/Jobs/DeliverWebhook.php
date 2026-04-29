<?php

namespace App\Jobs;

use App\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeliverWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const MAX_ATTEMPTS = 5;

    public int $tries = 1; // we manage retries via WebhookDelivery.next_retry_at ourselves

    public function __construct(public int $deliveryId) {}

    public function handle(): void
    {
        $delivery = WebhookDelivery::with('webhook')->find($this->deliveryId);

        if (! $delivery) {
            return;
        }

        if (in_array($delivery->status, ['success', 'giving_up'], true)) {
            return;
        }

        $webhook = $delivery->webhook;
        if (! $webhook || ! $webhook->active) {
            return;
        }

        $delivery->increment('attempts');

        $body = json_encode($delivery->payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $signature = hash_hmac('sha256', $body, $webhook->secret);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'User-Agent' => 'MailFlusher-Webhook/1.0',
                'X-MailFlusher-Event' => $delivery->event,
                'X-MailFlusher-Signature' => 'sha256='.$signature,
                'X-MailFlusher-Delivery-Id' => (string) $delivery->id,
            ])->timeout(10)->withBody($body, 'application/json')->post($webhook->url);

            $webhook->update([
                'last_delivered_at' => now(),
                'last_response_code' => $response->status(),
            ]);

            if ($response->successful()) {
                $delivery->markSuccess($response->status(), $response->body());

                return;
            }

            $givingUp = $delivery->attempts >= self::MAX_ATTEMPTS;
            $delivery->markFailed($response->status(), $response->body(), $givingUp);
            if (! $givingUp) {
                self::dispatch($delivery->id)->delay($delivery->next_retry_at);
            }
        } catch (\Throwable $e) {
            Log::warning('Webhook delivery failed', [
                'delivery_id' => $delivery->id,
                'webhook_id' => $webhook->id,
                'error' => $e->getMessage(),
            ]);

            $givingUp = $delivery->attempts >= self::MAX_ATTEMPTS;
            $delivery->markFailed(null, $e->getMessage(), $givingUp);
            if (! $givingUp) {
                self::dispatch($delivery->id)->delay($delivery->next_retry_at);
            }
        }
    }
}
