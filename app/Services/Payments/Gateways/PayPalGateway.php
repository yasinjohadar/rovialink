<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\DataTransferObjects\PaymentInitiationResult;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentRefund;
use App\Services\Payments\PaymentSettingsService;
use Illuminate\Support\Facades\Http;

class PayPalGateway implements PaymentGatewayInterface
{
    public function __construct(
        protected PaymentSettingsService $paymentSettings
    ) {}
    public function driver(): string
    {
        return 'paypal';
    }

    public function initiate(Payment $payment, Order $order, PaymentMethod $method, array $context = []): PaymentInitiationResult
    {
        [$clientId, $clientSecret, $baseUrl] = $this->credentials($method);
        $token = $this->accessToken($clientId, $clientSecret, $baseUrl);

        $currency = strtoupper($payment->currency ?: $this->paymentSettings->defaultCurrency());
        $response = Http::withToken($token)
            ->post("{$baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => (string) $order->id,
                    'custom_id' => (string) $payment->id,
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => number_format((float) $payment->amount, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'return_url' => route('frontend.checkout.paypal.return', $order),
                    'cancel_url' => route('frontend.checkout.cancel', $order),
                    'brand_name' => site_brand_name(),
                    'user_action' => 'PAY_NOW',
                ],
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('PayPal order creation failed: ' . $response->body());
        }

        $paypalOrder = $response->json();
        $approveUrl = collect($paypalOrder['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;

        if (! $approveUrl) {
            throw new \RuntimeException('PayPal approve URL missing.');
        }

        $payment->update([
            'transaction_id' => $paypalOrder['id'],
            'metadata' => array_merge($payment->metadata ?? [], [
                'paypal_order_id' => $paypalOrder['id'],
            ]),
        ]);

        return new PaymentInitiationResult(redirectUrl: $approveUrl);
    }

    public function captureOrder(Payment $payment, string $paypalOrderId): array
    {
        $method = $payment->paymentMethod;
        [$clientId, $clientSecret, $baseUrl] = $this->credentials($method);
        $token = $this->accessToken($clientId, $clientSecret, $baseUrl);

        $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$baseUrl}/v2/checkout/orders/{$paypalOrderId}/capture");

        if (! $response->successful()) {
            throw new \RuntimeException('PayPal capture failed: ' . $response->body());
        }

        return $response->json();
    }

    public function refund(Payment $payment, float $amount, ?string $reason = null): PaymentRefund
    {
        $captureId = $payment->metadata['paypal_capture_id'] ?? null;
        if (! $captureId) {
            throw new \RuntimeException('لا يوجد capture id لـ PayPal.');
        }

        $method = $payment->paymentMethod;
        [$clientId, $clientSecret, $baseUrl] = $this->credentials($method);
        $token = $this->accessToken($clientId, $clientSecret, $baseUrl);

        $response = Http::withToken($token)
            ->post("{$baseUrl}/v2/payments/captures/{$captureId}/refund", [
                'amount' => [
                    'value' => number_format($amount, 2, '.', ''),
                    'currency_code' => strtoupper($payment->currency),
                ],
                'note_to_payer' => $reason,
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('PayPal refund failed: ' . $response->body());
        }

        $data = $response->json();

        return PaymentRefund::create([
            'payment_id' => $payment->id,
            'amount' => $amount,
            'currency' => $payment->currency,
            'gateway_refund_id' => $data['id'] ?? null,
            'status' => ($data['status'] ?? '') === 'COMPLETED' ? 'completed' : 'pending',
            'reason' => $reason,
            'processed_by' => auth()->id(),
            'metadata' => ['paypal' => $data],
        ]);
    }

    public function supportsManualConfirmation(): bool
    {
        return false;
    }

    protected function credentials(?PaymentMethod $method): array
    {
        $config = $method?->config ?? [];
        $sandbox = array_key_exists('sandbox', $config)
            ? (bool) $config['sandbox']
            : $this->paymentSettings->paypalIsSandbox();

        $clientId = $config['client_id'] ?? $this->paymentSettings->paypalClientId();
        $clientSecret = $config['client_secret'] ?? $this->paymentSettings->paypalClientSecret();
        $baseUrl = $sandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

        if (! $clientId || ! $clientSecret) {
            throw new \RuntimeException('PayPal credentials غير مضبوطة.');
        }

        return [$clientId, $clientSecret, $baseUrl];
    }

    protected function accessToken(string $clientId, string $clientSecret, string $baseUrl): string
    {
        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

        if (! $response->successful()) {
            throw new \RuntimeException('PayPal auth failed.');
        }

        return $response->json('access_token');
    }
}
