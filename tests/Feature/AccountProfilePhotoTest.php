<?php

use App\Models\User;
use App\Services\UserPhotoService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('user can upload profile photo via account settings', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'is_active' => true,
    ]);

    $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

    $this->actingAs($user)
        ->patch(route('frontend.account.profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'photo' => $file,
        ])
        ->assertRedirect(route('frontend.account') . '#profile');

    $user->refresh();

    expect($user->photo)->not->toBeNull()
        ->and($user->photo)->toStartWith('users/photos/');
});

test('account page displays profile photo url when photo exists', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'is_active' => true,
    ]);

    $path = app(UserPhotoService::class)->store(
        UploadedFile::fake()->image('avatar.png'),
        $user
    );

    $user->update(['photo' => $path]);

    $this->actingAs($user)
        ->get(route('frontend.account'))
        ->assertOk()
        ->assertSee($user->photoUrl(), false);
});

test('profile photo upload rejects invalid file type', function () {
    $user = User::factory()->create([
        'is_active' => true,
    ]);

    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->actingAs($user)
        ->patch(route('frontend.account.profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'photo' => $file,
        ])
        ->assertSessionHasErrors('photo');

    expect($user->fresh()->photo)->toBeNull();
});
