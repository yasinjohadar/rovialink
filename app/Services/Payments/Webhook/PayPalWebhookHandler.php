<?php

namespace App\Services\Payments\Webhook;

use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Services\Payments\OrderPaymentSyncService;

class PayPalWebhookHandler
{
    public function __construct(
        protected OrderPaymentSyncService $syncService
    ) {}

    public function handle(PaymentWebhookEvent $event): void
    {
        $payload = $event->payload;
        $type = $payload['event_type'] ?? $event->event_type;

        match ($type) {
            'PAYMENT.CAPTURE.COMPLETED' => $this->handleCaptureCompleted($payload['resource'] ?? []),
            'PAYMENT.CAPTURE.REFUNDED' => $this->handleCaptureRefunded($payload['resource'] ?? []),
            default => null,
        };
    }

    protected function handleCaptureCompleted(array $resource): void
    {
        $customId = $resource['custom_id'] ?? null;
        $payment = $customId
            ? Payment::find($customId)
            : Payment::where('transaction_id', $resource['supplementary_data']['related_ids']['order_id'] ?? '')->first();

        if (! $payment) {
            return;
        }

        $this->syncService->markCompleted($payment, $resource['id'] ?? null, [
            'paypal_capture_id' => $resource['id'] ?? null,
        ]);
    }

    protected function handleCaptureRefunded(array $resource): void
    {
        $payment = Payment::whereJsonContains('metadata->paypal_capture_id', $resource['id'] ?? null)->first();
        if ($payment) {
            $this->syncService->markRefunded($payment);
        }
    }
}
