@extends('admin.layouts.master')

@section('page-title')
    هيرو الصفحة الرئيسية
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">هيرو الصفحة الرئيسية</h5>
                    <p class="text-muted mb-0 small">النصوص، الصورة، الخلفية، الأزرار، وإحصائيات قسم الهيرو في الصفحة الرئيسية للمتجر.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.homepage.index') }}" class="btn btn-outline-secondary btn-sm">لوحة الواجهة</a>
                    <a href="{{ route('frontend.home') }}" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">
                        <i class="fe fe-external-link me-1"></i> معاينة المتجر
                    </a>
                    <a href="{{ route('admin.site-settings.index') }}" class="btn btn-outline-secondary btn-sm">إعدادات الموقع</a>
                </div>
            </div>

            <form action="{{ route('admin.homepage.hero.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card custom-card mb-4">
                    <div class="card-header">
                        <div class="card-title mb-0">النصوص والعنوان</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ([
                                \App\Services\SiteSettingsService::KEY_HERO_BADGE,
                                \App\Services\SiteSettingsService::KEY_HERO_TITLE_PREFIX,
                                \App\Services\SiteSettingsService::KEY_HERO_TYPING_WORDS,
                                \App\Services\SiteSettingsService::KEY_HERO_SUBTITLE,
                            ] as $fieldKey)
                                @include('admin.partials.site-setting-field', [
                                    'key' => $fieldKey,
                                    'def' => $schema[$fieldKey],
                                    'settings' => $settings,
                                ])
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card custom-card mb-4">
                    <div class="card-header">
                        <div class="card-title mb-0">أزرار الإجراء</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ([
                                \App\Services\SiteSettingsService::KEY_HERO_BTN_PRIMARY_LABEL,
                                \App\Services\SiteSettingsService::KEY_HERO_BTN_PRIMARY_URL,
                                \App\Services\SiteSettingsService::KEY_HERO_BTN_SECONDARY_LABEL,
                                \App\Services\SiteSettingsService::KEY_HERO_BTN_SECONDARY_URL,
                            ] as $fieldKey)
                                @include('admin.partials.site-setting-field', [
                                    'key' => $fieldKey,
                                    'def' => $schema[$fieldKey],
                                    'settings' => $settings,
                                ])
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card custom-card mb-4">
                    <div class="card-header">
                        <div class="card-title mb-0">الصورة والخلفية</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ([
                                \App\Services\SiteSettingsService::KEY_HERO_IMAGE,
                                \App\Services\SiteSettingsService::KEY_HERO_BG_MODE,
                                \App\Services\SiteSettingsService::KEY_HERO_BG_COLOR,
                                \App\Services\SiteSettingsService::KEY_HERO_BG_IMAGE,
                            ] as $fieldKey)
                                @include('admin.partials.site-setting-field', [
                                    'key' => $fieldKey,
                                    'def' => $schema[$fieldKey],
                                    'settings' => $settings,
                                ])
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card custom-card mb-4">
                    <div class="card-header">
                        <div class="card-title mb-0">بطاقات الإحصائيات</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @include('admin.partials.site-setting-field', [
                                'key' => \App\Services\SiteSettingsService::KEY_HERO_STATS,
                                'def' => $schema[\App\Services\SiteSettingsService::KEY_HERO_STATS],
                                'settings' => $settings,
                            ])
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">حفظ إعدادات الهيرو</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
@stop
