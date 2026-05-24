<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\DataTransferObjects\PaymentInitiationResult;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentRefund;

class ManualGateway implements PaymentGatewayInterface
{
    public function driver(): string
    {
        return 'manual';
    }

    public function initiate(Payment $payment, Order $order, PaymentMethod $method, array $context = []): PaymentInitiationResult
    {
        $metadata = array_merge($payment->metadata ?? [], array_filter([
            'bank_reference' => $context['bank_reference'] ?? null,
            'payment_receipt_path' => $context['payment_receipt_path'] ?? null,
            'payment_receipt_original_name' => $context['payment_receipt_original_name'] ?? null,
            'manual_driver' => $method->driver,
        ], fn ($v) => $v !== null && $v !== ''));

        $payment->update(['metadata' => $metadata]);

        return new PaymentInitiationResult(
            view: 'frontend.pages.checkout.pending',
            viewData: [
                'order' => $order->load(['items', 'payments.paymentMethod']),
                'payment' => $payment->fresh('paymentMethod'),
                'method' => $method,
            ],
        );
    }

    public function refund(Payment $payment, float $amount, ?string $reason = null): PaymentRefund
    {
        return PaymentRefund::create([
            'payment_id' => $payment->id,
            'amount' => $amount,
            'currency' => $payment->currency,
            'status' => 'completed',
            'reason' => $reason,
            'processed_by' => auth()->id(),
            'metadata' => ['manual' => true],
        ]);
    }

    public function supportsManualConfirmation(): bool
    {
        return true;
    }
}
