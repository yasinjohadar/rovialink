<?php

namespace App\Services;

use App\Models\User;
use App\Services\Storage\StorageHelperService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UserPhotoService
{
    public function __construct(
        protected StorageHelperService $storageHelper
    ) {}

    public function disk(): string
    {
        return $this->storageHelper->mediaDisk();
    }

    public function url(?string $path): ?string
    {
        return user_photo_url($path);
    }

    /**
     * Store profile photo on cloud with local fallback; replaces existing photo when provided.
     */
    public function store(UploadedFile $file, ?User $user = null): ?string
    {
        if ($user?->photo) {
            $this->delete($user->photo);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = Str::uuid()->toString() . '.' . $extension;

        $path = $this->storageHelper->storeUploadedFileWithFailover(
            $this->disk(),
            'users/photos',
            $file,
            'image',
            $filename
        );

        return is_string($path) && $path !== '' ? $path : null;
    }

    /**
     * Remove photo from all mapped storages (cloud + local fallback).
     */
    public function delete(?string $path): bool
    {
        if ($path === null || $path === '') {
            return false;
        }

        return $this->storageHelper->deleteMedia($this->disk(), $path);
    }
}
