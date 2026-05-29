<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $siteFaviconUrl = site_setting_url(\App\Services\SiteSettingsService::KEY_SITE_FAVICON);
@endphp
@if($siteFaviconUrl)
<link rel="icon" href="{{ $siteFaviconUrl }}" sizes="any">
<link rel="shortcut icon" href="{{ $siteFaviconUrl }}">
@endif
@isset($seo)
<title>{{ $seo->title }}</title>
@include('frontend.layouts.seo', ['seo' => $seo])
@else
<title>@yield('title', site_brand_name() . ' - تسوق أونلاين')</title>
@endisset
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;600;700;800&family=Cairo:wght@300;400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
@php
    $styleCssPath = public_path('frontend/assets/css/style.css');
    $mainJsPath = public_path('frontend/assets/js/main.js');
    $assetVersion = max(
        file_exists($styleCssPath) ? filemtime($styleCssPath) : 0,
        file_exists($mainJsPath) ? filemtime($mainJsPath) : 0,
        1747756800
    ) ?: time();
@endphp
<link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}?v={{ $assetVersion }}">
@include('frontend.layouts.theme-variables')
@stack('styles')
