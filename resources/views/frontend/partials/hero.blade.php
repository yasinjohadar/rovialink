@php
    $hero = hero_settings();
    $bgMode = $hero['bg_mode'];
    $sectionClasses = 'hero-section position-relative hero-section--mode-' . $bgMode;
    $heroHighlight = $hero['typing_words'][0] ?? 'البرمجيات';
@endphp
<section class="{{ $sectionClasses }}" @if($bgMode === 'color') style="--hero-bg-color: {{ $hero['bg_color'] }};" @endif>
    @if($bgMode === 'image' && $hero['bg_image_url'])
        <div class="hero-bg-layer hero-bg-layer--image" style="background-image: url('{{ e($hero['bg_image_url']) }}');"></div>
        <div class="hero-bg-overlay"></div>
    @elseif($bgMode === 'color')
        <div class="hero-bg-layer hero-bg-layer--color"></div>
    @endif

    @if($bgMode === 'gradient')
        <div class="hero-bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    @endif

    <div class="hero-section__glass" aria-hidden="true"></div>

    <div class="container hero-section__content position-relative z-1">
        <div class="row hero-section__row align-items-stretch g-4 g-lg-3">
            <div class="col-12 col-lg-6 hero-section__copy text-center text-lg-start order-last order-lg-first">
                <div class="hero-copy-panel h-100 d-flex flex-column justify-content-center">
                    @if($hero['badge'])
                        <span class="hero-badge mb-3">{{ $hero['badge'] }}</span>
                    @endif
                    <h1 class="hero-title display-3 fw-bolder mb-3 mb-lg-4">
                        <span class="hero-title__prefix">{{ $hero['title_prefix'] }}</span>
                        <span class="hero-title__highlight">{{ $heroHighlight }}</span>
                    </h1>
                    @if($hero['subtitle'])
                        <p class="hero-subtitle lead mb-4 pe-lg-4">{{ $hero['subtitle'] }}</p>
                    @endif
                    <div class="hero-actions d-flex gap-3 justify-content-center justify-content-lg-start flex-wrap">
                        <a href="{{ $hero['btn_primary_url'] }}" class="btn btn-accent px-4 py-3 shadow-lg fs-5 hero-actions__btn">
                            {{ $hero['btn_primary_label'] }} <i class="fas fa-arrow-left ms-2"></i>
                        </a>
                        <a href="{{ $hero['btn_secondary_url'] }}" class="btn btn-glass px-4 py-3 fs-5 hero-actions__btn">{{ $hero['btn_secondary_label'] }}</a>
                    </div>
                </div>
            </div>
            @if($hero['image_url'])
                <div class="col-12 col-lg-6 hero-section__visual order-first order-lg-last">
                    <div class="hero-image-wrapper h-lg-100">
                        <img src="{{ $hero['image_url'] }}"
                             alt="{{ site_brand_name() }}"
                             class="hero-main-image"
                             loading="eager">
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="hero-section__wave" aria-hidden="true">
        <svg viewBox="0 0 1440 72" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,36 C320,72 1120,0 1440,36 L1440,72 L0,72 Z" style="fill:var(--page-bg)"/>
        </svg>
    </div>
</section>

@if(!empty($hero['stats']))
<section class="hero-stats-section">
    <div class="container py-4 py-lg-5">
        <div class="row g-4 section-fade-up hero-stats-row">
            @foreach($hero['stats'] as $stat)
                <div class="col-6 col-md-3">
                    <div class="glass-card hero-stat-card h-100">
                        <span class="elegant-card__shine" aria-hidden="true"></span>
                        <div class="hero-stat-card__body">
                            <div class="elegant-card__icon-wrap hero-stat-card__icon">
                                <i class="fas {{ $stat['icon'] ?? 'fa-star' }}"></i>
                            </div>
                            <h2 class="hero-stat-card__value counter en-text fw-bold" data-target="{{ (int) ($stat['target'] ?? 0) }}">0</h2>
                            <p class="hero-stat-card__label">{{ $stat['label'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
