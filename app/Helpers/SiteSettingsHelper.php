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

if (!function_exists('about_resolve_placeholders')) {
    function about_resolve_placeholders(string $text): string
    {
        return str_replace('{{site_name}}', site_brand_name(), $text);
    }
}

if (!function_exists('about_settings')) {
    /**
     * Resolved about page settings for the frontend.
     *
     * @return array<string, mixed>
     */
    function about_settings(): array
    {
        $values = site_setting(SiteSettingsService::KEY_ABOUT_VALUES);
        if (! is_array($values) || $values === []) {
            $values = SiteSettingsService::defaultAboutValues();
        }

        $stats = site_setting(SiteSettingsService::KEY_ABOUT_STATS);
        if (! is_array($stats) || $stats === []) {
            $stats = SiteSettingsService::defaultAboutStats();
        }

        return [
            'hero_title' => site_setting(SiteSettingsService::KEY_ABOUT_HERO_TITLE, 'من نحن'),
            'hero_subtitle' => site_setting(
                SiteSettingsService::KEY_ABOUT_HERO_SUBTITLE,
                'نبني تجربة تسوق رقمية موثوقة — منتجات أصلية، تسليم فوري، ودعم حقيقي لعملائنا.'
            ),
            'story_title' => site_setting(SiteSettingsService::KEY_ABOUT_STORY_TITLE, 'كيف بدأنا؟'),
            'story_text_1' => about_resolve_placeholders((string) site_setting(
                SiteSettingsService::KEY_ABOUT_STORY_TEXT_1,
                'انطلق {{site_name}} من رؤية بسيطة: جعل المنتجات الرقمية متاحة للجميع بأسعار عادلة وتجربة شراء سلسة.'
            )),
            'story_text_2' => about_resolve_placeholders((string) site_setting(
                SiteSettingsService::KEY_ABOUT_STORY_TEXT_2,
                'اليوم نخدم آلاف العملاء بمنتجات رقمية متنوعة مع التزامنا بالتسليم الفوري ودعم فني على مدار الساعة.'
            )),
            'story_image_url' => site_setting_url(SiteSettingsService::KEY_ABOUT_STORY_IMAGE),
            'vision_title' => site_setting(SiteSettingsService::KEY_ABOUT_VISION_TITLE, 'رؤيتنا'),
            'vision_text' => site_setting(
                SiteSettingsService::KEY_ABOUT_VISION_TEXT,
                'أن نكون الوجهة الأولى للمنتجات الرقمية في العالم العربي، حيث يجد العميل الجودة والثقة والتسليم الفوري في كل عملية شراء.'
            ),
            'mission_title' => site_setting(SiteSettingsService::KEY_ABOUT_MISSION_TITLE, 'رسالتنا'),
            'mission_text' => site_setting(
                SiteSettingsService::KEY_ABOUT_MISSION_TEXT,
                'تمكين الأفراد والشركات من الوصول إلى منتجات رقمية أصلية بأسعار تنافسية، مع تجربة دفع آمنة ودعم مستمر.'
            ),
            'values' => $values,
            'stats' => $stats,
            'cta_title' => site_setting(SiteSettingsService::KEY_ABOUT_CTA_TITLE, 'جاهز لتجربة التسوق؟'),
            'cta_text' => site_setting(
                SiteSettingsService::KEY_ABOUT_CTA_TEXT,
                'تصفح منتجاتنا الرقمية واستمتع بتسليم فوري ودفع آمن.'
            ),
            'cta_btn_label' => site_setting(SiteSettingsService::KEY_ABOUT_CTA_BTN_LABEL, 'تصفح المنتجات'),
            'cta_btn_url' => hero_resolve_url(site_setting(SiteSettingsService::KEY_ABOUT_CTA_BTN_URL), 'frontend.shop.index'),
        ];
    }
}

