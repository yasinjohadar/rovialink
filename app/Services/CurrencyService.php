<?php

namespace App\Services;

use App\Models\Currency;

class CurrencyService
{
    /**
     * Convert amount from default currency to target currency for display.
     * Prices are stored in default currency. rate_to_default = how many default units per 1 unit of this currency.
     * So: amount_in_default / rate_to_default = amount in target currency.
     */
    public function toDisplay(float $amountInDefault, ?string $targetCurrencyCode = null): float
    {
        if ($targetCurrencyCode === null) {
            $currency = $this->getDisplayCurrency();
        } else {
            $currency = Currency::where('code', $targetCurrencyCode)->where('is_active', true)->first();
        }

        if (!$currency || (float) $currency->rate_to_default <= 0) {
            return $amountInDefault;
        }

        return round($amountInDefault / (float) $currency->rate_to_default, 2);
    }

    /**
     * Get symbol for display currency (or default).
     */
    public function getDisplaySymbol(): string
    {
        $currency = $this->getDisplayCurrency();
        return $currency ? ($currency->symbol ?: $currency->code) : 'ر.س';
    }

    /**
     * Get code for display currency.
     */
    public function getDisplayCode(): string
    {
        $currency = $this->getDisplayCurrency();
        return $currency ? $currency->code : 'SAR';
    }

    /**
     * Get the currency currently selected for display (from session or default).
     */
    public function getDisplayCurrency(): ?Currency
    {
        $code = session('admin_display_currency');
        if ($code) {
            $currency = Currency::where('code', $code)->where('is_active', true)->first();
            if ($currency) {
                return $currency;
            }
        }
        return Currency::default()->active()->first();
    }

    /**
     * Format amount in default currency for display (using session currency).
     */
    public function format(float $amountInDefault, ?string $targetCurrencyCode = null): string
    {
        $display = $this->toDisplay($amountInDefault, $targetCurrencyCode);
        $symbol = $targetCurrencyCode
            ? (Currency::where('code', $targetCurrencyCode)->first()->symbol ?? $targetCurrencyCode)
            : $this->getDisplaySymbol();
        return number_format($display, 2) . ' ' . $symbol;
    }

    /**
     * Convert from a non-default currency to default (e.g. for storing).
     * amount_in_other * rate_to_default = amount in default.
     */
    public function toDefault(float $amount, string $fromCurrencyCode): float
    {
        $currency = Currency::where('code', $fromCurrencyCode)->first();
        if (!$currency || (float) $currency->rate_to_default <= 0) {
            return $amount;
        }
        return round($amount * (float) $currency->rate_to_default, 2);
    }
}
