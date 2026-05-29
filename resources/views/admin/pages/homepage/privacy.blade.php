@extends('admin.layouts.master')

@section('page-title')
    سياسة الخصوصية
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
                    <h5 class="page-title fs-21 mb-1">سياسة الخصوصية</h5>
                    <p class="text-muted mb-0 small">تعديل محتوى صفحة سياسة الخصوصية الظاهرة للزوار.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.homepage.index') }}" class="btn btn-outline-secondary btn-sm">لوحة الواجهة</a>
                    <a href="{{ route('frontend.privacy') }}" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">
                        <i class="fe fe-external-link me-1"></i> معاينة الصفحة
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.homepage.privacy.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card custom-card mb-4">
                    <div class="card-header">
                        <div class="card-title mb-0">هيرو الصفحة</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ([
                                \App\Services\SiteSettingsService::KEY_PRIVACY_HERO_TITLE,
                                \App\Services\SiteSettingsService::KEY_PRIVACY_HERO_SUBTITLE,
                                \App\Services\SiteSettingsService::KEY_PRIVACY_LAST_UPDATED,
                                \App\Services\SiteSettingsService::KEY_PRIVACY_INTRO,
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
                        <div class="card-title mb-0">أقسام سياسة الخصوصية</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @include('admin.partials.site-setting-field', [
                                'key' => \App\Services\SiteSettingsService::KEY_PRIVACY_SECTIONS,
                                'def' => $schema[\App\Services\SiteSettingsService::KEY_PRIVACY_SECTIONS],
                                'settings' => $settings,
                            ])
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">حفظ سياسة الخصوصية</button>
                    <a href="{{ route('admin.homepage.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
@stop
