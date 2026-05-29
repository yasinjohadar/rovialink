@extends('admin.layouts.master')

@section('page-title')
    صفحة من نحن
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
                    <h5 class="page-title fs-21 mb-1">صفحة من نحن</h5>
                    <p class="text-muted mb-0 small">تعديل محتوى صفحة «من نحن» الظاهرة للزوار في المتجر.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.homepage.index') }}" class="btn btn-outline-secondary btn-sm">لوحة الواجهة</a>
                    <a href="{{ route('frontend.about') }}" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">
                        <i class="fe fe-external-link me-1"></i> معاينة الصفحة
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.homepage.about.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card custom-card mb-4">
                    <div class="card-header">
                        <div class="card-title mb-0">هيرو الصفحة</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ([
                                \App\Services\SiteSettingsService::KEY_ABOUT_HERO_TITLE,
                                \App\Services\SiteSettingsService::KEY_ABOUT_HERO_SUBTITLE,
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
                        <div class="card-title mb-0">قصة المتجر</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ([
                                \App\Services\SiteSettingsService::KEY_ABOUT_STORY_TITLE,
                                \App\Services\SiteSettingsService::KEY_ABOUT_STORY_TEXT_1,
                                \App\Services\SiteSettingsService::KEY_ABOUT_STORY_TEXT_2,
                                \App\Services\SiteSettingsService::KEY_ABOUT_STORY_IMAGE,
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
                        <div class="card-title mb-0">الرؤية والرسالة</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ([
                                \App\Services\SiteSettingsService::KEY_ABOUT_VISION_TITLE,
                                \App\Services\SiteSettingsService::KEY_ABOUT_VISION_TEXT,
                                \App\Services\SiteSettingsService::KEY_ABOUT_MISSION_TITLE,
                                \App\Services\SiteSettingsService::KEY_ABOUT_MISSION_TEXT,
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
                        <div class="card-title mb-0">قيمنا</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @include('admin.partials.site-setting-field', [
                                'key' => \App\Services\SiteSettingsService::KEY_ABOUT_VALUES,
                                'def' => $schema[\App\Services\SiteSettingsService::KEY_ABOUT_VALUES],
                                'settings' => $settings,
                            ])
                        </div>
                    </div>
                </div>

                <div class="card custom-card mb-4">
                    <div class="card-header">
                        <div class="card-title mb-0">إحصائيات الصفحة</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @include('admin.partials.site-setting-field', [
                                'key' => \App\Services\SiteSettingsService::KEY_ABOUT_STATS,
                                'def' => $schema[\App\Services\SiteSettingsService::KEY_ABOUT_STATS],
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
                                \App\Services\SiteSettingsService::KEY_ABOUT_CTA_TITLE,
                                \App\Services\SiteSettingsService::KEY_ABOUT_CTA_TEXT,
                                \App\Services\SiteSettingsService::KEY_ABOUT_CTA_BTN_LABEL,
                                \App\Services\SiteSettingsService::KEY_ABOUT_CTA_BTN_URL,
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
                    <button type="submit" class="btn btn-primary">حفظ إعدادات من نحن</button>
                    <a href="{{ route('admin.homepage.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
@stop
