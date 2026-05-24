<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPaymentWebhookJob;
use App\Models\PaymentWebhookEvent;
use Illuminate\Http\Request;

class PayPalWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $parsed = $request->all();
        $eventId = $parsed['id'] ?? hash('sha256', $request->getContent());

        $event = PaymentWebhookEvent::firstOrCreate(
            ['provider' => 'paypal', 'event_id' => $eventId],
            [
                'event_type' => $parsed['event_type'] ?? null,
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
