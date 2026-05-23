@php
    $hero = hero_settings();
    $bgMode = $hero['bg_mode'];
    $sectionClasses = 'hero-section position-relative pb-5 hero-section--mode-' . $bgMode;
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

    <div class="container h-100 position-relative z-1">
        <div class="row h-100 align-items-center gy-5">
            <div class="col-lg-6 text-center text-lg-start">
                @if($hero['badge'])
                    <span class="badge bg-glass text-white px-3 py-2 rounded-pill mb-3 shadow-sm border border-secondary border-opacity-25" style="letter-spacing: 0.5px;">{{ $hero['badge'] }}</span>
                @endif
                <h1 class="display-3 fw-bolder mb-4 text-white lh-base typing-container" style="direction: rtl;">
                    {{ $hero['title_prefix'] }} <br>
                    <span class="text-accent typing-text" data-text="{{ json_encode($hero['typing_words'], JSON_UNESCAPED_UNICODE) }}"></span><span class="typing-cursor">|</span>
                </h1>
                @if($hero['subtitle'])
                    <p class="lead mb-4 text-white opacity-75 pe-lg-5">{{ $hero['subtitle'] }}</p>
                @endif
                <div class="d-flex gap-3 justify-content-center justify-content-lg-start mt-4 flex-wrap">
                    <a href="{{ $hero['btn_primary_url'] }}" class="btn btn-accent px-4 py-3 shadow-lg fs-5">
                        {{ $hero['btn_primary_label'] }} <i class="fas fa-arrow-left ms-2"></i>
                    </a>
                    <a href="{{ $hero['btn_secondary_url'] }}" class="btn btn-glass px-4 py-3 fs-5">{{ $hero['btn_secondary_label'] }}</a>
                </div>
            </div>
            @if($hero['image_url'])
                <div class="col-lg-6 position-relative d-none d-lg-block">
                    <div class="hero-image-wrapper ms-auto">
                        <img src="{{ $hero['image_url'] }}" alt="{{ $hero['title_prefix'] }}" class="hero-main-image rounded-4 shadow-lg" loading="eager">
                    </div>
                </div>
            @endif
        </div>

        @if(!empty($hero['stats']))
            <div class="row g-4 mt-5 pt-3 section-fade-up hero-stats-row">
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
        @endif
    </div>
</section>
