@extends('admin.layouts.master')

@section('page-title')
لوحة التحكم
@stop

@section('css')
    @include('frontend.layouts.theme-variables')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-orders-index.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-dashboard.css') }}?v=4">
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid dashboard-index-page my-4">
            <div class="orders-index-hero">
                <div class="orders-index-hero__top">
                    <div>
                        <h1 class="orders-index-hero__title">مرحباً بك في لوحة التحكم</h1>
                        <p class="orders-index-hero__subtitle">نظرة سريعة على أداء المتجر والطلبات والعملاء</p>
                    </div>
                    <div class="orders-index-hero__actions">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm">
                            <i class="bi bi-bag-check me-1"></i> الطلبات
                        </a>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> منتج جديد
                        </a>
                    </div>
                </div>
                <div class="orders-index-stats">
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">إجمالي الطلبات</div>
                        <div class="orders-index-stat__value">{{ number_format($stats['orders_total']) }}</div>
                    </div>
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">مبيعات الشهر</div>
                        <div class="orders-index-stat__value">{{ format_money($stats['sales_month']) }}</div>
                    </div>
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">طلبات معلّقة</div>
                        <div class="orders-index-stat__value">{{ number_format($stats['orders_pending']) }}</div>
                    </div>
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">العملاء</div>
                        <div class="orders-index-stat__value">{{ number_format($stats['customers_total']) }}</div>
                    </div>
                </div>
            </div>

            <div class="orders-index-panel dash-widgets-panel mb-4">
                <div class="orders-index-panel__head">
                    <h2 class="orders-index-panel__title">
                        <i class="bi bi-grid-1x2"></i>
                        مؤشرات الأداء
                    </h2>
                </div>
                <div class="p-3 p-md-4">
                    <div class="row g-3 dash-widgets-row">
                        @foreach($widgets as $widget)
                            @include('admin.partials.dashboard-stat-widget', ['widget' => $widget])
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-6">
                    <div class="orders-index-panel h-100">
                        <div class="orders-index-panel__head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="orders-index-panel__title mb-0">
                                <i class="bi bi-clock-history"></i>
                                أحدث الطلبات
                            </h2>
                            <a href="{{ route('admin.orders.index') }}" class="orders-index-view-btn">
                                <i class="bi bi-arrow-left-short"></i>
                                عرض الكل
                            </a>
                        </div>
                        <div class="orders-index-table-wrap">
                            @include('admin.pages.dashboard.partials.recent-orders-table')
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="orders-index-panel h-100">
                        <div class="orders-index-panel__head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="orders-index-panel__title mb-0">
                                <i class="bi bi-star"></i>
                                آراء العملاء الأخيرة
                            </h2>
                            <a href="{{ route('admin.reviews.index') }}" class="orders-index-view-btn">
                                <i class="bi bi-arrow-left-short"></i>
                                عرض الكل
                            </a>
                        </div>
                        <div class="orders-index-table-wrap">
                            @include('admin.pages.dashboard.partials.recent-reviews-table')
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="orders-index-panel">
                        <div class="orders-index-panel__head">
                            <h2 class="orders-index-panel__title">
                                <i class="bi bi-lightning-charge"></i>
                                إجراءات سريعة
                            </h2>
                        </div>
                        <div class="p-3 p-md-4">
                            <div class="row g-3 dash-quick-actions-row">
                                @php
                                    $quickLinks = [
                                        ['route' => 'admin.products.create', 'icon' => 'bi-plus-circle', 'title' => 'إضافة منتج', 'desc' => 'منتج جديد في المتجر'],
                                        ['route' => 'admin.orders.index', 'icon' => 'bi-bag-check', 'title' => 'إدارة الطلبات', 'desc' => 'متابعة وتحديث الحالات'],
                                        ['route' => 'admin.customers.index', 'icon' => 'bi-people', 'title' => 'العملاء', 'desc' => 'حسابات ومشتريات العملاء'],
                                        ['route' => 'admin.coupons.index', 'icon' => 'bi-tag', 'title' => 'الكوبونات', 'desc' => 'خصومات وعروض'],
                                        ['route' => 'admin.categories.index', 'icon' => 'bi-folder2', 'title' => 'التصنيفات', 'desc' => 'تنظيم كتالوج المنتجات'],
                                    ];
                                @endphp
                                @foreach($quickLinks as $link)
                                    <div class="col-6 col-md-4 col-lg">
                                        <a href="{{ route($link['route']) }}" class="dash-quick-link">
                                            <span class="dash-quick-link__icon" aria-hidden="true">
                                                <i class="bi {{ $link['icon'] }}"></i>
                                            </span>
                                            <span class="dash-quick-link__title">{{ $link['title'] }}</span>
                                            <span class="dash-quick-link__desc">{{ $link['desc'] }}</span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
