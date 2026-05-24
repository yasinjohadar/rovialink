<?php

namespace App\Contracts;

use App\DataTransferObjects\PaymentInitiationResult;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentRefund;

interface PaymentGatewayInterface
{
    public function driver(): string;

    public function initiate(Payment $payment, Order $order, PaymentMethod $method, array $context = []): PaymentInitiationResult;

    public function refund(Payment $payment, float $amount, ?string $reason = null): PaymentRefund;

    public function supportsManualConfirmation(): bool;
}
