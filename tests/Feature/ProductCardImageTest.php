<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('product card image can be uploaded and used for card url', function () {
    Storage::fake('public');

    $user = User::factory()->create(['is_active' => true]);
    $category = Category::create(['name' => 'تصنيف', 'slug' => 'cat-card-img']);
    $product = Product::create([
        'name' => 'منتج بطاقة',
        'slug' => 'product-card-img',
        'category_id' => $category->id,
        'price' => 10,
        'status' => 'active',
    ]);

    $product->images()->create([
        'path' => 'products/'.$product->id.'/detail.jpg',
        'order' => 0,
        'is_primary' => true,
    ]);

    $this->actingAs($user)->put(route('admin.products.update', $product), [
        'name' => $product->name,
        'slug' => $product->slug,
        'category_id' => $category->id,
        'price' => 10,
        'status' => 'active',
        'card_image' => UploadedFile::fake()->image('card-promo.jpg', 600, 600),
    ])->assertRedirect(route('admin.products.edit', $product));

    $product->refresh();

    expect($product->card_image)->not->toBeNull()
        ->and(str_contains($product->card_image, '/cards/'))->toBeTrue();
});

test('product card image can be removed', function () {
    Storage::fake('public');

    $user = User::factory()->create(['is_active' => true]);
    $category = Category::create(['name' => 'تصنيف', 'slug' => 'cat-card-rm']);
    $product = Product::create([
        'name' => 'منتج حذف بطاقة',
        'slug' => 'product-card-rm',
        'category_id' => $category->id,
        'price' => 10,
        'status' => 'active',
        'card_image' => 'products/99/cards/card.jpg',
    ]);

    Storage::disk('public')->put('products/99/cards/card.jpg', 'fake');

    $this->actingAs($user)->put(route('admin.products.update', $product), [
        'name' => $product->name,
        'slug' => $product->slug,
        'category_id' => $category->id,
        'price' => 10,
        'status' => 'active',
        'remove_card_image' => '1',
    ])->assertRedirect(route('admin.products.edit', $product));

    expect($product->fresh()->card_image)->toBeNull();
});
