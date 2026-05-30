<?php

namespace App\Http\Controllers;

use App\Services\Storage\StorageHelperService;

class PublicMediaController extends Controller
{
    private const ALLOWED_PREFIXES = [
        'blog/',
        'products/',
        'categories/',
        'brands/',
        'site/',
        'hero/',
        'users/',
    ];

    public function __invoke(string $path, StorageHelperService $storageHelper)
    {
        $path = ltrim(str_replace(['..', '\\'], ['', '/'], $path), '/');

        if ($path === '' || ! $this->isAllowedPath($path)) {
            abort(404);
        }

        if (! $storageHelper->mediaExists($storageHelper->mediaDisk(), $path)) {
            abort(404);
        }

        return $storageHelper->mediaResponse($path);
    }

    private function isAllowedPath(string $path): bool
    {
        foreach (self::ALLOWED_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
