<div class="page-hero">
    <div class="page-hero-content container">
        <div class="page-hero-icon"><i class="fas {{ $icon ?? 'fa-blog' }}"></i></div>
        <h1 class="page-hero-title">{{ $title ?? 'المدونة التعليمية' }}</h1>
        @if(!empty($subtitle))
        <p class="page-hero-subtitle">{{ $subtitle }}</p>
        @endif
        <nav class="page-hero-breadcrumb" aria-label="breadcrumb">
            <a href="{{ route('frontend.home') }}">الرئيسية</a>
            <i class="fas fa-chevron-left sep"></i>
            @if(!empty($breadcrumbParent))
                <a href="{{ $breadcrumbParent['url'] }}">{{ $breadcrumbParent['label'] }}</a>
                <i class="fas fa-chevron-left sep"></i>
            @endif
            <span class="current">{{ $breadcrumbCurrent ?? 'المدونة' }}</span>
        </nav>
    </div>
    <div class="page-hero-wave">
        <svg viewBox="0 0 1440 65" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,30 C360,65 1080,0 1440,30 L1440,65 L0,65 Z" style="fill:var(--page-bg)"/>
        </svg>
    </div>
</div>
