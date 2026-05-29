@extends('frontend.layouts.master')

@php
    $privacy = privacy_settings();
@endphp

@section('title', site_brand_name() . ' - ' . $privacy['hero_title'])

@push('styles')
<meta name="description" content="{{ Str::limit(strip_tags($privacy['hero_subtitle']), 160) }}">
@endpush

@section('content')
    @include('frontend.partials.blog-hero', [
        'title' => $privacy['hero_title'],
        'subtitle' => $privacy['hero_subtitle'],
        'breadcrumbCurrent' => 'سياسة الخصوصية',
        'icon' => 'fa-shield-halved',
    ])

    <div class="legal-page legal-page--privacy">
        <div class="container py-5">
            <div class="row g-4 g-xl-5">
                <div class="col-lg-4">
                    <aside class="legal-page__sidebar section-fade-up">
                        <div class="glass-card legal-page__meta-card mb-4">
                            <span class="legal-page__meta-badge">
                                <i class="fas fa-clock" aria-hidden="true"></i>
                                آخر تحديث
                            </span>
                            <p class="legal-page__meta-value mb-0">{{ $privacy['last_updated'] }}</p>
                        </div>

                        <div class="glass-card legal-page__toc-card">
                            <h2 class="legal-page__sidebar-title">محتويات الصفحة</h2>
                            <nav class="legal-page__toc" aria-label="محتويات سياسة الخصوصية">
                                <a href="#privacy-intro" class="legal-page__toc-link">
                                    <span class="legal-page__toc-num">•</span>
                                    <span>مقدمة</span>
                                </a>
                                @foreach($privacy['sections'] as $index => $section)
                                    <a href="#privacy-section-{{ $index }}" class="legal-page__toc-link">
                                        <span class="legal-page__toc-num">{{ $index + 1 }}</span>
                                        <span>{{ $section['title'] ?? 'قسم' }}</span>
                                    </a>
                                @endforeach
                            </nav>
                        </div>
                    </aside>
                </div>

                <div class="col-lg-8">
                    <div id="privacy-intro" class="glass-card legal-page__intro section-fade-up mb-4">
                        <div class="legal-page__intro-icon" aria-hidden="true">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <p class="legal-page__intro-text mb-0">{!! nl2br(e($privacy['intro'])) !!}</p>
                    </div>

                    @foreach($privacy['sections'] as $index => $section)
                        <article id="privacy-section-{{ $index }}"
                                 class="glass-card legal-page__section section-fade-up mb-4">
                            <div class="legal-page__section-header">
                                <div class="legal-page__section-icon">
                                    <i class="fas {{ $section['icon'] ?? 'fa-file-lines' }}"></i>
                                </div>
                                <div>
                                    <span class="legal-page__section-num">القسم {{ $index + 1 }}</span>
                                    <h2 class="legal-page__section-title mb-0">{{ $section['title'] ?? 'قسم' }}</h2>
                                </div>
                            </div>
                            <div class="legal-page__section-body">
                                {!! nl2br(e($section['content'] ?? '')) !!}
                            </div>
                        </article>
                    @endforeach

                    <div class="glass-panel legal-page__footer-note text-center section-fade-up">
                        <p class="mb-3">نحترم خصوصيتك ونلتزم بحماية بياناتك وفق هذه السياسة.</p>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <a href="{{ route('frontend.terms') }}" class="btn btn-glass px-4 py-2">الشروط والأحكام</a>
                            <a href="{{ route('frontend.contact') }}" class="btn btn-accent px-4 py-2">
                                تواصل معنا <i class="fas fa-arrow-left ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const tocLinks = document.querySelectorAll('.legal-page__toc-link');
    const sections = document.querySelectorAll('.legal-page__section, #privacy-intro');
    if (!tocLinks.length || !sections.length || !('IntersectionObserver' in window)) return;

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            const id = entry.target.getAttribute('id');
            tocLinks.forEach(function (link) {
                link.classList.toggle('is-active', link.getAttribute('href') === '#' + id);
            });
        });
    }, { rootMargin: '-20% 0px -60% 0px', threshold: 0 });

    sections.forEach(function (section) { observer.observe(section); });
})();
</script>
@endpush
