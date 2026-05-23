<?php

namespace App\Support;

use App\Models\AppStorageConfig;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;

class MediaUrlBuilder
{
    /** @var list<string> */
    private const S3_COMPATIBLE_DRIVERS = [
        's3',
        'digitalocean',
        'wasabi',
        'backblaze',
        'cloudflare_r2',
    ];

    public static function build(AppStorageConfig $config, Filesystem $filesystem, string $path): string
    {
        $config = $config->fresh();
        $driver = (string) ($config->driver ?? '');

        if (in_array($driver, self::S3_COMPATIBLE_DRIVERS, true)) {
            return self::s3CompatibleUrl($filesystem, $path);
        }

        if ($driver === 'bunny') {
            return self::bunnyUrl($config, $path);
        }

        if (! empty($config->cdn_url) && ! self::isRawObjectStoreEndpoint($config->cdn_url)) {
            return rtrim($config->cdn_url, '/') . '/' . ltrim($path, '/');
        }

        try {
            return $filesystem->url($path);
        } catch (\Throwable $e) {
            Log::debug('MediaUrlBuilder: filesystem url failed', [
                'driver' => $driver,
                'error' => $e->getMessage(),
            ]);

            return '';
        }
    }

    /**
     * Raw S3 API endpoints must not be used as public CDN URLs (iDrive returns HTML).
     */
    public static function isRawObjectStoreEndpoint(?string $url): bool
    {
        if ($url === null || $url === '') {
            return false;
        }

        return (bool) preg_match(
            '/idrivee2\.com|amazonaws\.com|cloudflarestorage\.com|digitaloceanspaces\.com|backblazeb2\.com/i',
            $url
        );
    }

    private static function s3CompatibleUrl(Filesystem $filesystem, string $path): string
    {
        if (config('media.s3_use_signed_urls', true)) {
            try {
                $minutes = (int) config('media.signed_url_ttl_minutes', 10_080);

                return $filesystem->temporaryUrl($path, now()->addMinutes($minutes));
            } catch (\Throwable $e) {
                Log::debug('MediaUrlBuilder: temporaryUrl failed', [
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        try {
            $url = $filesystem->url($path);
            if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
        } catch (\Throwable $e) {
            Log::debug('MediaUrlBuilder: public url failed', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
        }

        return '';
    }

    private static function bunnyUrl(AppStorageConfig $config, string $path): string
    {
        if (! empty($config->cdn_url)) {
            return rtrim($config->cdn_url, '/') . '/' . ltrim($path, '/');
        }

        $decrypted = $config->getDecryptedConfig();
        $pullZone = trim($decrypted['pull_zone'] ?? '');
        if ($pullZone !== '') {
            if (str_starts_with($pullZone, 'http')) {
                return rtrim($pullZone, '/') . '/' . ltrim($path, '/');
            }

            return 'https://' . $pullZone . '.b-cdn.net/' . ltrim($path, '/');
        }

        if (! empty($decrypted['storage_zone'])) {
            return 'https://' . trim($decrypted['storage_zone']) . '.b-cdn.net/' . ltrim($path, '/');
        }

        return '';
    }
}
