<?php

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use App\Services\Storage\StorageHelperService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('blog featured image is stored under blog post id directory like products', function () {
    Storage::fake('public');

    $user = User::factory()->create(['is_active' => true]);
    $category = BlogCategory::create([
        'name' => 'تصنيف',
        'slug' => 'blog-cat-cloud',
        'is_active' => true,
    ]);

    $this->actingAs($user)->post(route('admin.blog.posts.store'), [
        'title' => 'مقال سحابة',
        'slug' => 'cloud-blog-post',
        'content' => 'محتوى',
        'category_id' => $category->id,
        'status' => 'draft',
        'featured_image' => UploadedFile::fake()->image('featured.jpg'),
    ])->assertRedirect();

    $post = BlogPost::where('slug', 'cloud-blog-post')->firstOrFail();

    expect($post->featured_image)->toStartWith('blog/'.$post->id.'/')
        ->and(blog_image_url($post->featured_image))->toContain('/media/blog/'.$post->id.'/');
});

test('blog media service uses same media disk as products', function () {
    $blogMedia = app(\App\Services\Blog\BlogPostMediaService::class);
    $storageHelper = app(StorageHelperService::class);

    expect($storageHelper->mediaDisk())->toBe(config('media.disk', 'public'));
});
