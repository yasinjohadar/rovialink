@extends('admin.layouts.master')

@section('page-title')
    الأسئلة الشائعة
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
                    <h5 class="page-title fs-21 mb-1">الأسئلة الشائعة</h5>
                    <p class="text-muted mb-0 small">تعديل محتوى صفحة الأسئلة الشائعة الظاهرة للزوار.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.homepage.index') }}" class="btn btn-outline-secondary btn-sm">لوحة الواجهة</a>
                    <a href="{{ route('frontend.faq') }}" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">
                        <i class="fe fe-external-link me-1"></i> معاينة الصفحة
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.homepage.faq.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card custom-card mb-4">
                    <div class="card-header">
                        <div class="card-title mb-0">هيرو الصفحة</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ([
                                \App\Services\SiteSettingsService::KEY_FAQ_HERO_TITLE,
                                \App\Services\SiteSettingsService::KEY_FAQ_HERO_SUBTITLE,
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
                        <div class="card-title mb-0">مجموعات الأسئلة والأجوبة</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @include('admin.partials.site-setting-field', [
                                'key' => \App\Services\SiteSettingsService::KEY_FAQ_GROUPS,
                                'def' => $schema[\App\Services\SiteSettingsService::KEY_FAQ_GROUPS],
                                'settings' => $settings,
                            ])
                        </div>
                    </div>
                </div>

                <div class="card custom-card mb-4">
                    <div class="card-header">
                        <div class="card-title mb-0">دعوة للإجراء (CTA)</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ([
                                \App\Services\SiteSettingsService::KEY_FAQ_CTA_TITLE,
                                \App\Services\SiteSettingsService::KEY_FAQ_CTA_TEXT,
                                \App\Services\SiteSettingsService::KEY_FAQ_CTA_BTN_LABEL,
                                \App\Services\SiteSettingsService::KEY_FAQ_CTA_BTN_URL,
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

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">حفظ إعدادات الأسئلة الشائعة</button>
                    <a href="{{ route('admin.homepage.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
@stop