if (!function_exists('faq_settings')) {
    /**
     * Resolved FAQ page settings for the frontend.
     *
     * @return array<string, mixed>
     */
    function faq_settings(): array
    {
        $groups = site_setting(SiteSettingsService::KEY_FAQ_GROUPS);
        if (! is_array($groups) || $groups === []) {
            $groups = SiteSettingsService::defaultFaqGroups();
        }

        return [
            'hero_title' => site_setting(SiteSettingsService::KEY_FAQ_HERO_TITLE, 'الأسئلة الشائعة'),
            'hero_subtitle' => site_setting(
                SiteSettingsService::KEY_FAQ_HERO_SUBTITLE,
                'إجابات سريعة عن الطلبات، الدفع، التسليم الرقمي، والدعم — كل ما تحتاج معرفته في مكان واحد.'
            ),
            'groups' => $groups,
            'cta_title' => site_setting(SiteSettingsService::KEY_FAQ_CTA_TITLE, 'لم تجد إجابتك؟'),
            'cta_text' => site_setting(
                SiteSettingsService::KEY_FAQ_CTA_TEXT,
                'فريق الدعم جاهز لمساعدتك في أي استفسار حول طلباتك أو منتجاتنا.'
            ),
            'cta_btn_label' => site_setting(SiteSettingsService::KEY_FAQ_CTA_BTN_LABEL, 'تواصل معنا'),
            'cta_btn_url' => hero_resolve_url(site_setting(SiteSettingsService::KEY_FAQ_CTA_BTN_URL), 'frontend.contact'),
        ];
    }
}

if (!function_exists('terms_settings')) {
    /**
     * Resolved terms page settings for the frontend.
     *
     * @return array<string, mixed>
     */
    function terms_settings(): array
    {
        $sections = site_setting(SiteSettingsService::KEY_TERMS_SECTIONS);
        if (! is_array($sections) || $sections === []) {
            $sections = SiteSettingsService::defaultTermsSections();
        }

        $lastUpdated = trim((string) site_setting(SiteSettingsService::KEY_TERMS_LAST_UPDATED, ''));
        if ($lastUpdated === '') {
            $lastUpdated = now()->translatedFormat('j F Y');
        }

        return [
            'hero_title' => site_setting(SiteSettingsService::KEY_TERMS_HERO_TITLE, 'الشروط والأحكام'),
            'hero_subtitle' => site_setting(
                SiteSettingsService::KEY_TERMS_HERO_SUBTITLE,
                'يرجى قراءة هذه الشروط بعناية قبل استخدام المتجر أو إتمام أي عملية شراء.'
            ),
            'last_updated' => $lastUpdated,
            'intro' => about_resolve_placeholders((string) site_setting(
                SiteSettingsService::KEY_TERMS_INTRO,
                'باستخدامك لموقع {{site_name}} فإنك توافق على الالتزام بالشروط والأحكام التالية.'
            )),
            'sections' => $sections,
        ];
    }
}

if (!function_exists('privacy_settings')) {
    /**
     * Resolved privacy page settings for the frontend.
     *
     * @return array<string, mixed>
     */
    function privacy_settings(): array
    {
        $sections = site_setting(SiteSettingsService::KEY_PRIVACY_SECTIONS);
        if (! is_array($sections) || $sections === []) {
            $sections = SiteSettingsService::defaultPrivacySections();
        }

        $lastUpdated = trim((string) site_setting(SiteSettingsService::KEY_PRIVACY_LAST_UPDATED, ''));
        if ($lastUpdated === '') {
            $lastUpdated = now()->translatedFormat('j F Y');
        }

        return [
            'hero_title' => site_setting(SiteSettingsService::KEY_PRIVACY_HERO_TITLE, 'سياسة الخصوصية'),
            'hero_subtitle' => site_setting(
                SiteSettingsService::KEY_PRIVACY_HERO_SUBTITLE,
                'نلتزم بحماية بياناتك وشرح كيفية جمعها واستخدامها بشفافية تامة.'
            ),
            'last_updated' => $lastUpdated,
            'intro' => about_resolve_placeholders((string) site_setting(
                SiteSettingsService::KEY_PRIVACY_INTRO,
                'توضّح سياسة الخصوصية هذه كيف يتعامل {{site_name}} مع بياناتك الشخصية.'
            )),
            'sections' => $sections,
        ];
    }
}
