<?php

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;

test('checkout with cod creates pending payment and order', function () {
    $user = User::factory()->create();
    $product = Product::create([
        'name' => 'Test Product',
        'slug' => 'test-product',
        'price' => 99.00,
        'status' => 'active',
        'stock_quantity' => 10,
        'is_visible' => true,
    ]);
    $cod = PaymentMethod::create([
        'name' => 'COD Test',
        'slug' => 'cod-test',
        'driver' => 'cod',
        'is_active' => true,
        'order' => 1,
        'config' => ['instructions' => 'test'],
    ]);

    $this->actingAs($user);
    app(CartService::class)->add($product->id, 1);

    $response = $this->post(route('frontend.checkout.store'), [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => $user->email,
        'phone' => '0500000000',
        'city' => 'Riyadh',
        'payment_method_id' => $cod->id,
        'address' => 'digital',
        'country' => 'SA',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
    $this->assertDatabaseHas('payments', [
        'payment_method_id' => $cod->id,
        'status' => 'pending',
    ]);
});
