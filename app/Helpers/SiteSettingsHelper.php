<?php

use App\Services\SiteSettingsService;

if (!function_exists('site_setting')) {
    /**
     * Get a site setting value by key (cached).
     *
     * @param string $key Key from SiteSettingsService (e.g. site_name, site_logo)
     * @param mixed $default Default if not set
     * @return mixed
     */
    function site_setting(string $key, $default = null)
    {
        return app(SiteSettingsService::class)->get($key, $default);
    }
}

if (!function_exists('site_brand_name')) {
    /**
     * Display name of the site (header, footer, titles).
     */
    function site_brand_name(): string
    {
        $name = trim((string) site_setting(SiteSettingsService::KEY_SITE_NAME, ''));

        return $name !== '' ? $name : 'RoviaLink';
    }
}

if (!function_exists('site_setting_url')) {
    /**
     * Get the full URL for a site setting that stores a file path (e.g. site_logo, site_favicon).
     *
     * @param string $key Key such as SiteSettingsService::KEY_SITE_LOGO or KEY_SITE_FAVICON
     * @return string|null URL or null if not set
     */
    function site_setting_url(string $key): ?string
    {
        $path = site_setting($key);
        if (empty($path)) {
            return null;
        }

        return media_url($path);
    }
}

if (!function_exists('hero_resolve_url')) {
    function hero_resolve_url(?string $url, string $fallbackRoute = 'frontend.shop.index'): string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return route($fallbackRoute);
        }
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return url('/' . ltrim($url, '/'));
    }
}

if (!function_exists('hero_settings')) {
    /**
     * Resolved homepage hero settings for the frontend partial.
     *
     * @return array<string, mixed>
     */
    function hero_settings(): array
    {
        $typing = site_setting(SiteSettingsService::KEY_HERO_TYPING_WORDS);
        if (! is_array($typing) || $typing === []) {
            $typing = SiteSettingsService::defaultHeroTypingWords();
        }

        $stats = site_setting(SiteSettingsService::KEY_HERO_STATS);
        if (! is_array($stats) || $stats === []) {
            $stats = SiteSettingsService::defaultHeroStats();
        }

        $bgMode = site_setting(SiteSettingsService::KEY_HERO_BG_MODE, 'gradient');
        if (! in_array($bgMode, ['gradient', 'color', 'image'], true)) {
            $bgMode = 'gradient';
        }

        return [
            'badge' => site_setting(SiteSettingsService::KEY_HERO_BADGE, 'متجرك الرقمي — تسليم فوري وآمن'),
            'title_prefix' => site_setting(SiteSettingsService::KEY_HERO_TITLE_PREFIX, 'احصل على أفضل'),
            'typing_words' => $typing,
            'subtitle' => site_setting(
                SiteSettingsService::KEY_HERO_SUBTITLE,
                'منتجات رقمية أصلية بأسعار تنافسية — تنزيل فوري بعد الدفع، دفع آمن، ودعم فني على مدار الساعة.'
            ),
            'btn_primary_label' => site_setting(SiteSettingsService::KEY_HERO_BTN_PRIMARY_LABEL, 'تصفح المنتجات'),
            'btn_primary_url' => hero_resolve_url(site_setting(SiteSettingsService::KEY_HERO_BTN_PRIMARY_URL), 'frontend.shop.index'),
            'btn_secondary_label' => site_setting(SiteSettingsService::KEY_HERO_BTN_SECONDARY_LABEL, 'التصنيفات'),
            'btn_secondary_url' => hero_resolve_url(site_setting(SiteSettingsService::KEY_HERO_BTN_SECONDARY_URL), 'frontend.categories.index'),
            'image_url' => site_setting_url(SiteSettingsService::KEY_HERO_IMAGE),
            'bg_mode' => $bgMode,
            'bg_color' => site_setting(SiteSettingsService::KEY_HERO_BG_COLOR, '#0a1628'),
            'bg_image_url' => site_setting_url(SiteSettingsService::KEY_HERO_BG_IMAGE),
            'stats' => $stats,
        ];
    }
}
