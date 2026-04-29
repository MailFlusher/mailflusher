<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use App\Http\Requests\Webhook\StoreWebhookRequest;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index()
    {
        if (! user()->canUseWebhooks()) {
            return response()->json(['data' => []]);
        }

        $webhooks = Webhook::where('user_id', user()->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $webhooks]);
    }

    public function store(StoreWebhookRequest $request)
    {
        $webhook = Webhook::create([
            'user_id' => user()->id,
            'url' => $request->url,
            'events' => $request->events,
            'description' => $request->description,
            'secret' => Webhook::generateSecret(),
            'active' => $request->boolean('active', true),
        ]);

        // Return secret once on creation so the user can save it
        return response()->json([
            'data' => $webhook,
            'secret' => $webhook->secret,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $webhook = Webhook::where('user_id', user()->id)->where('id', $id)->firstOrFail();

        $request->validate([
            'url' => ['nullable', 'url', 'starts_with:https://', 'max:500'],
            'description' => ['nullable', 'string', 'max:200'],
            'events' => ['nullable', 'array', 'min:1'],
            'events.*' => ['string', 'in:'.implode(',', \App\Http\Requests\StoreWebhookRequest::ALLOWED_EVENTS)],
            'active' => ['nullable', 'boolean'],
        ]);

        $webhook->fill($request->only(['url', 'description', 'events', 'active']));
        $webhook->save();

        return response()->json(['data' => $webhook]);
    }

    public function destroy(string $id)
    {
        $webhook = Webhook::where('user_id', user()->id)->where('id', $id)->firstOrFail();

        $webhook->delete();

        return response()->noContent();
    }

    public function deliveries(string $id)
    {
        $webhook = Webhook::where('user_id', user()->id)->where('id', $id)->firstOrFail();

        $deliveries = WebhookDelivery::where('webhook_id', $webhook->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get(['id', 'event', 'status', 'response_code', 'attempts', 'delivered_at', 'created_at']);

        return response()->json(['data' => $deliveries]);
    }
}
