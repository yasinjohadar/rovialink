<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ShopController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Http\Controllers\Frontend\AccountController;

Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::prefix('')->name('frontend.')->group(function () {
    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Shop
    Route::get('shop', [ShopController::class, 'index'])->name('shop.index');

    // Categories
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('category/{slug}', [CategoryController::class, 'show'])->name('category.show');

    // Products
    Route::get('product/{slug}', [ProductController::class, 'show'])->name('product.show');
    Route::get('product/{slug}/quick-view', [ProductController::class, 'quickView'])->name('product.quick-view');
    Route::get('product/{slug}/quick-view-data', [ProductController::class, 'quickViewData'])->name('product.quick-view-data');

    // Cart
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::delete('clear', [CartController::class, 'clear'])->name('clear');
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/', [CartController::class, 'store'])->name('store');
        Route::patch('/{id}', [CartController::class, 'update'])->name('update');
        Route::delete('/{id}', [CartController::class, 'destroy'])->name('destroy');
        Route::post('apply-coupon', [CartController::class, 'applyCoupon'])->name('apply-coupon');
        Route::post('remove-coupon', [CartController::class, 'removeCoupon'])->name('remove-coupon');
    });

    // Checkout
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index')->middleware('auth');
        Route::post('/', [CheckoutController::class, 'store'])->name('store')->middleware('auth');
    });

    // Blog
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('/category/{slug}', [BlogController::class, 'category'])->name('category');
        Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('tag');
        Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
        Route::post('/{slug}/comment', [BlogController::class, 'storeComment'])->name('comment.store')->middleware('auth');
    });

    // Pages
    Route::get('about', [PageController::class, 'about'])->name('about');
    Route::match(['get', 'post'], 'contact', [PageController::class, 'contact'])->name('contact');
    Route::get('faq', [PageController::class, 'faq'])->name('faq');
    Route::get('privacy', [PageController::class, 'privacy'])->name('privacy');
    Route::get('terms', [PageController::class, 'terms'])->name('terms');

    // User Account
    Route::middleware('auth')->prefix('account')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('account');
        Route::get('orders/{order}', [AccountController::class, 'showOrder'])->name('account.orders.show');
        Route::patch('profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
        Route::put('password', [AccountController::class, 'updatePassword'])->name('account.password.update');
        Route::post('addresses', [AccountController::class, 'storeAddress'])->name('account.addresses.store');
        Route::patch('addresses/{address}', [AccountController::class, 'updateAddress'])->name('account.addresses.update');
        Route::delete('addresses/{address}', [AccountController::class, 'destroyAddress'])->name('account.addresses.destroy');
        Route::post('orders/{order}/reorder', [AccountController::class, 'reorder'])->name('account.orders.reorder');
        Route::post('orders/{order}/return', [AccountController::class, 'storeReturn'])->name('account.orders.return');
        Route::delete('wishlist/{product}', [AccountController::class, 'removeWishlist'])->name('account.wishlist.remove');
    });

    Route::middleware('auth')->group(function () {
        Route::post('wishlist/{product}/toggle', [AccountController::class, 'toggleWishlist'])->name('wishlist.toggle');
    });

    Route::get('wishlist', function () {
        $wishlistItems = auth()->user()->wishlistProducts()
            ->with(['images', 'category', 'brand'])
            ->withAvg(['reviews' => fn ($q) => $q->where('status', 'approved')], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('status', 'approved')])
            ->visible()
            ->orderByPivot('wishlists.created_at', 'desc')
            ->get();
        return view('frontend.pages.wishlist.index', compact('wishlistItems'));
    })->name('wishlist')->middleware('auth');

    Route::get('compare', function () {
        $compareItems = [];
        return view('frontend.pages.compare.index', compact('compareItems'));
    })->name('compare');
});
