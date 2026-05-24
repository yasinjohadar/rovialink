<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use App\Events\WhatsAppMessageReceived;
use App\Listeners\AutoReplyWhatsAppListener;
use App\Services\CurrencyService;
use App\Services\ThemeColorService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Helpers/MoneyHelper.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        
        // تسجيل PermissionServiceProvider
        $this->app->register(PermissionServiceProvider::class);

        // Register WhatsApp auto-reply listener
        Event::listen(
            WhatsAppMessageReceived::class,
            AutoReplyWhatsAppListener::class
        );

        View::composer(['admin.layouts.master', 'admin.pages.*'], function ($view) {
            $view->with('currencyService', app(CurrencyService::class));
        });

        View::composer(
            ['frontend.layouts.head', 'frontend.layouts.master', 'frontend.layouts.auth', 'frontend.layouts.theme-variables'],
            function ($view) {
                $view->with('themeColors', app(ThemeColorService::class)->toCssVariables());
            }
        );
    }
}
