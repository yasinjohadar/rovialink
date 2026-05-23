<?php

return [
    'site_name' => env('SEO_SITE_NAME', 'RoviaLink'),
    'default_title' => env('SEO_DEFAULT_TITLE', 'RoviaLink - تسوق أونلاين'),
    'default_description' => env('SEO_DEFAULT_DESCRIPTION', 'متجرك الإلكتروني الأول للتسوق الذكي. اكتشف أفضل المنتجات بأسعار تنافسية.'),
    'default_keywords' => env('SEO_DEFAULT_KEYWORDS', 'متجر إلكتروني, تسوق أونلاين, RoviaLink'),
    'default_og_image' => env('SEO_DEFAULT_OG_IMAGE', '/frontend/assets/images/logo.png'),
    'organization_logo' => env('SEO_ORGANIZATION_LOGO', '/frontend/assets/images/logo.png'),
    'twitter_site' => env('SEO_TWITTER_SITE', ''),
    'locale' => env('SEO_LOCALE', 'ar_SA'),
    'language' => env('SEO_LANGUAGE', 'ar'),

    'blog' => [
        'index_title' => 'المدونة التعليمية - إديو ستور',
        'index_description' => 'مقالات، أخبار، ونصائح تعليمية بأيدي خبراء في مختلف المجالات الرقمية.',
        'category_title_suffix' => ' - مدونة إديو ستور',
        'tag_title_prefix' => 'وسم: ',
        'tag_title_suffix' => ' - مدونة إديو ستور',
    ],
];
