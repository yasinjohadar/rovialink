<?php

namespace App\Services\Blog;

use App\Models\BlogPost;
use App\Services\Storage\StorageHelperService;
use Illuminate\Http\UploadedFile;

class BlogPostMediaService
{
    public function __construct(
        protected StorageHelperService $storageHelper
    ) {}

    public function directoryFor(BlogPost $post): string
    {
        return 'blog/'.$post->id;
    }

    public function storeFeaturedImage(BlogPost $post, UploadedFile $file): ?string
    {
        $path = $this->storageHelper->storeUploadedFileWithFailover(
            $this->storageHelper->mediaDisk(),
            $this->directoryFor($post),
            $file,
            'image'
        );

        return $path ?: null;
    }

    public function deleteFeaturedImage(?string $path): void
    {
        if (! $path) {
            return;
        }

        $this->storageHelper->deleteMedia($this->storageHelper->mediaDisk(), $path);
    }

    public function replaceFeaturedImage(BlogPost $post, UploadedFile $file): ?string
    {
        $this->deleteFeaturedImage($post->featured_image);

        return $this->storeFeaturedImage($post, $file);
    }
}
