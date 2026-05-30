<?php

use App\Models\BlogPost;
use App\Models\User;

test('admin can delete blog post featured image via ajax route', function () {
    $user = User::factory()->create(['is_active' => true]);
    $post = BlogPost::create([
        'title' => 'مقال اختبار',
        'slug' => 'test-post-featured-delete',
        'content' => 'محتوى',
        'status' => 'draft',
        'author_id' => $user->id,
        'featured_image' => 'blog/images/test.webp',
        'featured_image_alt' => 'alt text',
    ]);

    $this->actingAs($user)
        ->deleteJson(route('admin.blog.posts.delete-featured-image', $post))
        ->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'تم حذف الصورة البارزة',
        ]);

    $post->refresh();

    expect($post->featured_image)->toBeNull()
        ->and($post->featured_image_alt)->toBeNull();
});
