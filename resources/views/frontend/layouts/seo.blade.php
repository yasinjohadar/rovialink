@if($seo->description)
<meta name="description" content="{{ $seo->description }}">
@endif

@if($seo->keywords)
<meta name="keywords" content="{{ $seo->keywords }}">
@endif

@if($seo->canonical)
<link rel="canonical" href="{{ $seo->canonical }}">
@endif

<meta name="robots" content="{{ $seo->robots }}">

<link rel="alternate" hreflang="{{ config('seo.language', 'ar') }}" href="{{ $seo->canonical ?? url()->current() }}">

@if($seo->prevUrl)
<link rel="prev" href="{{ $seo->prevUrl }}">
@endif

@if($seo->nextUrl)
<link rel="next" href="{{ $seo->nextUrl }}">
@endif

<meta property="og:title" content="{{ $seo->ogTitle() }}">
@if($seo->ogDescription())
<meta property="og:description" content="{{ $seo->ogDescription() }}">
@endif
@if($seo->ogImage)
<meta property="og:image" content="{{ $seo->ogImage }}">
@endif
@if($seo->ogUrl)
<meta property="og:url" content="{{ $seo->ogUrl }}">
@endif
<meta property="og:type" content="{{ $seo->ogType }}">
<meta property="og:locale" content="{{ $seo->ogLocale ?? config('seo.locale') }}">
<meta property="og:site_name" content="{{ $seo->ogSiteName ?? config('seo.site_name') }}">

@if($seo->articlePublishedTime)
<meta property="article:published_time" content="{{ $seo->articlePublishedTime }}">
@endif
@if($seo->articleModifiedTime)
<meta property="article:modified_time" content="{{ $seo->articleModifiedTime }}">
@endif

<meta name="twitter:card" content="{{ $seo->twitterCard }}">
<meta name="twitter:title" content="{{ $seo->twitterTitle() }}">
@if($seo->twitterDescription())
<meta name="twitter:description" content="{{ $seo->twitterDescription() }}">
@endif
@if($seo->twitterImage)
<meta name="twitter:image" content="{{ $seo->twitterImage }}">
@endif
@if($seo->twitterCreator)
<meta name="twitter:creator" content="{{ $seo->twitterCreator }}">
@endif
@if($seo->twitterSite)
<meta name="twitter:site" content="{{ $seo->twitterSite }}">
@endif

@foreach($seo->jsonLdScripts() as $schema)
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
@endforeach

@stack('seo')
