<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\DataTransferObjects\PaymentInitiationResult;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\Payments\Gateways\ManualGateway;
use App\Services\Payments\Gateways\PayPalGateway;
use App\Services\Payments\Gateways\StripeGateway;

class PaymentOrchestrator
{
    public function __construct(
        protected ManualGateway $manualGateway,
        protected StripeGateway $stripeGateway,
        protected PayPalGateway $payPalGateway,
        protected OrderPaymentSyncService $syncService,
    ) {}

    public function gatewayFor(PaymentMethod $method): PaymentGatewayInterface
    {
        $driver = $method->resolvedGateway();

        return match ($driver) {
            'stripe' => $this->stripeGateway,
            'paypal' => $this->payPalGateway,
            default => $this->manualGateway,
        };
    }

    public function initiate(Order $order, array $context = []): PaymentInitiationResult
    {
        $payment = $order->payments()->latest()->first();
        if (! $payment) {
            throw new \RuntimeException('لا يوجد سجل دفع للطلب.');
        }

        $method = $payment->paymentMethod;
        if (! $method) {
            throw new \RuntimeException('وسيلة الدفع غير معرّفة.');
        }

        return $this->gatewayFor($method)->initiate($payment, $order, $method, $context);
    }

    public function confirmManual(Payment $payment): void
    {
        $this->syncService->markCompleted($payment);
    }

    public function rejectManual(Payment $payment, ?string $reason = null): void
    {
        $this->syncService->markCancelled($payment, $reason);
    }

    public function refund(Payment $payment, ?float $amount = null, ?string $reason = null): void
    {
        $amount = $amount ?? (float) $payment->amount;
        $method = $payment->paymentMethod;
        if (! $method) {
            throw new \RuntimeException('وسيلة الدفع غير معرّفة.');
        }

        $refund = $this->gatewayFor($method)->refund($payment, $amount, $reason);

        if ($refund->status === 'completed' || $method->isManual()) {
            $this->syncService->markRefunded($payment);
        }
    }
}
