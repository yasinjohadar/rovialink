<?php

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('blog image url uses media serve route for local storage paths', function () {
    Storage::fake('public');

    $path = 'blog/images/sample.webp';
    Storage::disk('public')->put($path, 'fake-image');

    $url = blog_image_url($path);

    expect($url)->toContain('/media/blog/images/sample.webp')
        ->and($url)->not->toContain('/storage/');
});

test('blog featured image upload stores with failover and resolves url', function () {
    Storage::fake('public');

    $user = User::factory()->create(['is_active' => true]);
    $category = \App\Models\BlogCategory::create([
        'name' => 'تصنيف',
        'slug' => 'blog-cat-media',
        'is_active' => true,
    ]);

    $this->actingAs($user)->post(route('admin.blog.posts.store'), [
        'title' => 'مقال جديد',
        'slug' => 'new-blog-media-post',
        'content' => 'محتوى المقال',
        'category_id' => $category->id,
        'status' => 'draft',
        'featured_image' => UploadedFile::fake()->image('featured.jpg'),
    ])->assertRedirect();

    $post = BlogPost::where('slug', 'new-blog-media-post')->first();

    expect($post)->not->toBeNull()
        ->and($post->featured_image)->toStartWith('blog/'.$post->id.'/')
        ->and(blog_image_url($post->featured_image))->toContain('/media/blog/'.$post->id.'/');
});

test('public media route serves blog images from storage', function () {
    Storage::fake('public');

    $path = 'blog/images/route-test.webp';
    Storage::disk('public')->put($path, 'binary');

    $this->get(url('media/'.$path))
        ->assertOk();
});
