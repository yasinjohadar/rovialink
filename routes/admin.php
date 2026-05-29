<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ReviewSettingsController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogTagController;
use App\Http\Controllers\Admin\AppStorageController;
use App\Http\Controllers\Admin\AppStorageAnalyticsController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\BackupScheduleController;
use App\Http\Controllers\Admin\BackupStorageController;
use App\Http\Controllers\Admin\BackupStorageAnalyticsController;
use App\Http\Controllers\Admin\StorageDiskMappingController;
use App\Http\Controllers\Admin\WhatsAppSettingsController;
use App\Http\Controllers\Admin\WhatsAppMessageController;
use App\Http\Controllers\Admin\WhatsAppWebController;
use App\Http\Controllers\Admin\WhatsAppWebSettingsController;
use App\Http\Controllers\Admin\WhatsAppWebhookController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ProductAttributeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\WishlistController;
use App\Http\Controllers\Admin\OrderReturnController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\ProductCompareController;
use App\Http\Controllers\Admin\SystemStatusController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth', 'check.user.active'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::put('users/{user}/change-password', [UserController::class, 'updatePassword'])->name('users.update-password');
    Route::post('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // System status
    Route::get('system-status', [SystemStatusController::class, 'index'])->name('system-status.index');

    // General site settings
    Route::get('settings/site', [\App\Http\Controllers\Admin\GeneralSiteSettingsController::class, 'index'])->name('site-settings.index');
    Route::put('settings/site', [\App\Http\Controllers\Admin\GeneralSiteSettingsController::class, 'update'])->name('site-settings.update');

    // Frontend / homepage
    Route::get('homepage', [\App\Http\Controllers\Admin\HomepageSettingsController::class, 'index'])->name('homepage.index');
    Route::get('homepage/hero', [\App\Http\Controllers\Admin\HomepageHeroSettingsController::class, 'edit'])->name('homepage.hero.edit');
    Route::put('homepage/hero', [\App\Http\Controllers\Admin\HomepageHeroSettingsController::class, 'update'])->name('homepage.hero.update');
    Route::get('homepage/about', [\App\Http\Controllers\Admin\AboutPageSettingsController::class, 'edit'])->name('homepage.about.edit');
    Route::put('homepage/about', [\App\Http\Controllers\Admin\AboutPageSettingsController::class, 'update'])->name('homepage.about.update');
    Route::get('homepage/faq', [\App\Http\Controllers\Admin\FaqPageSettingsController::class, 'edit'])->name('homepage.faq.edit');
    Route::put('homepage/faq', [\App\Http\Controllers\Admin\FaqPageSettingsController::class, 'update'])->name('homepage.faq.update');
    Route::get('homepage/terms', [\App\Http\Controllers\Admin\TermsPageSettingsController::class, 'edit'])->name('homepage.terms.edit');
    Route::put('homepage/terms', [\App\Http\Controllers\Admin\TermsPageSettingsController::class, 'update'])->name('homepage.terms.update');
    Route::get('homepage/privacy', [\App\Http\Controllers\Admin\PrivacyPageSettingsController::class, 'edit'])->name('homepage.privacy.edit');
    Route::put('homepage/privacy', [\App\Http\Controllers\Admin\PrivacyPageSettingsController::class, 'update'])->name('homepage.privacy.update');

    // Activity / Audit log
    Route::get('activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');

    // Categories routes
    Route::resource('categories', CategoryController::class);

    // Brands routes
    Route::resource('brands', BrandController::class);

    // Products routes
    // Compare routes must be before resource route so \"compare\" is not matched كـ {product}
    Route::get('products/compare', [ProductCompareController::class, 'compare'])->name('products.compare');
    Route::post('products/{product}/compare/add', [ProductCompareController::class, 'add'])->name('products.compare.add');
    Route::delete('products/{product}/compare/remove', [ProductCompareController::class, 'remove'])->name('products.compare.remove');
    Route::delete('products/compare/clear', [ProductCompareController::class, 'clear'])->name('products.compare.clear');
    Route::post('products/bulk-update', [ProductController::class, 'bulkUpdate'])->name('products.bulk-update');
    Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
    Route::match(['post', 'delete'], 'products/{product}/images/{productImage}', [ProductController::class, 'deleteImage'])
        ->name('products.images.delete');
    Route::post('products/{product}/images/{productImage}/delete', [ProductController::class, 'deleteImage']);
    Route::resource('products', ProductController::class);

    // Product attributes (for variants: color, size, etc.)
    Route::resource('attributes', ProductAttributeController::class)->except(['show']);
    Route::get('attributes/{attribute}/values', [ProductAttributeController::class, 'valuesIndex'])->name('attributes.values.index');
    Route::post('attributes/{attribute}/values', [ProductAttributeController::class, 'valuesStore'])->name('attributes.values.store');
    Route::put('attributes/{attribute}/values/{value}', [ProductAttributeController::class, 'valuesUpdate'])->name('attributes.values.update');
    Route::delete('attributes/{attribute}/values/{value}', [ProductAttributeController::class, 'valuesDestroy'])->name('attributes.values.destroy');

    // Orders routes
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('orders/statuses', [OrderStatusController::class, 'store'])->name('orders.statuses.store');
    Route::put('orders/statuses/{orderStatus}', [OrderStatusController::class, 'update'])->name('orders.statuses.update');
    Route::delete('orders/statuses/{orderStatus}', [OrderStatusController::class, 'destroy'])->name('orders.statuses.destroy');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    // Order returns (RMA)
    Route::get('order-returns', [OrderReturnController::class, 'index'])->name('order-returns.index');
    Route::get('order-returns/{orderReturn}', [OrderReturnController::class, 'show'])->name('order-returns.show');
    Route::post('orders/{order}/order-returns', [OrderReturnController::class, 'store'])->name('order-returns.store');
    Route::post('order-returns/{orderReturn}/approve', [OrderReturnController::class, 'approve'])->name('order-returns.approve');
    Route::post('order-returns/{orderReturn}/reject', [OrderReturnController::class, 'reject'])->name('order-returns.reject');

    // Payment methods
    Route::post('payment-methods/{payment_method}/toggle-active', [PaymentMethodController::class, 'toggleActive'])->name('payment-methods.toggle-active');
    Route::resource('payment-methods', PaymentMethodController::class)->except(['show']);

    // Payment settings (before payments/{payment} to avoid conflict with "settings" id)
    Route::get('payments/settings', [\App\Http\Controllers\Admin\PaymentSettingsController::class, 'index'])->name('payments.settings.index');
    Route::put('payments/settings', [\App\Http\Controllers\Admin\PaymentSettingsController::class, 'update'])->name('payments.settings.update');
    Route::redirect('payment-settings', 'payments/settings', 301);
    Route::redirect('payment-setting', 'payments/settings', 301);

    Route::get('payments/webhooks', [PaymentController::class, 'webhooks'])->name('payments.webhooks');
    Route::post('payments/{payment}/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    Route::post('payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
    Route::resource('payments', PaymentController::class)->only(['index', 'show']);

    // Certificate functionality removed

    // Reviews routes
    // يجب وضع routes المخصصة قبل resource route لتجنب التعارض
    Route::get('reviews/statistics', [ReviewController::class, 'statistics'])->name('reviews.statistics');
    Route::get('review-settings', [ReviewSettingsController::class, 'index'])->name('review-settings.index');
    Route::put('review-settings', [ReviewSettingsController::class, 'update'])->name('review-settings.update');
    Route::resource('reviews', ReviewController::class);
    Route::post('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    Route::post('reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
    Route::delete('reviews/{review}/images/{image}', [ReviewController::class, 'deleteImage'])->name('reviews.images.delete');

    // Coupons routes (specific routes must be before resource so "usage-report" is not matched as {coupon})
    Route::get('coupons/usage-report', [CouponController::class, 'getUsageReport'])->name('coupons.usage-report');
    Route::post('coupons/mark-expired', [CouponController::class, 'markAsExpired'])->name('coupons.mark-expired');
    Route::resource('coupons', CouponController::class);

    // Tax classes & rates
    Route::get('tax', [TaxController::class, 'index'])->name('tax.index');
    Route::get('tax/classes/create', [TaxController::class, 'createClass'])->name('tax.classes.create');
    Route::post('tax/classes', [TaxController::class, 'storeClass'])->name('tax.classes.store');
    Route::get('tax/classes/{tax_class}/edit', [TaxController::class, 'editClass'])->name('tax.classes.edit');
    Route::put('tax/classes/{tax_class}', [TaxController::class, 'updateClass'])->name('tax.classes.update');
    Route::delete('tax/classes/{tax_class}', [TaxController::class, 'destroyClass'])->name('tax.classes.destroy');
    Route::get('tax/classes/{tax_class}/rates', [TaxController::class, 'rates'])->name('tax.classes.rates');
    Route::post('tax/classes/{tax_class}/rates', [TaxController::class, 'storeRate'])->name('tax.classes.rates.store');
    Route::put('tax/classes/{tax_class}/rates/{rate}', [TaxController::class, 'updateRate'])->name('tax.classes.rates.update');
    Route::delete('tax/classes/{tax_class}/rates/{rate}', [TaxController::class, 'destroyRate'])->name('tax.classes.rates.destroy');

    // Reports dashboard
    Route::get('reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');

    // Customers routes
    Route::resource('customers', CustomerController::class)->only(['index', 'show']);
    Route::post('customers/{customer}/addresses', [CustomerController::class, 'storeAddress'])->name('customers.addresses.store');
    Route::put('customers/{customer}/addresses/{address}', [CustomerController::class, 'updateAddress'])->name('customers.addresses.update');
    Route::delete('customers/{customer}/addresses/{address}', [CustomerController::class, 'destroyAddress'])->name('customers.addresses.destroy');
    Route::post('customers/{customer}/notes', [CustomerController::class, 'storeNote'])->name('customers.notes.store');
    Route::post('customers/{customer}/loyalty/adjust', [CustomerController::class, 'adjustLoyaltyPoints'])->name('customers.loyalty.adjust');

    // Loyalty (points) settings
    Route::get('loyalty/settings', [\App\Http\Controllers\Admin\LoyaltySettingsController::class, 'index'])->name('loyalty.settings.index');
    Route::put('loyalty/settings', [\App\Http\Controllers\Admin\LoyaltySettingsController::class, 'update'])->name('loyalty.settings.update');
    Route::get('loyalty/transactions', [\App\Http\Controllers\Admin\LoyaltyTransactionController::class, 'index'])->name('loyalty.transactions.index');

    // Wishlists (admin view + destroy)
    Route::get('wishlists', [WishlistController::class, 'index'])->name('wishlists.index');
    Route::delete('wishlists/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlists.destroy');

    // Blog routes
    Route::prefix('blog')->name('blog.')->group(function () {
        // Blog Posts routes
        Route::resource('posts', BlogPostController::class);
        Route::post('posts/{post}/toggle-featured', [BlogPostController::class, 'toggleFeatured'])->name('posts.toggle-featured');
        Route::post('posts/{post}/toggle-publish', [BlogPostController::class, 'togglePublish'])->name('posts.toggle-publish');
        Route::delete('posts/{post}/featured-image', [BlogPostController::class, 'deleteFeaturedImage'])->name('posts.delete-featured-image');
        
        // Blog Categories routes
        Route::resource('categories', BlogCategoryController::class);
        Route::post('categories/{category}/toggle-active', [BlogCategoryController::class, 'toggleActive'])->name('categories.toggle-active');
        
        // Blog Tags routes
        Route::resource('tags', BlogTagController::class);
    });



    // ========== Email Settings Routes ==========
    Route::prefix('settings/email')->name('settings.email.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EmailSettingController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\EmailSettingController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\EmailSettingController::class, 'store'])->name('store');
        Route::post('/test-temp', [\App\Http\Controllers\Admin\EmailSettingController::class, 'testTemp'])->name('test-temp');
        Route::get('/{emailSetting}/edit', [\App\Http\Controllers\Admin\EmailSettingController::class, 'edit'])->name('edit');
        Route::put('/{emailSetting}', [\App\Http\Controllers\Admin\EmailSettingController::class, 'update'])->name('update');
        Route::delete('/{emailSetting}', [\App\Http\Controllers\Admin\EmailSettingController::class, 'destroy'])->name('destroy');
        Route::post('/{emailSetting}/activate', [\App\Http\Controllers\Admin\EmailSettingController::class, 'activate'])->name('activate');
        Route::post('/{emailSetting}/test', [\App\Http\Controllers\Admin\EmailSettingController::class, 'test'])->name('test');
        Route::get('/provider/{provider}', [\App\Http\Controllers\Admin\EmailSettingController::class, 'getProviderPreset'])->name('provider.preset');
    });

    // Email templates
    Route::resource('email-templates', EmailTemplateController::class)->except(['show'])->names('email-templates');

    // Zoom functionality removed

    // ========== App Storage Routes ==========
    Route::prefix('storage')->name('storage.')->group(function () {
        Route::get('/', [AppStorageController::class, 'index'])->name('index');
        Route::get('/create', [AppStorageController::class, 'create'])->name('create');
        Route::post('/', [AppStorageController::class, 'store'])->name('store');
        Route::get('/{config}/edit', [AppStorageController::class, 'edit'])->name('edit');
        Route::put('/{config}', [AppStorageController::class, 'update'])->name('update');
        Route::delete('/{config}', [AppStorageController::class, 'destroy'])->name('destroy');
        Route::post('/{config}/test', [AppStorageController::class, 'test'])->name('test');
        Route::post('/test-connection', [AppStorageController::class, 'testConnection'])->name('test-connection');
        Route::get('/analytics', [AppStorageAnalyticsController::class, 'index'])->name('analytics');
    });

    // ========== Backup Routes ==========
    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::get('/create', [BackupController::class, 'create'])->name('create');
        Route::post('/', [BackupController::class, 'store'])->name('store');
        Route::get('/{backup}', [BackupController::class, 'show'])->name('show');
        Route::delete('/{backup}', [BackupController::class, 'destroy'])->name('destroy');
        Route::get('/{backup}/download', [BackupController::class, 'download'])->name('download');
        Route::post('/{backup}/restore', [BackupController::class, 'restore'])->name('restore');
        Route::get('/{backup}/status', [BackupController::class, 'status'])->name('status');
        Route::post('/{backup}/run', [BackupController::class, 'run'])->name('run');
    });

    // ========== Backup Schedule Routes ==========
    Route::prefix('backup-schedules')->name('backup-schedules.')->group(function () {
        Route::get('/', [BackupScheduleController::class, 'index'])->name('index');
        Route::get('/create', [BackupScheduleController::class, 'create'])->name('create');
        Route::post('/', [BackupScheduleController::class, 'store'])->name('store');
        Route::get('/{schedule}/edit', [BackupScheduleController::class, 'edit'])->name('edit');
        Route::put('/{schedule}', [BackupScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}', [BackupScheduleController::class, 'destroy'])->name('destroy');
        Route::post('/{schedule}/execute', [BackupScheduleController::class, 'execute'])->name('execute');
        Route::post('/{schedule}/toggle-active', [BackupScheduleController::class, 'toggleActive'])->name('toggle-active');
    });

    // ========== Backup Storage Routes ==========
    Route::prefix('backup-storage')->name('backup-storage.')->group(function () {
        Route::get('/', [BackupStorageController::class, 'index'])->name('index');
        Route::get('/create', [BackupStorageController::class, 'create'])->name('create');
        Route::post('/', [BackupStorageController::class, 'store'])->name('store');
        Route::get('/{config}/edit', [BackupStorageController::class, 'edit'])->name('edit');
        Route::put('/{config}', [BackupStorageController::class, 'update'])->name('update');
        Route::delete('/{config}', [BackupStorageController::class, 'destroy'])->name('destroy');
        Route::post('/{config}/test', [BackupStorageController::class, 'test'])->name('test');
        Route::post('/test-connection', [BackupStorageController::class, 'testConnection'])->name('test-connection');
        Route::get('/analytics', [BackupStorageAnalyticsController::class, 'index'])->name('analytics');
    });

    // ========== Storage Disk Mappings Routes ==========
    Route::prefix('storage-disk-mappings')->name('storage-disk-mappings.')->group(function () {
        Route::get('/', [StorageDiskMappingController::class, 'index'])->name('index');
        Route::get('/create', [StorageDiskMappingController::class, 'create'])->name('create');
        Route::post('/', [StorageDiskMappingController::class, 'store'])->name('store');
        Route::get('/{mapping}/edit', [StorageDiskMappingController::class, 'edit'])->name('edit');
        Route::put('/{mapping}', [StorageDiskMappingController::class, 'update'])->name('update');
        Route::delete('/{mapping}', [StorageDiskMappingController::class, 'destroy'])->name('destroy');
    });



    Route::prefix('ai')->name('ai.')->group(function () {
    
        // AI Models
        Route::resource('models', \App\Http\Controllers\Admin\AIModelController::class)->names([
            'index' => 'models.index',
            'create' => 'models.create',
            'store' => 'models.store',
            'show' => 'models.show',
            'edit' => 'models.edit',
            'update' => 'models.update',
            'destroy' => 'models.destroy',
        ]);
        Route::post('models/{model}/test', [\App\Http\Controllers\Admin\AIModelController::class, 'test'])->name('models.test');
        Route::post('models/test-temp', [\App\Http\Controllers\Admin\AIModelController::class, 'testTemp'])->name('models.test-temp');
        Route::post('models/{model}/set-default', [\App\Http\Controllers\Admin\AIModelController::class, 'setDefault'])->name('models.set-default');
        Route::post('models/{model}/toggle-active', [\App\Http\Controllers\Admin\AIModelController::class, 'toggleActive'])->name('models.toggle-active');
        Route::post('models/fetch-groq-models', [\App\Http\Controllers\Admin\AIModelController::class, 'fetchGroqModels'])->name('models.fetch-groq-models');
        
        // Content
        Route::post('content/summarize', [\App\Http\Controllers\Admin\AIContentController::class, 'summarize'])->name('content.summarize');
        Route::post('content/improve', [\App\Http\Controllers\Admin\AIContentController::class, 'improve'])->name('content.improve');
        Route::post('content/grammar-check', [\App\Http\Controllers\Admin\AIContentController::class, 'grammarCheck'])->name('content.grammar-check');

        Route::post('products/generate-copy', [\App\Http\Controllers\Admin\AIProductController::class, 'generateCopy'])->name('products.generate-copy');
        Route::post('products/generate-seo', [\App\Http\Controllers\Admin\AIProductController::class, 'generateSeo'])->name('products.generate-seo');

        Route::post('seo/audit', [\App\Http\Controllers\Admin\AISeoAuditController::class, 'audit'])->name('seo.audit');
        Route::post('seo/apply', [\App\Http\Controllers\Admin\AISeoAuditController::class, 'apply'])->name('seo.apply');
        
        // Settings
        Route::get('settings', [\App\Http\Controllers\Admin\AISettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [\App\Http\Controllers\Admin\AISettingsController::class, 'update'])->name('settings.update');
    });
    
    /**
     * Blog AI Posts Routes
     * These should be placed in the blog route group
     */
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('ai-posts/create', [\App\Http\Controllers\Admin\AIBlogPostController::class, 'create'])->name('ai-posts.create');
        Route::post('ai-posts', [\App\Http\Controllers\Admin\AIBlogPostController::class, 'store'])->name('ai-posts.store');
        Route::post('ai-posts/generate', [\App\Http\Controllers\Admin\AIBlogPostController::class, 'generate'])->name('ai-posts.generate');
    });

    // WhatsApp Settings Routes
    Route::prefix('whatsapp-settings')
        ->middleware(['role:admin'])
        ->name('whatsapp-settings.')
        ->group(function () {
            Route::get('/', [WhatsAppSettingsController::class, 'index'])->name('index');
            Route::post('/', [WhatsAppSettingsController::class, 'update'])->name('update');
            Route::post('/test-connection', [WhatsAppSettingsController::class, 'testConnection'])->name('test-connection');
        });

    // WhatsApp Messages Routes
    Route::prefix('whatsapp-messages')
        ->middleware(['role:admin'])
        ->name('whatsapp-messages.')
        ->group(function () {
            Route::get('/', [WhatsAppMessageController::class, 'index'])->name('index');
            Route::get('/send', [WhatsAppMessageController::class, 'create'])->name('create');
            Route::get('/search-students', [WhatsAppMessageController::class, 'searchStudents'])->name('search-students');
            Route::post('/send', [WhatsAppMessageController::class, 'send'])->name('send');
            Route::post('/broadcast', [WhatsAppMessageController::class, 'broadcast'])->name('broadcast');
            Route::get('/broadcast/students-count', [WhatsAppMessageController::class, 'getStudentsCount'])->name('broadcast.students-count');
            Route::post('/{message}/retry', [WhatsAppMessageController::class, 'retry'])->name('retry');
            Route::get('/{message}', [WhatsAppMessageController::class, 'show'])->name('show');
        });

    // WhatsApp Web Routes
    Route::prefix('whatsapp-web')
        ->middleware(['role:admin'])
        ->name('whatsapp-web.')
        ->group(function () {
            Route::get('/connect', [WhatsAppWebController::class, 'connect'])->name('connect');
            Route::post('/start-connection', [WhatsAppWebController::class, 'startConnection'])->name('start-connection');
            Route::get('/qr/{sessionId}', [WhatsAppWebController::class, 'getQrCode'])->name('qr');
            Route::get('/status/{sessionId}', [WhatsAppWebController::class, 'getStatus'])->name('status');
            Route::post('/disconnect/{sessionId}', [WhatsAppWebController::class, 'disconnect'])->name('disconnect');
        });

    // WhatsApp Web Settings Routes
    Route::prefix('whatsapp-web-settings')
        ->middleware(['role:admin'])
        ->name('whatsapp-web-settings.')
        ->group(function () {
            Route::get('/', [WhatsAppWebSettingsController::class, 'index'])->name('index');
            Route::post('/', [WhatsAppWebSettingsController::class, 'update'])->name('update');
            Route::post('/test-connection', [WhatsAppWebSettingsController::class, 'testConnection'])->name('test-connection');
        });

});
