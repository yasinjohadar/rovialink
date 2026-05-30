<?php

if (!function_exists('storage_url')) {
    /**
     * Get the URL for a file stored in storage/app/public
     * Works correctly with/without /public in URL
     * 
     * @param string $path The file path relative to storage/app/public
     * @return string The full URL to the file
     */
    function storage_url($path)
    {
        // Remove 'storage/' prefix if exists (already handled by symbolic link)
        $cleanPath = ltrim($path, '/');
        $cleanPath = str_replace('storage/', '', $cleanPath);
        
        // Use asset() which works correctly with symbolic links
        return asset('storage/' . $cleanPath);
    }
}

if (!function_exists('blog_image_url')) {
    /**
     * Get the URL for a blog post featured image
     * Tries multiple methods to ensure the image is accessible
     * 
     * @param string|null $imagePath The image path from database
     * @return string The full URL to the image
     */
    function blog_image_url($imagePath, $seed = null)
    {
        if (empty($imagePath)) {
            return asset('frontend/assets/images/placeholder.jpg');
        }

        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return $imagePath;
        }

        $url = media_url(ltrim($imagePath, '/'));
        if (! empty($url)) {
            return $url;
        }

        return asset('frontend/assets/images/placeholder.jpg');
    }
}

if (!function_exists('course_image_url')) {
    /**
     * Get the URL for a course image
     * Tries multiple methods to ensure the image is accessible
     * 
     * @param string|null $imagePath The image path from database
     * @return string The full URL to the image
     */
    function course_image_url($imagePath)
    {
        if (empty($imagePath)) {
            return asset('frontend/assets/img/default-course.jpg');
        }

        // Clean the path
        $imagePath = ltrim($imagePath, '/');
        $filename = basename($imagePath);
        
        // Method 1: Try StorageHelperService (dynamic storage) - FIRST
        try {
            $storageHelper = app(\App\Services\Storage\StorageHelperService::class);
            $url = $storageHelper->getFileUrl('public', $imagePath);
            if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
        } catch (\Exception $e) {
            // Continue to next method
        }
        
        // Method 2: Try route (local storage fallback) - SECOND
        try {
            if (strpos($imagePath, 'courses/images/') !== false) {
                return route('course.image', ['filename' => $filename]);
            }
            if (strpos($imagePath, 'courses/covers/') !== false) {
                return route('course.cover', ['filename' => $filename]);
            }
            if (strpos($imagePath, 'courses/thumbnails/') !== false) {
                return route('course.thumbnail', ['filename' => $filename]);
            }
        } catch (\Exception $e) {
            // Continue to next method
        }
        
        // Method 3: Fallback to asset (requires storage link) - LAST
        return asset('storage/' . $imagePath);
    }
}

if (!function_exists('course_cover_url')) {
    /**
     * Get the URL for a course cover image
     *
     * @param string|null $imagePath The cover path from database (e.g. courses/covers/xxx.jpg)
     * @return string The full URL to the image
     */
    function course_cover_url($imagePath)
    {
        if (empty($imagePath)) {
            return asset('frontend/assets/img/default-course.jpg');
        }
        $imagePath = ltrim($imagePath, '/');
        $filename = basename($imagePath);
        try {
            $storageHelper = app(\App\Services\Storage\StorageHelperService::class);
            $url = $storageHelper->getFileUrl('public', $imagePath);
            if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
        } catch (\Exception $e) {
            // continue
        }
        try {
            if (strpos($imagePath, 'courses/covers/') !== false) {
                return route('course.cover', ['filename' => $filename]);
            }
        } catch (\Exception $e) {
            // continue
        }
        return asset('storage/' . $imagePath);
    }
}

if (!function_exists('media_serve_url')) {
    function media_serve_url(string $path): string
    {
        return app(\App\Services\Storage\StorageHelperService::class)->mediaServeUrl($path);
    }
}

if (!function_exists('media_url')) {
    /**
     * Resolve URL for catalog media (products, categories, hero, site settings files).
     *
     * @param string|null $path Relative path or absolute URL
     */
    function media_url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        try {
            $storageHelper = app(\App\Services\Storage\StorageHelperService::class);
            $disk = $storageHelper->mediaDisk();
            $cleanPath = ltrim($path, '/');
            $url = $storageHelper->resolveMediaUrl($disk, $cleanPath);

            if (! empty($url) && ! $storageHelper->isPublicStorageUrl($url)) {
                return $url;
            }

            if ($storageHelper->mediaExists($disk, $cleanPath)) {
                return $storageHelper->mediaServeUrl($cleanPath);
            }

            return ! empty($url) ? $storageHelper->mediaServeUrl($cleanPath) : null;
        } catch (\Throwable $e) {
            $clean = ltrim($path, '/');

            return $clean !== '' ? media_serve_url($clean) : null;
        }
    }
}

if (!function_exists('product_image_url')) {
    /**
     * Get the URL for a product image with fallback to default image
     * 
     * @param string|null $imagePath The image path from database
     * @param int|null $seed Seed for generating varied default images
     * @return string The full URL to the image
     */
    function product_image_url($imagePath, $seed = null)
    {
        if (empty($imagePath)) {
            $seed = $seed ?? rand(1, 100);
            return "https://picsum.photos/seed/product{$seed}/400/450";
        }

        $url = media_url($imagePath);
        if (! empty($url)) {
            return $url;
        }

        $seed = $seed ?? rand(1, 100);

        return "https://picsum.photos/seed/product{$seed}/400/450";
    }
}

if (!function_exists('brand_image_url')) {
    /**
     * @param  string|null  $imagePath
     */
    function brand_image_url($imagePath): ?string
    {
        if (empty($imagePath)) {
            return null;
        }

        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return $imagePath;
        }

        return media_url($imagePath);
    }
}

if (!function_exists('user_photo_url')) {
    /**
     * Resolve URL for a user profile photo path.
     */
    function user_photo_url(?string $path): ?string
    {
        return media_url($path);
    }
}

if (!function_exists('category_image_url')) {
    /**
     * Get the URL for a category image with fallback to default image
     * 
     * @param string|null $imagePath The image path from database
     * @param int|null $seed Seed for generating varied default images
     * @return string The full URL to the image
     */
    function category_image_url($imagePath, $seed = null)
    {
        if (empty($imagePath)) {
            $seed = $seed ?? rand(1, 50);
            return "https://picsum.photos/seed/cat{$seed}/300/300";
        }

        $url = media_url($imagePath);
        if (! empty($url)) {
            return $url;
        }

        $seed = $seed ?? rand(1, 50);

        return "https://picsum.photos/seed/cat{$seed}/300/300";
    }
}

if (!function_exists('storage_disk_url')) {
    /**
     * Get the URL for a file stored in a specific disk (dynamic storage)
     * 
     * @param string $disk The disk name (e.g., 'public', 'images')
     * @param string $path The file path
     * @return string The full URL to the file
     */
    function storage_disk_url(string $disk, string $path): string
    {
        try {
            $storageHelper = app(\App\Services\Storage\StorageHelperService::class);
            $url = $storageHelper->getFileUrl($disk, $path);
            if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
        } catch (\Exception $e) {
            // Fallback to default storage URL
        }
        
        // Fallback to asset if dynamic storage fails
        return asset('storage/' . ltrim($path, '/'));
    }
}
