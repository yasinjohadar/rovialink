<?php

namespace App\Jobs;

use App\Models\PaymentWebhookEvent;
use App\Services\Payments\Webhook\StripeWebhookHandler;
use App\Services\Payments\Webhook\PayPalWebhookHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPaymentWebhookJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PaymentWebhookEvent $webhookEvent
    ) {}

    public function handle(StripeWebhookHandler $stripeHandler, PayPalWebhookHandler $paypalHandler): void
    {
        try {
            match ($this->webhookEvent->provider) {
                'stripe' => $stripeHandler->handle($this->webhookEvent),
                'paypal' => $paypalHandler->handle($this->webhookEvent),
                default => throw new \RuntimeException('Unknown provider'),
            };
            $this->webhookEvent->markProcessed();
        } catch (\Throwable $e) {
            $this->webhookEvent->markFailed($e->getMessage());
            report($e);
        }
    }
}
