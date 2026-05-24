<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

test('bulk delete soft deletes selected products', function () {
    $user = User::factory()->create(['is_active' => true]);
    $category = Category::create(['name' => 'تصنيف', 'slug' => 'cat-bulk-del']);

    $p1 = Product::create([
        'name' => 'منتج 1',
        'slug' => 'bulk-del-1',
        'category_id' => $category->id,
        'price' => 10,
        'status' => 'active',
    ]);
    $p2 = Product::create([
        'name' => 'منتج 2',
        'slug' => 'bulk-del-2',
        'category_id' => $category->id,
        'price' => 20,
        'status' => 'active',
    ]);

    $this->actingAs($user)->post(route('admin.products.bulk-update'), [
        'ids' => [$p1->id, $p2->id],
        'action' => 'delete',
    ])->assertRedirect(route('admin.products.index'));

    expect(Product::find($p1->id))->toBeNull()
        ->and(Product::find($p2->id))->toBeNull()
        ->and(Product::withTrashed()->find($p1->id))->not->toBeNull()
        ->and(Product::withTrashed()->find($p2->id))->not->toBeNull();
});
