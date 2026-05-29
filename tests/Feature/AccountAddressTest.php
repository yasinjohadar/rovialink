<?php

use App\Models\CustomerAddress;
use App\Models\User;

test('user can store billing address with simplified fields', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user)
        ->post(route('frontend.account.addresses.store'), [
            'name' => 'مدير النظام',
            'phone' => '+966501234567',
            'country' => 'SA',
            'address_line_1' => 'شارع الملك فهد',
            'address_line_2' => 'الطابق 3',
            'city' => 'الرياض',
        ])
        ->assertRedirect(route('frontend.account') . '#addresses');

    $address = CustomerAddress::where('user_id', $user->id)->first();

    expect($address)->not->toBeNull()
        ->and($address->type)->toBe('billing')
        ->and($address->phone)->toBe('+966501234567')
        ->and($address->country)->toBe('SA')
        ->and($address->city)->toBe('الرياض')
        ->and($address->is_default)->toBeTrue();
});

test('second address is not auto default', function () {
    $user = User::factory()->create(['is_active' => true]);

    CustomerAddress::create([
        'user_id' => $user->id,
        'type' => 'billing',
        'address_line_1' => 'عنوان قديم',
        'is_default' => true,
    ]);

    $this->actingAs($user)
        ->post(route('frontend.account.addresses.store'), [
            'address_line_1' => 'عنوان جديد',
            'city' => 'جدة',
        ])
        ->assertRedirect(route('frontend.account') . '#addresses');

    $newAddress = CustomerAddress::where('user_id', $user->id)
        ->where('address_line_1', 'عنوان جديد')
        ->first();

    expect($newAddress)->not->toBeNull()
        ->and($newAddress->is_default)->toBeFalse();
});

test('address store rejects invalid phone format', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user)
        ->post(route('frontend.account.addresses.store'), [
            'address_line_1' => 'شارع تجريبي',
            'phone' => '0501234567',
        ])
        ->assertSessionHasErrors('phone');

    expect(CustomerAddress::where('user_id', $user->id)->count())->toBe(0);
});

test('account page renders address section with intl phone assets', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user)
        ->get(route('frontend.account'))
        ->assertOk()
        ->assertSee('عناوين الفوترة')
        ->assertSee('account-address-phone.js', false)
        ->assertSee('intlTelInput', false);
});
