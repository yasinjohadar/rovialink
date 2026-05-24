<?php

namespace App\Support;

class CheckoutPhoneCountries
{
    /**
     * @return array<int, array{iso2: string, name_ar: string, dial_code: string}>
     */
    public static function all(): array
    {
        return config('checkout_phone_countries', []);
    }

    /**
     * @return list<string>
     */
    public static function iso2List(): array
    {
        return array_values(array_map(
            fn (array $row) => strtoupper($row['iso2']),
            static::all()
        ));
    }

    public static function defaultIso2(): string
    {
        return 'SA';
    }

    /**
     * Parse E.164 phone to guess country ISO2 for intl-tel-input initial country.
     */
    public static function guessIso2FromPhone(?string $phone): ?string
    {
        if ($phone === null || $phone === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if ($digits === '') {
            return null;
        }

        $byDialLength = collect(static::all())
            ->sortByDesc(fn (array $row) => strlen($row['dial_code']))
            ->values();

        foreach ($byDialLength as $row) {
            $code = $row['dial_code'];
            if (str_starts_with($digits, $code)) {
                return strtoupper($row['iso2']);
            }
        }

        return null;
    }
}
