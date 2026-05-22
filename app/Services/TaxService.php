<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\TaxRate;

class TaxService
{
    /**
     * احسب الضرائب على السلة كاملة بناءً على فئات الضرائب للمنتجات وعنوان الشحن.
     */
    public function calculateForCart(ShoppingCart $cart, array $shippingAddress): array
    {
        $country = $shippingAddress['country'] ?? null;
        $state = $shippingAddress['state'] ?? null;
        $city = $shippingAddress['city'] ?? null;
        $postal = $shippingAddress['postal_code'] ?? null;

        if (!$country) {
            return [
                'items' => [],
                'tax_amount' => 0,
            ];
        }

        $taxAmount = 0.0;
        $itemTaxes = [];

        foreach ($cart->items as $item) {
            $product = $item->product;
            if (!$product || !$product->tax_class_id) {
                continue;
            }

            $rate = $this->matchRateForProduct($product, $country, $state, $city, $postal);
            if (!$rate) {
                continue;
            }

            $lineSubtotal = $item->quantity * ($item->unit_price ?? $product->effective_price);
            $lineTax = $lineSubtotal * (float) $rate->rate;

            $taxAmount += $lineTax;
            $itemTaxes[$item->id] = [
                'rate' => (float) $rate->rate,
                'amount' => $lineTax,
            ];
        }

        return [
            'items' => $itemTaxes,
            'tax_amount' => $taxAmount,
        ];
    }

    protected function matchRateForProduct(Product $product, string $country, ?string $state, ?string $city, ?string $postal): ?TaxRate
    {
        $rates = TaxRate::where('tax_class_id', $product->tax_class_id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        foreach ($rates as $rate) {
            if ($rate->country_code && strtoupper($rate->country_code) !== strtoupper($country)) {
                continue;
            }
            if ($rate->state && $state && strcasecmp($rate->state, $state) !== 0) {
                continue;
            }
            if ($rate->city && $city && strcasecmp($rate->city, $city) !== 0) {
                continue;
            }
            if ($rate->postal_code_pattern && $postal) {
                $pattern = '#^' . str_replace(['*', '%'], '.*', preg_quote($rate->postal_code_pattern, '#')) . '$#i';
                if (!preg_match($pattern, $postal)) {
                    continue;
                }
            }

            return $rate;
        }

        return null;
    }
}

