@extends('frontend.layouts.master')

@php
    $faq = faq_settings();
    $contactEmail = site_setting(\App\Services\SiteSettingsService::KEY_SITE_CONTACT_EMAIL);
    $contactPhone = site_setting(\App\Services\SiteSettingsService::KEY_SITE_CONTACT_PHONE);
    $whatsapp = site_setting(\App\Services\SiteSettingsService::KEY_SITE_WHATSAPP_NUMBER);
@endphp

@section('title', site_brand_name() . ' - ' . $faq['hero_title'])

@push('styles')
<meta name="description" content="{{ Str::limit(strip_tags($faq['hero_subtitle']), 160) }}">
@endpush

@section('content')
    @include('frontend.partials.blog-hero', [
        'title' => $faq['hero_title'],
        'subtitle' => $faq['hero_subtitle'],
        'breadcrumbCurrent' => 'الأسئلة الشائعة',
        'icon' => 'fa-circle-question',
    ])

    <div class="faq-page">
        <div class="container py-5">
            <div class="row g-4 g-xl-5">
                {{-- Sidebar --}}
                <div class="col-lg-4">
                    <div class="faq-page__sidebar section-fade-up">
                        <div class="glass-card faq-page__search-card mb-4">
                            <label class="faq-page__search-label" for="faqSearchInput">
                                <i class="fas fa-search" aria-hidden="true"></i>
                                ابحث في الأسئلة
                            </label>
                            <input type="search"
                                   id="faqSearchInput"
                                   class="faq-page__search-input form-control"
                                   placeholder="مثال: الدفع، التسليم، الاسترجاع..."
                                   autocomplete="off">
                        </div>

                        <div class="glass-card faq-page__nav-card mb-4">
                            <h3 class="faq-page__sidebar-title">التصنيفات</h3>
                            <nav class="faq-page__nav" aria-label="تصنيفات الأسئلة">
                                @foreach($faq['groups'] as $groupIndex => $group)
                                    <a href="#faq-group-{{ $groupIndex }}" class="faq-page__nav-link">
                                        <span class="faq-page__nav-icon"><i class="fas {{ $group['icon'] ?? 'fa-circle-question' }}"></i></span>
                                        <span>{{ $group['title'] ?? 'أسئلة عامة' }}</span>
                                        <span class="faq-page__nav-count">{{ count($group['items'] ?? []) }}</span>
                                    </a>
                                @endforeach
                            </nav>
                        </div>

                        <div class="glass-card faq-page__help-card">
                            <h3 class="faq-page__sidebar-title">تحتاج مساعدة؟</h3>
                            <p class="faq-page__help-text">تواصل معنا مباشرة وسنرد عليك في أقرب وقت.</p>
                            <ul class="faq-page__help-list list-unstyled mb-0">
                                @if($contactEmail)
                                    <li>
                                        <a href="mailto:{{ $contactEmail }}" class="faq-page__help-link">
                                            <i class="fas fa-envelope"></i>
                                            <span>{{ $contactEmail }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if($contactPhone)
                                    <li>
                                        <a href="tel:{{ $contactPhone }}" class="faq-page__help-link">
                                            <i class="fas fa-phone"></i>
                                            <span>{{ $contactPhone }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if($whatsapp)
                                    <li>
                                        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}" class="faq-page__help-link" target="_blank" rel="noopener noreferrer">
                                            <i class="fab fa-whatsapp"></i>
                                            <span>واتساب</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- FAQ groups --}}
                <div class="col-lg-8">
                    <div id="faqNoResults" class="faq-page__empty glass-card section-fade-up d-none">
                        <i class="fas fa-search fa-2x mb-3" aria-hidden="true"></i>
                        <h3 class="h5 mb-2">لا توجد نتائج</h3>
                        <p class="mb-0 text-secondary">جرّب كلمات مختلفة أو تواصل مع فريق الدعم.</p>
                    </div>

                    @foreach($faq['groups'] as $groupIndex => $group)
                        <section id="faq-group-{{ $groupIndex }}"
                                 class="faq-page__group glass-card section-fade-up mb-4"
                                 data-faq-group>
                            <div class="faq-page__group-header">
                                <div class="faq-page__group-icon">
                                    <i class="fas {{ $group['icon'] ?? 'fa-circle-question' }}"></i>
                                </div>
                                <div>
                                    <h2 class="faq-page__group-title mb-1">{{ $group['title'] ?? 'أسئلة عامة' }}</h2>
                                    <p class="faq-page__group-meta mb-0">{{ count($group['items'] ?? []) }} سؤال</p>
                                </div>
                            </div>

                            @if(!empty($group['items']))
                                <div class="accordion faq-page__accordion" id="faqAccordion{{ $groupIndex }}">
                                    @foreach($group['items'] as $itemIndex => $item)
                                        @php
                                            $collapseId = 'faq-collapse-' . $groupIndex . '-' . $itemIndex;
                                            $headingId = 'faq-heading-' . $groupIndex . '-' . $itemIndex;
                                            $isFirst = $itemIndex === 0;
                                        @endphp
                                        <div class="accordion-item faq-page__item" data-faq-item>
                                            <h3 class="accordion-header" id="{{ $headingId }}">
                                                <button class="accordion-button {{ $isFirst ? '' : 'collapsed' }}"
                                                        type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#{{ $collapseId }}"
                                                        aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                                                        aria-controls="{{ $collapseId }}">
                                                    <span class="faq-page__question-icon" aria-hidden="true"><i class="fas fa-question"></i></span>
                                                    <span class="faq-page__question-text">{{ $item['question'] ?? '' }}</span>
                                                </button>
                                            </h3>
                                            <div id="{{ $collapseId }}"
                                                 class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}"
                                                 aria-labelledby="{{ $headingId }}"
                                                 data-bs-parent="#faqAccordion{{ $groupIndex }}">
                                                <div class="accordion-body faq-page__answer">
                                                    {!! nl2br(e($item['answer'] ?? '')) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </section>
                    @endforeach
                </div>
            </div>

            {{-- CTA --}}
            <div class="glass-panel faq-page__cta text-center section-fade-up mt-2">
                <div class="faq-page__cta-icon mx-auto mb-3" aria-hidden="true">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="faq-page__cta-title">{{ $faq['cta_title'] }}</h3>
                <p class="faq-page__cta-text mx-auto mb-4">{{ $faq['cta_text'] }}</p>
                <a href="{{ $faq['cta_btn_url'] }}" class="btn btn-accent px-5 py-2 rounded-pill">
                    {{ $faq['cta_btn_label'] }} <i class="fas fa-arrow-left ms-2"></i>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const searchInput = document.getElementById('faqSearchInput');
    const groups = document.querySelectorAll('[data-faq-group]');
    const emptyState = document.getElementById('faqNoResults');
    if (!searchInput || !groups.length) return;

    function normalize(text) {
        return (text || '').toLowerCase().trim();
    }

    function filterFaq() {
        const query = normalize(searchInput.value);
        let visibleGroups = 0;

        groups.forEach(function (group) {
            let visibleItems = 0;
            group.querySelectorAll('[data-faq-item]').forEach(function (item) {
                const text = normalize(item.textContent);
                const match = query === '' || text.includes(query);
                item.classList.toggle('d-none', !match);
                if (match) visibleItems++;
            });
            const showGroup = visibleItems > 0;
            group.classList.toggle('d-none', !showGroup);
            if (showGroup) visibleGroups++;
        });

        if (emptyState) {
            emptyState.classList.toggle('d-none', visibleGroups > 0 || query === '');
        }
    }

    searchInput.addEventListener('input', filterFaq);
})();
</script>
@endpush
