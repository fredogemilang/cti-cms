<?php

namespace App\Services;

use App\Jobs\DeliverWebhook;
use App\Models\Webhook;
use App\Models\WebhookDelivery;

class WebhookDispatcher
{
    public const EVENTS = [
        'form.submitted',
        'post.published',
        'page.published',
        'page.updated',
        'user.registered',
        'event.registration.created',
        'media.uploaded',
    ];

    /**
     * Match registered webhooks for the event and queue a delivery job per match.
     */
    public function dispatch(string $event, array $payload): void
    {
        $webhooks = Webhook::where('is_active', true)->get()
            ->filter(fn (Webhook $w) => $w->listensTo($event));

        foreach ($webhooks as $webhook) {
            $delivery = WebhookDelivery::create([
                'webhook_id' => $webhook->id,
                'event' => $event,
                'payload' => [
                    'event' => $event,
                    'timestamp' => now()->toAtomString(),
                    'data' => $payload,
                ],
                'status' => 'pending',
                'attempts' => 0,
            ]);

            DeliverWebhook::dispatch($delivery->id);
        }
    }
}
