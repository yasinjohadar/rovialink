<?php

namespace App\Services\Payments\Webhook;

use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Services\Payments\OrderPaymentSyncService;
use App\Services\Payments\PaymentSettingsService;
use Stripe\Webhook;
use Stripe\Stripe;

class StripeWebhookHandler
{
    public function __construct(
        protected OrderPaymentSyncService $syncService,
        protected PaymentSettingsService $paymentSettings,
    ) {}

    public function handle(PaymentWebhookEvent $event): void
    {
        $payload = $event->payload;
        $type = $payload['type'] ?? $event->event_type;

        match ($type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($payload['data']['object'] ?? []),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($payload['data']['object'] ?? []),
            'charge.refunded' => $this->handleChargeRefunded($payload['data']['object'] ?? []),
            default => null,
        };
    }

    public function verifyAndParse(string $payload, ?string $signature): array
    {
        $secret = $this->paymentSettings->stripeWebhookSecret();
        if ($secret && $signature) {
            Stripe::setApiKey($this->paymentSettings->stripeSecret());
            $event = Webhook::constructEvent($payload, $signature, $secret);

            return json_decode(json_encode($event), true);
        }

        return json_decode($payload, true) ?: [];
    }

    protected function handleCheckoutCompleted(array $session): void
    {
        $paymentId = $session['metadata']['payment_id'] ?? null;
        $payment = $paymentId
            ? Payment::find($paymentId)
            : Payment::where('transaction_id', $session['id'] ?? null)->first();

        if (! $payment || ($session['payment_status'] ?? '') !== 'paid') {
            return;
        }

        $this->syncService->markCompleted($payment, $session['payment_intent'] ?? null, [
            'stripe_session_id' => $session['id'] ?? null,
            'stripe_payment_intent' => $session['payment_intent'] ?? null,
        ]);
    }

    protected function handlePaymentFailed(array $intent): void
    {
        $payment = Payment::where('transaction_id', $intent['id'] ?? null)->first();
        if ($payment) {
            $this->syncService->markFailed($payment, $intent['last_payment_error']['message'] ?? 'failed');
        }
    }

    protected function handleChargeRefunded(array $charge): void
    {
        $paymentIntent = $charge['payment_intent'] ?? null;
        if (! $paymentIntent) {
            return;
        }

        $payment = Payment::whereJsonContains('metadata->stripe_payment_intent', $paymentIntent)->first()
            ?? Payment::where('transaction_id', $paymentIntent)->first();

        if ($payment) {
            $this->syncService->markRefunded($payment);
        }
    }
}
