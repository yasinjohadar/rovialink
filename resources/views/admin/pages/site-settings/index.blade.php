@extends('admin.layouts.master')

@section('page-title')
    إعدادات الموقع العامة
@stop

@section('css')
    @include('frontend.layouts.theme-variables')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-orders-index.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-site-settings.css') }}?v=1">
@stop

@section('content')
    @php
        $bySection = [];
        foreach ($schema as $key => $def) {
            $bySection[$def['section']][$key] = $def;
        }
        $sectionsOrder = array_keys($sectionLabels);
        $visibleSections = array_values(array_filter($sectionsOrder, fn ($k) => isset($bySection[$k])));

        $sectionIcons = [
            'general' => 'bi-gear',
            'branding' => 'bi-palette',
            'about' => 'bi-info-circle',
            'faq' => 'bi-question-circle',
            'terms' => 'bi-file-text',
            'privacy' => 'bi-shield-lock',
            'contact' => 'bi-telephone',
            'maintenance' => 'bi-tools',
            'locale' => 'bi-translate',
            'seo' => 'bi-search',
            'social' => 'bi-share',
            'social_auth' => 'bi-box-arrow-in-right',
        ];

        $sectionDescriptions = [
            'general' => 'اسم الموقع والوصف الأساسي.',
            'branding' => 'الشعار، الأيقونة، ولون التمييز.',
            'about' => 'محتوى صفحة من نحن.',
            'faq' => 'مجموعات الأسئلة الشائعة.',
            'terms' => 'نصوص الشروط والأحكام.',
            'privacy' => 'نصوص سياسة الخصوصية.',
            'contact' => 'بيانات التواصل والفوتر.',
            'maintenance' => 'تفعيل وضع الصيانة ورسالته.',
            'locale' => 'اللغة الافتراضية والمنطقة الزمنية.',
            'seo' => 'إعدادات محركات البحث والميتا.',
            'social' => 'روابط حسابات التواصل الاجتماعي.',
            'social_auth' => 'تفعيل Google وFacebook وتخزين مفاتيح OAuth.',
        ];

        $siteName = $settings[\App\Services\SiteSettingsService::KEY_SITE_NAME] ?? 'RoviaLink';
        $accentColor = $settings[\App\Services\SiteSettingsService::KEY_SITE_ACCENT_COLOR] ?? '#387e99';
        $maintenanceOn = ! empty($settings[\App\Services\SiteSettingsService::KEY_SITE_MAINTENANCE_MODE]);
        $locale = $settings[\App\Services\SiteSettingsService::KEY_SITE_LOCALE] ?? 'ar';
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid site-settings-page my-4">
            <div class="orders-index-hero">
                <div class="orders-index-hero__top">
                    <div>
                        <h1 class="orders-index-hero__title">إعدادات الموقع العامة</h1>
                        <p class="orders-index-hero__subtitle">
                            اسم الموقع، الشعار، التواصل، وضع الصيانة، اللغة والمنطقة، ومحركات البحث
                        </p>
                    </div>
                    <div class="orders-index-hero__actions">
                        <a href="{{ route('admin.homepage.hero.edit') }}" class="btn btn-sm">
                            <i class="bi bi-image me-1"></i>
                            هيرو الرئيسية
                        </a>
                        <a href="{{ route('frontend.home') }}" class="btn btn-sm" target="_blank" rel="noopener noreferrer">
                            <i class="bi bi-box-arrow-up-left me-1"></i>
                            معاينة المتجر
                        </a>
                    </div>
                </div>
                <div class="orders-index-stats">
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">اسم الموقع</div>
                        <div class="orders-index-stat__value" style="font-size:0.95rem;">{{ $siteName }}</div>
                    </div>
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">لون التمييز</div>
                        <div class="orders-index-stat__value d-flex align-items-center gap-2">
                            <span class="site-settings-color-swatch" style="background: {{ $accentColor }};"></span>
                            <span style="font-size:0.85rem;">{{ $accentColor }}</span>
                        </div>
                    </div>
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">وضع الصيانة</div>
                        <div class="orders-index-stat__value">{{ $maintenanceOn ? 'مفعّل' : 'معطّل' }}</div>
                    </div>
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">اللغة</div>
                        <div class="orders-index-stat__value">{{ $locale === 'ar' ? 'العربية' : 'English' }}</div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="site-settings-alerts">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="site-settings-alerts">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="orders-index-panel site-settings-panel">
                    <div class="site-settings-layout">
                        <nav class="site-settings-nav" aria-label="أقسام الإعدادات">
                            <ul class="site-settings-nav__list nav nav-pills flex-column" id="siteSettingsTabs" role="tablist">
                                @foreach ($visibleSections as $idx => $sectionKey)
                                    <li class="nav-item" role="presentation">
                                        <button
                                            class="site-settings-nav__btn nav-link {{ $idx === 0 ? 'active' : '' }}"
                                            id="tab-{{ $sectionKey }}"
                                            data-bs-toggle="tab"
                                            data-bs-target="#pane-{{ $sectionKey }}"
                                            type="button"
                                            role="tab"
                                            aria-controls="pane-{{ $sectionKey }}"
                                            aria-selected="{{ $idx === 0 ? 'true' : 'false' }}"
                                        >
                                            <i class="bi {{ $sectionIcons[$sectionKey] ?? 'bi-sliders' }}"></i>
                                            {{ $sectionLabels[$sectionKey] }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </nav>

                        <div class="site-settings-content tab-content" id="siteSettingsTabsContent">
                            @foreach ($visibleSections as $idx => $sectionKey)
                                <div
                                    class="tab-pane fade {{ $idx === 0 ? 'show active' : '' }}"
                                    id="pane-{{ $sectionKey }}"
                                    role="tabpanel"
                                    aria-labelledby="tab-{{ $sectionKey }}"
                                >
                                    <div class="site-settings-section-head">
                                        <h2 class="site-settings-section-head__title">{{ $sectionLabels[$sectionKey] }}</h2>
                                        <p class="site-settings-section-head__desc">{{ $sectionDescriptions[$sectionKey] ?? '' }}</p>
                                    </div>

                                    @if ($sectionKey === 'social_auth')
                                        @include('admin.pages.site-settings.partials.social-auth-guide')
                                    @endif

                                    <div class="row g-3">
                                        @foreach ($bySection[$sectionKey] as $key => $def)
                                            @include('admin.partials.site-setting-field', [
                                                'key' => $key,
                                                'def' => $def,
                                                'settings' => $settings,
                                            ])
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="site-settings-footer">
                        <p class="site-settings-footer__note mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            التغييرات تُطبَّق على الواجهة الأمامية ولوحة التحكم بعد الحفظ.
                        </p>
                        <div class="site-settings-footer__actions">
                            <a href="{{ route('admin.dashboard') }}" class="site-settings-cancel-btn">إلغاء</a>
                            <button type="submit" class="site-settings-save-btn">
                                <i class="bi bi-check2"></i>
                                حفظ الإعدادات
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
