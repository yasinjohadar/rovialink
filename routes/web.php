<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ProfileController;

Route::get('/dashboard', function () {
    return redirect(\App\Support\AuthRedirect::home());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'check.user.active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Store (public shop) routes
Route::prefix('store')->name('store.')->group(function () {
    Route::get('products', [\App\Http\Controllers\Store\StoreProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [\App\Http\Controllers\Store\StoreProductController::class, 'show'])->name('products.show');
    Route::get('cart', [\App\Http\Controllers\Store\CartController::class, 'index'])->name('cart.index');
    Route::post('cart', [\App\Http\Controllers\Store\CartController::class, 'store'])->name('cart.store');
    Route::patch('cart/{itemId}', [\App\Http\Controllers\Store\CartController::class, 'update'])->name('cart.update');
    Route::delete('cart/{itemId}', [\App\Http\Controllers\Store\CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('cart/apply-coupon', [\App\Http\Controllers\Store\CartController::class, 'applyCoupon'])->name('cart.apply-coupon');
    Route::post('cart/remove-coupon', [\App\Http\Controllers\Store\CartController::class, 'removeCoupon'])->name('cart.remove-coupon');
    Route::get('checkout', [\App\Http\Controllers\Store\CheckoutController::class, 'index'])->name('checkout.index')->middleware('auth');
    Route::post('checkout', [\App\Http\Controllers\Store\CheckoutController::class, 'store'])->name('checkout.store')->middleware('auth');
    Route::get('checkout/success/{order}', [\App\Http\Controllers\Store\CheckoutController::class, 'success'])->name('checkout.success')->middleware('auth');
    Route::get('downloads/{token}', [\App\Http\Controllers\Store\DownloadController::class, 'download'])->name('downloads.show')->middleware('auth');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/frontend.php';