<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\DataTransferObjects\PaymentInitiationResult;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentRefund;
use App\Services\Payments\PaymentSettingsService;
use Stripe\Checkout\Session;
use Stripe\Refund;
use Stripe\Stripe;

class StripeGateway implements PaymentGatewayInterface
{
    public function __construct(
        protected PaymentSettingsService $paymentSettings
    ) {}
    public function driver(): string
    {
        return 'stripe';
    }

    public function initiate(Payment $payment, Order $order, PaymentMethod $method, array $context = []): PaymentInitiationResult
    {
        $this->configureStripe($method);

        $currency = strtolower($payment->currency ?: $this->paymentSettings->defaultCurrency());
        $lineItems = $order->items->map(function ($item) use ($currency) {
            return [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $item->product_name,
                    ],
                    'unit_amount' => (int) round($item->unit_price * 100),
                ],
                'quantity' => $item->quantity,
            ];
        })->values()->all();

        if ($order->tax_amount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => 'الضريبة'],
                    'unit_amount' => (int) round($order->tax_amount * 100),
                ],
                'quantity' => 1,
            ];
        }

        if ($order->discount_amount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => 'خصم'],
                    'unit_amount' => -(int) round($order->discount_amount * 100),
                ],
                'quantity' => 1,
            ];
        }

        $session = Session::create([
            'mode' => 'payment',
            'client_reference_id' => (string) $order->id,
            'customer_email' => $context['email'] ?? $order->billing_address?->address_line_2,
            'line_items' => $lineItems,
            'success_url' => route('frontend.checkout.success', $order) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('frontend.checkout.cancel', $order),
            'metadata' => [
                'order_id' => (string) $order->id,
                'payment_id' => (string) $payment->id,
            ],
        ]);

        $payment->update([
            'transaction_id' => $session->id,
            'metadata' => array_merge($payment->metadata ?? [], [
                'stripe_session_id' => $session->id,
            ]),
        ]);

        return new PaymentInitiationResult(
            redirectUrl: $session->url,
            metadata: ['stripe_session_id' => $session->id],
        );
    }

    public function refund(Payment $payment, float $amount, ?string $reason = null): PaymentRefund
    {
        $method = $payment->paymentMethod;
        $this->configureStripe($method);

        $paymentIntent = $payment->metadata['stripe_payment_intent'] ?? null;
        if (! $paymentIntent && ! empty($payment->metadata['stripe_session_id'])) {
            $session = Session::retrieve($payment->metadata['stripe_session_id']);
            $paymentIntent = $session->payment_intent;
        }

        if (! $paymentIntent) {
            throw new \RuntimeException('لا يوجد معرف دفع Stripe لهذه العملية.');
        }

        $refund = Refund::create([
            'payment_intent' => $paymentIntent,
            'amount' => (int) round($amount * 100),
            'reason' => 'requested_by_customer',
            'metadata' => ['reason' => $reason ?? ''],
        ]);

        return PaymentRefund::create([
            'payment_id' => $payment->id,
            'amount' => $amount,
            'currency' => $payment->currency,
            'gateway_refund_id' => $refund->id,
            'status' => $refund->status === 'succeeded' ? 'completed' : 'pending',
            'reason' => $reason,
            'processed_by' => auth()->id(),
            'metadata' => ['stripe_refund' => $refund->toArray()],
        ]);
    }

    public function supportsManualConfirmation(): bool
    {
        return false;
    }

    protected function configureStripe(?PaymentMethod $method): void
    {
        $secret = $method?->config['secret_key'] ?? $this->paymentSettings->stripeSecret();
        if (! $secret) {
            throw new \RuntimeException('Stripe secret key غير مضبوط.');
        }
        Stripe::setApiKey($secret);
    }
}
