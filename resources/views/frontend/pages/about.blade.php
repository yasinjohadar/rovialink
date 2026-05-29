@extends('frontend.layouts.master')

@php
    $about = about_settings();
@endphp

@section('title', site_brand_name() . ' - ' . $about['hero_title'])

@push('styles')
<meta name="description" content="{{ Str::limit(strip_tags($about['hero_subtitle']), 160) }}">
@endpush

@section('content')
    @include('frontend.partials.blog-hero', [
        'title' => $about['hero_title'],
        'subtitle' => $about['hero_subtitle'],
        'breadcrumbCurrent' => 'من نحن',
        'icon' => 'fa-users',
    ])

    <div class="about-page">
        <div class="container py-5">
            {{-- Story --}}
            <div class="row align-items-center g-5 mb-5 pb-lg-4 section-fade-up">
                <div class="col-lg-6">
                    <div class="about-page__story-visual glass-card">
                        @if($about['story_image_url'])
                            <img src="{{ $about['story_image_url'] }}"
                                 alt="{{ $about['story_title'] }}"
                                 class="about-page__story-image"
                                 loading="lazy">
                        @else
                            <div class="about-page__story-placeholder" aria-hidden="true">
                                <span class="about-page__story-glow"></span>
                                <i class="fas fa-store"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <span class="about-page__eyebrow">قصتنا</span>
                    <h2 class="about-page__heading">{{ $about['story_title'] }}</h2>
                    <p class="about-page__text">{{ $about['story_text_1'] }}</p>
                    <p class="about-page__text mb-4">{{ $about['story_text_2'] }}</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('frontend.shop.index') }}" class="btn btn-accent px-4 py-2">
                            تصفح المنتجات <i class="fas fa-arrow-left ms-2"></i>
                        </a>
                        <a href="{{ route('frontend.contact') }}" class="btn btn-glass px-4 py-2">تواصل معنا</a>
                    </div>
                </div>
            </div>

            {{-- Vision & Mission --}}
            <div class="row g-4 mb-5 pb-lg-3 section-fade-up">
                <div class="col-md-6">
                    <div class="glass-card about-page__vm-card about-page__vm-card--vision h-100">
                        <span class="elegant-card__shine" aria-hidden="true"></span>
                        <div class="about-page__vm-glow about-page__vm-glow--vision" aria-hidden="true"></div>
                        <i class="fas fa-eye about-page__vm-icon"></i>
                        <h3 class="about-page__vm-title">{{ $about['vision_title'] }}</h3>
                        <p class="about-page__vm-text mb-0">{{ $about['vision_text'] }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="glass-card about-page__vm-card about-page__vm-card--mission h-100">
                        <span class="elegant-card__shine" aria-hidden="true"></span>
                        <div class="about-page__vm-glow about-page__vm-glow--mission" aria-hidden="true"></div>
                        <i class="fas fa-bullseye about-page__vm-icon about-page__vm-icon--mission"></i>
                        <h3 class="about-page__vm-title">{{ $about['mission_title'] }}</h3>
                        <p class="about-page__vm-text mb-0">{{ $about['mission_text'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Values --}}
            <div class="about-page__section-header text-center section-fade-up">
                <span class="about-page__eyebrow">ما يميزنا</span>
                <h2 class="about-page__heading mb-2">قيمنا</h2>
                <p class="about-page__lead mx-auto">مبادئ نلتزم بها في كل تفاعل مع عملائنا.</p>
            </div>
            <div class="row g-4 mb-5 pb-lg-3 section-fade-up">
                @foreach($about['values'] as $value)
                    <div class="col-sm-6 col-lg-3">
                        <div class="glass-card feature-elegant-card about-page__value-card h-100">
                            <span class="elegant-card__shine" aria-hidden="true"></span>
                            <div class="feature-elegant-card__body text-center">
                                <div class="elegant-card__icon-wrap mx-auto mb-3">
                                    <i class="fas {{ $value['icon'] ?? 'fa-star' }}"></i>
                                </div>
                                <h5 class="feature-elegant-card__title">{{ $value['title'] ?? '' }}</h5>
                                <p class="feature-elegant-card__text mb-0">{{ $value['text'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Stats --}}
            <div class="glass-panel about-page__stats section-fade-up">
                <div class="text-center mb-5">
                    <span class="about-page__eyebrow">أرقامنا</span>
                    <h2 class="about-page__heading mb-0">أرقام نفخر بها</h2>
                </div>
                <div class="row g-4 text-center">
                    @foreach($about['stats'] as $stat)
                        <div class="col-6 col-md-3">
                            <div class="about-page__stat-item">
                                <div class="elegant-card__icon-wrap about-page__stat-icon mx-auto mb-3">
                                    <i class="fas {{ $stat['icon'] ?? 'fa-star' }}"></i>
                                </div>
                                <div class="about-page__stat-value counter en-text fw-bold" data-target="{{ (int) ($stat['target'] ?? 0) }}">0</div>
                                <p class="about-page__stat-label mb-0">{{ $stat['label'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- CTA --}}
            <div class="glass-panel about-page__cta text-center section-fade-up mt-5">
                <h3 class="about-page__cta-title">{{ $about['cta_title'] }}</h3>
                <p class="about-page__cta-text mx-auto mb-4">{{ $about['cta_text'] }}</p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ $about['cta_btn_url'] }}" class="btn btn-accent px-5 py-2 rounded-pill">
                        {{ $about['cta_btn_label'] }} <i class="fas fa-arrow-left ms-2"></i>
                    </a>
                    <a href="{{ route('frontend.contact') }}" class="btn btn-glass px-4 py-2 rounded-pill">تواصل معنا</a>
                </div>
            </div>
        </div>
    </div>
@endsection
