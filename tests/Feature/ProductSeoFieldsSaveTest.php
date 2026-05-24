<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

test('product update persists seo meta fields', function () {
    $user = User::factory()->create(['is_active' => true]);
    $category = Category::create(['name' => 'تصنيف', 'slug' => 'cat-seo-test']);
    $product = Product::create([
        'name' => 'منتج SEO',
        'slug' => 'product-seo-test',
        'category_id' => $category->id,
        'price' => 10,
        'status' => 'draft',
        'meta_title' => 'عنوان قديم',
        'meta_description' => 'وصف قديم',
        'meta_keywords' => 'قديم',
    ]);

    $this->actingAs($user)->put(route('admin.products.update', $product), [
        'name' => $product->name,
        'slug' => $product->slug,
        'category_id' => $category->id,
        'price' => 10,
        'status' => 'draft',
        'meta_title' => 'Windows 11 Pro مفتاح تفعيل أصلي وسريع',
        'meta_description' => str_repeat('وصف SEO محدّث. ', 20),
        'meta_keywords' => 'ويندوز, تفعيل, مفتاح',
    ])->assertRedirect(route('admin.products.edit', $product));

    $product->refresh();

    expect($product->meta_title)->toBe('Windows 11 Pro مفتاح تفعيل أصلي وسريع')
        ->and($product->meta_keywords)->toBe('ويندوز, تفعيل, مفتاح');
});
