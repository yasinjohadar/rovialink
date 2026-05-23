<?php

namespace App\Support;

class StorageConfigHelper
{
    public static function toBool(mixed $value, bool $default = false): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (int) $value !== 0;
        }

        if ($value === null || $value === '') {
            return $default;
        }

        $parsed = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($parsed !== null) {
            return $parsed;
        }

        return in_array(strtolower((string) $value), ['1', 'on', 'yes'], true);
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    public static function normalizeS3Flags(array $config): array
    {
        if (array_key_exists('use_path_style', $config)) {
            $config['use_path_style'] = self::toBool($config['use_path_style']);
        }

        return $config;
    }
}
