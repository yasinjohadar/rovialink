<?php

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderStatus;
use App\Models\User;

test('customer can view order linked by billing email when user_id is missing', function () {
    $user = User::factory()->create(['email' => 'buyer@example.com']);
    $statusId = OrderStatus::ordered()->value('id');

    $order = Order::create([
        'user_id' => null,
        'order_status_id' => $statusId,
        'subtotal' => 100,
        'total' => 100,
        'currency' => 'SAR',
    ]);

    OrderAddress::create([
        'order_id' => $order->id,
        'type' => 'billing',
        'first_name' => 'Test',
        'last_name' => 'Buyer',
        'phone' => '0500000000',
        'address_line_1' => 'digital',
        'address_line_2' => 'buyer@example.com',
        'city' => 'Riyadh',
        'country' => 'SA',
    ]);

    $this->actingAs($user)
        ->get(route('frontend.account.orders.show', $order))
        ->assertOk();

    expect($order->fresh()->user_id)->toBe($user->id);
});

test('customer cannot view another users order', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $statusId = OrderStatus::ordered()->value('id');

    $order = Order::create([
        'user_id' => $owner->id,
        'order_status_id' => $statusId,
        'subtotal' => 50,
        'total' => 50,
        'currency' => 'SAR',
    ]);

    OrderAddress::create([
        'order_id' => $order->id,
        'type' => 'billing',
        'first_name' => 'Owner',
        'last_name' => 'User',
        'phone' => '0500000001',
        'address_line_1' => 'digital',
        'address_line_2' => 'owner@example.com',
        'city' => 'Riyadh',
        'country' => 'SA',
    ]);

    $this->actingAs($stranger)
        ->get(route('frontend.account.orders.show', $order))
        ->assertRedirect(route('frontend.account'));
});
