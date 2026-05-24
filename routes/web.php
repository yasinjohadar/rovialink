<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/dashboard', function () {
    return redirect(\App\Support\AuthRedirect::home());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'check.user.active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('webhooks/stripe', \App\Http\Controllers\Webhooks\StripeWebhookController::class)->name('webhooks.stripe');
Route::post('webhooks/paypal', \App\Http\Controllers\Webhooks\PayPalWebhookController::class)->name('webhooks.paypal');

// Legacy store URLs → unified frontend
Route::prefix('store')->group(function () {
    Route::redirect('cart', '/cart', 301);
    Route::redirect('checkout', '/checkout', 301);
    Route::get('checkout/success/{order}', fn ($order) => redirect()->route('frontend.checkout.success', $order));
    Route::get('downloads/{token}', fn (string $token) => redirect()->route('frontend.downloads.show', $token));
});

Route::prefix('store')->name('store.')->group(function () {
    Route::get('products', [\App\Http\Controllers\Store\StoreProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [\App\Http\Controllers\Store\StoreProductController::class, 'show'])->name('products.show');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/frontend.php';
