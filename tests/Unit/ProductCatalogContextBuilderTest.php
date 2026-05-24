<?php

use App\Models\Category;
use App\Models\Product;
use App\Services\Store\ProductCatalogContextBuilder;

test('search returns products matching query terms', function () {
    $category = Category::create(['name' => 'برمجيات', 'slug' => 'software-chat-test']);

    Product::create([
        'name' => 'Windows 11 Pro Key',
        'slug' => 'win11-chat-test',
        'category_id' => $category->id,
        'price' => 29.99,
        'status' => 'active',
        'is_visible' => true,
        'short_description' => 'مفتاح تفعيل ويندوز',
    ]);

    Product::create([
        'name' => 'قالب غير ذي صلة',
        'slug' => 'other-chat-test',
        'category_id' => $category->id,
        'price' => 9.99,
        'status' => 'active',
        'is_visible' => true,
    ]);

    $builder = new ProductCatalogContextBuilder;
    $results = $builder->searchProducts('ويندوز 11', 5);

    expect(collect($results)->pluck('slug'))->toContain('win11-chat-test');
});

test('build includes current product slug first', function () {
    $category = Category::create(['name' => 'cat2', 'slug' => 'cat2-chat']);

    Product::create([
        'name' => 'Current Product',
        'slug' => 'current-product-chat',
        'category_id' => $category->id,
        'price' => 10,
        'status' => 'active',
        'is_visible' => true,
    ]);

    $builder = new ProductCatalogContextBuilder;
    $built = $builder->build('سعر', 'current-product-chat', 5);

    expect($built['products'][0]['slug'] ?? null)->toBe('current-product-chat');
});
