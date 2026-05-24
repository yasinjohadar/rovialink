<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('replacing primary image does not add old primary to gallery', function () {
    Storage::fake('public');

    $user = User::factory()->create(['is_active' => true]);
    $category = Category::create(['name' => 'تصنيف', 'slug' => 'cat-img-mgmt']);
    $product = Product::create([
        'name' => 'منتج صور',
        'slug' => 'product-img-mgmt',
        'category_id' => $category->id,
        'price' => 10,
        'status' => 'active',
    ]);

    $oldPrimary = $product->images()->create([
        'path' => 'products/'.$product->id.'/old-primary.jpg',
        'order' => 0,
        'is_primary' => true,
    ]);
    $gallery = $product->images()->create([
        'path' => 'products/'.$product->id.'/gallery-1.jpg',
        'order' => 1,
        'is_primary' => false,
    ]);

    $this->actingAs($user)->put(route('admin.products.update', $product), [
        'name' => $product->name,
        'slug' => $product->slug,
        'category_id' => $category->id,
        'price' => 10,
        'status' => 'active',
        'primary_image' => UploadedFile::fake()->image('new-primary.jpg'),
    ])->assertRedirect(route('admin.products.edit', $product));

    expect(ProductImage::find($oldPrimary->id))->toBeNull()
        ->and($product->fresh()->galleryImages)->toHaveCount(1)
        ->and($product->fresh()->galleryImages->first()->id)->toBe($gallery->id)
        ->and($product->fresh()->primaryImage)->not->toBeNull()
        ->and($product->fresh()->primaryImage->is_primary)->toBeTrue();
});

test('gallery image can be deleted via ajax', function () {
    $user = User::factory()->create(['is_active' => true]);
    $category = Category::create(['name' => 'تصنيف', 'slug' => 'cat-img-del']);
    $product = Product::create([
        'name' => 'منتج حذف صورة',
        'slug' => 'product-img-del',
        'category_id' => $category->id,
        'price' => 10,
        'status' => 'active',
    ]);

    $gallery = $product->images()->create([
        'path' => 'products/'.$product->id.'/gallery.jpg',
        'order' => 1,
        'is_primary' => false,
    ]);

    $this->actingAs($user)
        ->postJson(route('admin.products.images.delete', [$product, $gallery]))
        ->assertOk()
        ->assertJson(['success' => true, 'was_primary' => false]);

    expect(ProductImage::find($gallery->id))->toBeNull();
});
