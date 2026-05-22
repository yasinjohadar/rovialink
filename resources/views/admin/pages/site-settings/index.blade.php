@extends('admin.layouts.master')

@section('page-title')
    إعدادات الموقع العامة
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
                    <h5 class="page-title fs-21 mb-1">إعدادات الموقع العامة</h5>
                    <p class="text-muted mb-0 small">اسم الموقع، الشعار، التواصل، وضع الصيانة، اللغة والمنطقة، ومحركات البحث.</p>
                </div>
                <a href="{{ route('admin.homepage.hero.edit') }}" class="btn btn-outline-primary btn-sm">تعديل هيرو الصفحة الرئيسية</a>
            </div>

            @php
                $bySection = [];
                foreach ($schema as $key => $def) {
                    $bySection[$def['section']][$key] = $def;
                }
                $sectionsOrder = array_keys($sectionLabels);
            @endphp

            <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header border-bottom-0">
                        <ul class="nav nav-tabs card-header-tabs" id="siteSettingsTabs" role="tablist">
                            @foreach ($sectionsOrder as $idx => $sectionKey)
                                @if (isset($bySection[$sectionKey]))
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $idx === 0 ? 'active' : '' }}" id="tab-{{ $sectionKey }}" data-bs-toggle="tab" data-bs-target="#pane-{{ $sectionKey }}"
                                                type="button" role="tab" aria-controls="pane-{{ $sectionKey }}" aria-selected="{{ $idx === 0 ? 'true' : 'false' }}">
                                            {{ $sectionLabels[$sectionKey] }}
                                        </button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-body tab-content" id="siteSettingsTabsContent">
                        @foreach ($sectionsOrder as $idx => $sectionKey)
                            @if (!isset($bySection[$sectionKey]))
                                @continue
                            @endif
                            <div class="tab-pane fade {{ $idx === 0 ? 'show active' : '' }}" id="pane-{{ $sectionKey }}" role="tabpanel" aria-labelledby="tab-{{ $sectionKey }}">
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
                    <div class="card-footer border-top">
                        <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                        <a href="{{ route('admin.homepage.hero.edit') }}" class="btn btn-outline-primary">تعديل هيرو الصفحة الرئيسية</a>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">إلغاء</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
