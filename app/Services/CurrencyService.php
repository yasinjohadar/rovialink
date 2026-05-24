<?php

namespace App\Services;

class CurrencyService
{
    public function format(float $amount): string
    {
        return format_money($amount);
    }

    public function getDisplaySymbol(): string
    {
        return currency_symbol();
    }

    public function getDisplayCode(): string
    {
        return site_currency_code();
    }
}
