<?php

namespace App\Services\Storage;

use App\Models\AppStorageConfig;
use App\Models\StorageDiskMapping;
use App\Support\MediaUrlBuilder;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\File as HttpFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StorageHelperService
{
    protected AppStorageManager $storageManager;

    public function __construct(AppStorageManager $storageManager)
    {
        $this->storageManager = $storageManager;
    }

    /**
     * الحصول على disk
     * 
     * @param string $diskName
     * @return Filesystem
     */
    public function getDisk(string $diskName): Filesystem
    {
        return $this->storageManager->getDisk($diskName);
    }

    /**
     * تخزين ملف
     * 
     * @param string $disk
     * @param string $path
     * @param mixed $content
     * @param string|null $fileType
     * @return bool
     */
    public function storeFile(string $disk, string $path, $content, ?string $fileType = null): bool
    {
        return $this->storageManager->store($disk, $path, $content, $fileType);
    }

    /**
     * تخزين ملف مع Auto-failover
     * 
     * @param string $disk
     * @param string $path
     * @param mixed $content
     * @param string|null $fileType
     * @return bool
     */
    public function storeFileWithFailover(string $disk, string $path, $content, ?string $fileType = null): bool
    {
        try {
            return $this->storageManager->storeWithFailover($disk, $path, $content, $fileType);
        } catch (\Exception $e) {
            Log::error("StorageHelperService: Failed to store file with failover", [
                'disk' => $disk,
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * الحصول على URL للملف
     * 
     * @param string $disk
     * @param string $path
     * @return string
     */
    public function getFileUrl(string $disk, string $path): string
    {
        return $this->storageManager->url($disk, $path);
    }

    /**
     * حذف ملف
     * 
     * @param string $disk
     * @param string $path
     * @return bool
     */
    public function deleteFile(string $disk, string $path): bool
    {
        return $this->storageManager->delete($disk, $path);
    }

    /**
     * التحقق من وجود الملف
     * 
     * @param string $disk
     * @param string $path
     * @return bool
     */
    public function fileExists(string $disk, string $path): bool
    {
        try {
            $storage = $this->getDisk($disk);
            return $storage->exists($path);
        } catch (\Exception $e) {
            Log::error("StorageHelperService: Failed to check file existence", [
                'disk' => $disk,
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * نسخ ملف
     * 
     * @param string $disk
     * @param string $fromPath
     * @param string $toPath
     * @return bool
     */
    public function copyFile(string $disk, string $fromPath, string $toPath): bool
    {
        try {
            $storage = $this->getDisk($disk);
            return $storage->copy($fromPath, $toPath);
        } catch (\Exception $e) {
            Log::error("StorageHelperService: Failed to copy file", [
                'disk' => $disk,
                'from' => $fromPath,
                'to' => $toPath,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * نقل ملف
     * 
     * @param string $disk
     * @param string $fromPath
     * @param string $toPath
     * @return bool
     */
    public function moveFile(string $disk, string $fromPath, string $toPath): bool
    {
        try {
            $storage = $this->getDisk($disk);
            return $storage->move($fromPath, $toPath);
        } catch (\Exception $e) {
            Log::error("StorageHelperService: Failed to move file", [
                'disk' => $disk,
                'from' => $fromPath,
                'to' => $toPath,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * الحصول على محتوى الملف
     * 
     * @param string $disk
     * @param string $path
     * @return string
     */
    public function getFileContent(string $disk, string $path): string
    {
        try {
            return $this->storageManager->retrieve($disk, $path);
        } catch (\Exception $e) {
            Log::error("StorageHelperService: Failed to retrieve file", [
                'disk' => $disk,
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return '';
        }
    }

    /**
     * الحصول على حجم الملف
     * 
     * @param string $disk
     * @param string $path
     * @return int
     */
    public function getFileSize(string $disk, string $path): int
    {
        try {
            $storage = $this->getDisk($disk);
            return $storage->size($path);
        } catch (\Exception $e) {
            Log::error("StorageHelperService: Failed to get file size", [
                'disk' => $disk,
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * الحصول على آخر وقت تعديل
     * 
     * @param string $disk
     * @param string $path
     * @return int|null
     */
    public function getLastModified(string $disk, string $path): ?int
    {
        try {
            $storage = $this->getDisk($disk);
            return $storage->lastModified($path);
        } catch (\Exception $e) {
            Log::error("StorageHelperService: Failed to get last modified", [
                'disk' => $disk,
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * الحصول على جميع الملفات في مجلد
     * 
     * @param string $disk
     * @param string $path
     * @return array
     */
    public function getFiles(string $disk, string $path = ''): array
    {
        try {
            $storage = $this->getDisk($disk);
            return $storage->files($path);
        } catch (\Exception $e) {
            Log::error("StorageHelperService: Failed to get files", [
                'disk' => $disk,
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * الحصول على جميع المجلدات
     * 
     * @param string $disk
     * @param string $path
     * @return array
     */
    public function getDirectories(string $disk, string $path = ''): array
    {
        try {
            $storage = $this->getDisk($disk);
            return $storage->directories($path);
        } catch (\Exception $e) {
            Log::error("StorageHelperService: Failed to get directories", [
                'disk' => $disk,
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Helper method لتخزين ملف من UploadedFile
     * 
     * @param string $disk
     * @param string $path
     * @param \Illuminate\Http\UploadedFile $file
     * @param string|null $fileType
     * @return string|false
     */
    public function storeUploadedFile(string $disk, string $path, $file, ?string $fileType = null)
    {
        try {
            Log::info("StorageHelperService: Starting file upload", [
                'disk' => $disk,
                'path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $fileType,
            ]);

            $storage = $this->getDisk($disk);
            
            // استخدام putFile الذي يحفظ الملف تلقائياً
            $storedPath = $storage->putFile($path, $file);
            
            if ($storedPath) {
                Log::info("StorageHelperService: File uploaded successfully", [
                    'disk' => $disk,
                    'stored_path' => $storedPath,
                    'original_path' => $path,
                ]);

                // تتبع التخزين باستخدام analytics service مباشرة
                try {
                    $mapping = \App\Models\StorageDiskMapping::where('disk_name', $disk)
                        ->where('is_active', true)
                        ->first();
                    
                    if ($mapping && $mapping->primaryStorage) {
                        $fileSize = $file->getSize();
                        $analyticsService = app(\App\Services\Storage\AppStorageAnalyticsService::class);
                        $analyticsService->trackStorageUsage($mapping->primaryStorage, $fileSize, $fileType);
                        $analyticsService->trackBandwidth($mapping->primaryStorage, 'upload', $fileSize, $fileType);
                    }
                } catch (\Exception $trackingException) {
                    // لا نوقف العملية إذا فشل tracking
                    Log::warning("StorageHelperService: Failed to track storage usage", [
                        'error' => $trackingException->getMessage(),
                    ]);
                }
                
                return $storedPath;
            } else {
                Log::error("StorageHelperService: putFile returned false", [
                    'disk' => $disk,
                    'path' => $path,
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("StorageHelperService: Failed to store uploaded file", [
                'disk' => $disk,
                'path' => $path,
                'file_name' => $file->getClientOriginalName() ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Helper method للحصول على disk مباشرة (للاستخدام مع Laravel Storage methods)
     * 
     * @param string $diskName
     * @return Filesystem
     */
    public function disk(string $diskName): Filesystem
    {
        return $this->getDisk($diskName);
    }

    /**
     * Default media disk from config (StorageDiskMapping name).
     */
    public function mediaDisk(): string
    {
        return (string) config('media.disk', 'public');
    }

    /**
     * Store an uploaded file: primary cloud → mapping fallbacks → local public disk.
     *
     * @return string|false Relative path on success
     */
    public function storeUploadedFileWithFailover(
        string $disk,
        string $directory,
        UploadedFile $file,
        ?string $fileType = 'image',
        ?string $filename = null
    ): string|false {
        $directory = trim($directory, '/');
        $storages = $this->resolveStoragesForMedia($disk);

        foreach ($storages as $storageConfig) {
            $storedPath = $this->putUploadedFileOnStorage($storageConfig, $directory, $file, $filename);
            if ($storedPath !== false) {
                $this->trackUploadAnalytics($disk, $storageConfig, $file, $fileType);

                return $storedPath;
            }
        }

        $storedPath = $this->putOnNativePublicDisk($directory, $file, $filename);
        if ($storedPath !== false) {
            Log::info('StorageHelperService: Stored on native public disk (final fallback)', [
                'path' => $storedPath,
            ]);

            return $storedPath;
        }

        Log::error('StorageHelperService: All upload targets failed', [
            'disk' => $disk,
            'directory' => $directory,
            'file' => $file->getClientOriginalName(),
        ]);

        return false;
    }

    /**
     * Delete media from all mapped storages plus native public disk.
     */
    public function deleteMedia(string $disk, string $path): bool
    {
        if ($path === '') {
            return false;
        }

        $deleted = false;
        $storages = $this->resolveStoragesForDisk($disk);

        foreach ($storages as $storageConfig) {
            try {
                $filesystem = AppStorageFactory::create($storageConfig);
                if ($filesystem->exists($path) && $filesystem->delete($path)) {
                    $deleted = true;
                }
            } catch (\Throwable $e) {
                Log::warning('StorageHelperService: deleteMedia failed on storage', [
                    'storage' => $storageConfig->name,
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        try {
            $local = Storage::disk('public');
            if ($local->exists($path) && $local->delete($path)) {
                $deleted = true;
            }
        } catch (\Throwable $e) {
            Log::warning('StorageHelperService: deleteMedia failed on public disk', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
        }

        return $deleted;
    }

    /**
     * Resolve a public URL for media, checking primary then fallbacks then local asset.
     */
    public function resolveMediaUrl(string $disk, ?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        foreach ($this->resolveStoragesForMedia($disk) as $storageConfig) {
            try {
                $filesystem = AppStorageFactory::create($storageConfig);
                if ($filesystem->exists($path)) {
                    $url = $this->urlForStorageConfig($storageConfig, $filesystem, $path);
                    if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL)) {
                        return $url;
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('StorageHelperService: resolveMediaUrl check failed', [
                    'storage' => $storageConfig->name,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        try {
            $local = Storage::disk('public');
            if ($local->exists($path)) {
                $url = $local->url($path);
                if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL)) {
                    return $url;
                }
            }
        } catch (\Throwable $e) {
            Log::debug('StorageHelperService: resolveMediaUrl local check failed', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Check whether media exists on any configured storage (including native public).
     */
    public function mediaExists(string $disk, string $path): bool
    {
        if ($path === '') {
            return false;
        }

        $path = ltrim($path, '/');

        foreach ($this->resolveStoragesForMedia($disk) as $storageConfig) {
            try {
                if (AppStorageFactory::create($storageConfig)->exists($path)) {
                    return true;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        try {
            return Storage::disk('public')->exists($path);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Resolve storages for catalog media (may merge public + images mappings).
     *
     * @return Collection<int, AppStorageConfig>
     */
    protected function resolveStoragesForMedia(string $disk): Collection
    {
        if ($disk !== $this->mediaDisk()) {
            return $this->resolveStoragesForDisk($disk);
        }

        $diskNames = config('media.disks', [$disk]);
        if (! is_array($diskNames) || $diskNames === []) {
            $diskNames = [$disk];
        }

        $storages = collect();
        foreach ($diskNames as $diskName) {
            $storages = $storages->merge($this->resolveStoragesForDisk((string) $diskName));
        }

        return $this->prioritizeMediaStorages($storages);
    }

    /**
     * @return Collection<int, AppStorageConfig>
     */
    protected function resolveStoragesForDisk(string $disk): Collection
    {
        $mapping = StorageDiskMapping::where('disk_name', $disk)
            ->where('is_active', true)
            ->first();

        if (! $mapping) {
            return collect();
        }

        $storages = collect();

        if ($mapping->primaryStorage && $mapping->primaryStorage->is_active) {
            $storages->push($mapping->primaryStorage->fresh());
        }

        foreach ($mapping->getFallbackStorages() as $fallback) {
            if ($fallback->is_active) {
                $storages->push($fallback->fresh());
            }
        }

        return $storages->filter()->unique('id')->values();
    }

    /**
     * Cloud / remote drivers before local when uploading catalog media.
     *
     * @param  Collection<int, AppStorageConfig>  $storages
     * @return Collection<int, AppStorageConfig>
     */
    protected function prioritizeMediaStorages(Collection $storages): Collection
    {
        return $storages
            ->filter()
            ->unique('id')
            ->sortByDesc(function (AppStorageConfig $storage) {
                $cloudBoost = $storage->driver === 'local' ? 0 : 100_000;

                return $cloudBoost + (int) ($storage->priority ?? 0);
            })
            ->values();
    }

    /**
     * @return string|false
     */
    protected function putUploadedFileOnStorage(
        AppStorageConfig $storageConfig,
        string $directory,
        UploadedFile $file,
        ?string $filename
    ): string|false {
        $realPath = $file->getRealPath();
        if (! $realPath || ! is_readable($realPath)) {
            Log::warning('StorageHelperService: Uploaded file is not readable', [
                'storage' => $storageConfig->name,
            ]);

            return false;
        }

        $uploadOptions = ['visibility' => 'public'];
        $fileObject = new HttpFile($realPath);

        try {
            $filesystem = AppStorageFactory::create($storageConfig);
            $storedPath = $filename
                ? $filesystem->putFileAs($directory, $fileObject, $filename, $uploadOptions)
                : $filesystem->putFile($directory, $fileObject, $uploadOptions);

            if ($storedPath && $this->verifyStoredFile($filesystem, $storedPath)) {
                Log::info('StorageHelperService: File stored', [
                    'storage' => $storageConfig->name,
                    'path' => $storedPath,
                ]);

                return $storedPath;
            }

            if ($storedPath) {
                try {
                    $filesystem->delete($storedPath);
                } catch (\Throwable $e) {
                    // ignore cleanup errors
                }
            }
        } catch (\Throwable $e) {
            Log::warning('StorageHelperService: Upload failed on storage', [
                'storage' => $storageConfig->name,
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }

    /**
     * @return string|false
     */
    protected function putOnNativePublicDisk(string $directory, UploadedFile $file, ?string $filename): string|false
    {
        $realPath = $file->getRealPath();
        if (! $realPath || ! is_readable($realPath)) {
            return false;
        }

        try {
            $local = Storage::disk('public');
            $fileObject = new HttpFile($realPath);
            $uploadOptions = ['visibility' => 'public'];
            $storedPath = $filename
                ? $local->putFileAs($directory, $fileObject, $filename, $uploadOptions)
                : $local->putFile($directory, $fileObject, $uploadOptions);

            if ($storedPath && $this->verifyStoredFile($local, $storedPath)) {
                return $storedPath;
            }
        } catch (\Throwable $e) {
            Log::error('StorageHelperService: Native public disk upload failed', [
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }

    protected function verifyStoredFile(Filesystem $filesystem, string $path): bool
    {
        try {
            return $filesystem->exists($path) && $filesystem->size($path) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function urlForStorageConfig(
        AppStorageConfig $config,
        Filesystem $filesystem,
        string $path
    ): string {
        return MediaUrlBuilder::build($config, $filesystem, $path);
    }

    protected function trackUploadAnalytics(
        string $disk,
        AppStorageConfig $storageConfig,
        UploadedFile $file,
        ?string $fileType
    ): void {
        try {
            $analyticsService = app(AppStorageAnalyticsService::class);
            $analyticsService->trackStorageUsage($storageConfig, $file->getSize(), $fileType);
            $analyticsService->trackBandwidth($storageConfig, 'upload', $file->getSize(), $fileType);
        } catch (\Throwable $e) {
            Log::debug('StorageHelperService: Analytics tracking skipped', [
                'disk' => $disk,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
