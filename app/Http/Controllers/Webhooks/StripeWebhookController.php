<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPaymentWebhookJob;
use App\Models\PaymentWebhookEvent;
use App\Services\Payments\Webhook\StripeWebhookHandler;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, StripeWebhookHandler $handler)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $parsed = $handler->verifyAndParse($payload, $signature);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $eventId = $parsed['id'] ?? hash('sha256', $payload);
        $event = PaymentWebhookEvent::firstOrCreate(
            ['provider' => 'stripe', 'event_id' => $eventId],
            [
                'event_type' => $parsed['type'] ?? null,
                'payload' => $parsed,
                'status' => 'pending',
            ]
        );

        if ($event->wasRecentlyCreated || $event->status === 'pending') {
            ProcessPaymentWebhookJob::dispatch($event);
        }

        return response()->json(['received' => true]);
    }
}
