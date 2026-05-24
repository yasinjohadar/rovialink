@extends('admin.layouts.master')

@section('page-title')
لوحة التحكم
@stop

@section('css')
<link href="{{ asset('assets/css/admin-dashboard.css') }}?v=3" rel="stylesheet">
@stop

@section('content')
        <div class="main-content app-content">
            <div class="container-fluid">
                <div class="page-header">
                    <div>
                        <h3 class="page-title">مرحباً بك في لوحة التحكم</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active" aria-current="page">الرئيسية</li>
                        </ol>
                    </div>
                </div>

                <div class="row g-3 mb-4 dash-widgets-row">
                    @foreach($widgets as $widget)
                        @include('admin.partials.dashboard-stat-widget', ['widget' => $widget])
                    @endforeach
                </div>

                <div class="row g-3">
                    <div class="col-xl-6">
                        <div class="card custom-card dash-table-card">
                            <div class="card-header justify-content-between align-items-center">
                                <div class="card-title mb-0">أحدث الطلبات</div>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                            </div>
                            <div class="card-body dash-table-card__body">
                                @if($recentOrders->isNotEmpty())
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>الرقم</th>
                                                    <th>العميل</th>
                                                    <th>المبلغ</th>
                                                    <th>الحالة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentOrders as $order)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold text-primary">
                                                                {{ $order->order_number }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $order->user?->name ?? 'زائر' }}</td>
                                                        <td>{{ format_money($order->total) }}</td>
                                                        <td>
                                                            @if($order->status)
                                                                <span class="badge text-white" style="background-color: {{ $order->status->color ?? '#6c757d' }}">
                                                                    {{ $order->status->name }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary-transparent">—</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center text-muted py-5">لا توجد طلبات بعد</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card custom-card dash-table-card">
                            <div class="card-header justify-content-between align-items-center">
                                <div class="card-title mb-0">آراء العملاء الأخيرة</div>
                                <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                            </div>
                            <div class="card-body dash-table-card__body">
                                @if($recentReviews->isNotEmpty())
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>المستخدم</th>
                                                    <th>التقييم</th>
                                                    <th>التعليق</th>
                                                    <th>الحالة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentReviews as $review)
                                                    <tr>
                                                        <td>{{ $review->user?->name ?? 'مجهول' }}</td>
                                                        <td>
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted opacity-25' }}" style="font-size:0.7rem"></i>
                                                            @endfor
                                                        </td>
                                                        <td class="text-muted small">{{ \Illuminate\Support\Str::limit($review->comment, 40) }}</td>
                                                        <td>
                                                            @switch($review->status)
                                                                @case('approved')
                                                                    <span class="badge bg-success-transparent">مقبول</span>
                                                                    @break
                                                                @case('rejected')
                                                                    <span class="badge bg-danger-transparent">مرفوض</span>
                                                                    @break
                                                                @default
                                                                    <span class="badge bg-warning-transparent">انتظار</span>
                                                            @endswitch
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center text-muted py-5">لا توجد آراء حالياً</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title mb-0">إجراءات سريعة</div>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    @php
                                        $quickLinks = [
                                            ['route' => 'admin.products.create', 'icon' => 'fe-plus-circle', 'title' => 'إضافة منتج', 'desc' => 'منتج جديد في المتجر'],
                                            ['route' => 'admin.orders.index', 'icon' => 'fe-shopping-bag', 'title' => 'إدارة الطلبات', 'desc' => 'متابعة وتحديث الحالات'],
                                            ['route' => 'admin.customers.index', 'icon' => 'fe-users', 'title' => 'العملاء', 'desc' => 'حسابات ومشتريات العملاء'],
                                            ['route' => 'admin.coupons.index', 'icon' => 'fe-tag', 'title' => 'الكوبونات', 'desc' => 'خصومات وعروض'],
                                            ['route' => 'admin.categories.index', 'icon' => 'fe-folder', 'title' => 'التصنيفات', 'desc' => 'تنظيم كتالوج المنتجات'],
                                        ];
                                    @endphp
                                    @foreach($quickLinks as $link)
                                        <div class="col-6 col-md-4 col-lg-2">
                                            <a href="{{ route($link['route']) }}" class="dash-quick-link border rounded-3 p-3 h-100 d-flex flex-column align-items-center text-center">
                                                <span class="dash-quick-link__icon" aria-hidden="true">
                                                    <i class="fe {{ $link['icon'] }}"></i>
                                                </span>
                                                <h6 class="mb-1 fs-13 fw-semibold">{{ $link['title'] }}</h6>
                                                <p class="text-muted mb-0 small">{{ $link['desc'] }}</p>
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
