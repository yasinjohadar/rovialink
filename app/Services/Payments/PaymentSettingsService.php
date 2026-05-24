<?php

namespace App\Services\Payments;

use App\Models\SystemSetting;
use Exception;
use Illuminate\Support\Facades\Crypt;

class PaymentSettingsService
{
    public const GROUP = 'payments';

    /** @var list<string> */
    protected array $encryptedKeys = [
        'stripe_secret_key',
        'stripe_webhook_secret',
        'paypal_client_secret',
    ];

    public function getSettings(): array
    {
        $raw = $this->rawSettings();

        return [
            'payment_default_currency' => 'USD',
            'stripe_publishable_key' => $raw['stripe_publishable_key'] ?? '',
            'stripe_secret_key' => $this->decryptIfEncrypted($raw['stripe_secret_key'] ?? ''),
            'stripe_webhook_secret' => $this->decryptIfEncrypted($raw['stripe_webhook_secret'] ?? ''),
            'paypal_client_id' => $raw['paypal_client_id'] ?? '',
            'paypal_client_secret' => $this->decryptIfEncrypted($raw['paypal_client_secret'] ?? ''),
            'paypal_webhook_id' => $raw['paypal_webhook_id'] ?? '',
            'paypal_mode' => $raw['paypal_mode'] ?? 'sandbox',
        ];
    }

  /**
     * Settings for admin form (secrets masked).
     */
    public function getSettingsForForm(): array
    {
        $settings = $this->getSettings();

        return array_merge($settings, [
            'stripe_secret_key' => '',
            'stripe_webhook_secret' => '',
            'paypal_client_secret' => '',
            'stripe_secret_configured' => $this->isConfigured('stripe_secret_key'),
            'stripe_webhook_configured' => $this->isConfigured('stripe_webhook_secret'),
            'paypal_secret_configured' => $this->isConfigured('paypal_client_secret'),
        ]);
    }

    public function updateSettings(array $input): void
    {
        $raw = $this->rawSettings();

        foreach ($this->defaultKeys() as $key => $default) {
            if (! array_key_exists($key, $input)) {
                continue;
            }

            $value = $input[$key];

            if (in_array($key, $this->encryptedKeys, true)) {
                if ($value === null || $value === '') {
                    $value = $raw[$key] ?? '';
                } else {
                    $value = Crypt::encryptString((string) $value);
                }
            } elseif ($key === 'payment_default_currency') {
                $value = 'USD';
            }

            SystemSetting::set($key, (string) $value, 'string', self::GROUP);
        }
    }

    public function initializeDefaults(): void
    {
        foreach ($this->defaultKeys() as $key => $default) {
            if (! SystemSetting::byKey($key)->ofGroup(self::GROUP)->exists()) {
                SystemSetting::set($key, (string) $default, 'string', self::GROUP);
            }
        }
    }

    public function defaultCurrency(): string
    {
        return $this->getSettings()['payment_default_currency'];
    }

    public function stripePublishableKey(): string
    {
        return $this->getSettings()['stripe_publishable_key'];
    }

    public function stripeSecret(): string
    {
        return $this->getSettings()['stripe_secret_key'];
    }

    public function stripeWebhookSecret(): string
    {
        return $this->getSettings()['stripe_webhook_secret'];
    }

    public function paypalClientId(): string
    {
        return $this->getSettings()['paypal_client_id'];
    }

    public function paypalClientSecret(): string
    {
        return $this->getSettings()['paypal_client_secret'];
    }

    public function paypalWebhookId(): string
    {
        return $this->getSettings()['paypal_webhook_id'];
    }

    public function paypalIsSandbox(): bool
    {
        return ($this->getSettings()['paypal_mode'] ?? 'sandbox') !== 'live';
    }

    protected function defaultKeys(): array
    {
        return [
            'payment_default_currency' => 'USD',
            'stripe_publishable_key' => '',
            'stripe_secret_key' => '',
            'stripe_webhook_secret' => '',
            'paypal_client_id' => '',
            'paypal_client_secret' => '',
            'paypal_webhook_id' => '',
            'paypal_mode' => 'sandbox',
        ];
    }

    protected function rawSettings(): array
    {
        return SystemSetting::where('group', self::GROUP)
            ->get()
            ->pluck('value', 'key')
            ->toArray();
    }

    protected function isConfigured(string $key): bool
    {
        $value = $this->rawSettings()[$key] ?? '';

        return $value !== '';
    }

    protected function decryptIfEncrypted(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        try {
            return Crypt::decryptString($value);
        } catch (Exception) {
            return $value;
        }
    }
}
