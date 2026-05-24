<?php

if (! function_exists('site_currency_code')) {
    function site_currency_code(): string
    {
        return 'USD';
    }
}

if (! function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        return '$';
    }
}

if (! function_exists('format_money')) {
    function format_money(float|int|string|null $amount, int $decimals = 2): string
    {
        return number_format((float) $amount, $decimals).' '.currency_symbol();
    }
}
