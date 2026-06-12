@extends('admin.layouts.master')

@section('page-title')
    طلب مرتجع #{{ $orderReturn->id }}
@stop

@section('styles')
    @include('frontend.layouts.theme-variables')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-order-show.css') }}?v=4">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-orders-index.css') }}?v=1">
@endsection

@section('content')
    @php
        $statusLabels = [
            'pending' => ['label' => 'قيد الانتظار', 'color' => '#f59e0b'],
            'approved' => ['label' => 'معتمد', 'color' => '#22c55e'],
            'rejected' => ['label' => 'مرفوض', 'color' => '#ef4444'],
        ];
        $statusMeta = $statusLabels[$orderReturn->status] ?? ['label' => $orderReturn->status, 'color' => '#6c757d'];
        $itemsQty = $orderReturn->items->sum('quantity');
        $requesterInitial = $orderReturn->requestedByUser?->name
            ? mb_substr($orderReturn->requestedByUser->name, 0, 1)
            : '?';
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid order-show-page my-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                </div>
            @endif

            <div class="order-show-hero">
                <div class="order-show-hero__inner">
                    <div class="order-show-hero__top">
                        <div>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <a href="{{ route('admin.order-returns.index') }}" class="order-show-back">
                                    <i class="bi bi-arrow-right"></i>
                                    قائمة طلبات المرتجع
                                </a>
                                <a href="{{ route('admin.orders.show', $orderReturn->order) }}" class="order-show-back">
                                    <i class="bi bi-receipt"></i>
                                    عرض الطلب الأصلي
                                </a>
                            </div>
                            <h1 class="order-show-hero__title">طلب مرتجع #{{ $orderReturn->id }}</h1>
                            <div class="order-show-hero__meta">
                                <span><i class="bi bi-receipt"></i> {{ $orderReturn->order->order_number }}</span>
                                <span><i class="bi bi-calendar3"></i>
                                    {{ ($orderReturn->requested_at ?? $orderReturn->created_at)->format('Y/m/d — H:i') }}
                                </span>
                            </div>
                        </div>
                        <span class="order-show-status">
                            <span class="order-show-status__dot" style="background: {{ $statusMeta['color'] }};"></span>
                            {{ $statusMeta['label'] }}
                        </span>
                    </div>

                    <div class="order-show-hero__stats">
                        <div class="order-show-stat order-show-stat--highlight">
                            <div class="order-show-stat__label">الطلب الأصلي</div>
                            <div class="order-show-stat__value">{{ $orderReturn->order->order_number }}</div>
                        </div>
                        <div class="order-show-stat">
                            <div class="order-show-stat__label">بنود المرتجع</div>
                            <div class="order-show-stat__value">{{ $orderReturn->items->count() }}</div>
                        </div>
                        <div class="order-show-stat">
                            <div class="order-show-stat__label">الكمية المرتجعة</div>
                            <div class="order-show-stat__value">{{ $itemsQty }}</div>
                        </div>
                        <div class="order-show-stat">
                            <div class="order-show-stat__label">طالب المرتجع</div>
                            <div class="order-show-stat__value">{{ $orderReturn->requestedByUser->name ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="order-show-card">
                        <div class="order-show-card__head">
                            <h2 class="order-show-card__title">
                                <i class="bi bi-info-circle"></i>
                                تفاصيل الطلب
                            </h2>
                        </div>
                        <div class="order-show-card__body">
                            <div class="order-show-customer">
                                <div class="order-show-customer__avatar">{{ $requesterInitial }}</div>
                                <div>
                                    <div class="order-show-customer__name">{{ $orderReturn->requestedByUser->name ?? '—' }}</div>
                                    <div class="order-show-customer__detail">
                                        <span>
                                            <i class="bi bi-flag"></i>
                                            <span class="order-show-badge orders-index-status-badge" style="background-color: {{ $statusMeta['color'] }}; color: #fff !important; padding: 0.25rem 0.6rem;">
                                                {{ $statusMeta['label'] }}
                                            </span>
                                        </span>
                                        <span><i class="bi bi-chat-left-text"></i> {{ $orderReturn->reason ?: '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="order-show-card">
                        <div class="order-show-card__head">
                            <h2 class="order-show-card__title">
                                <i class="bi bi-box-seam"></i>
                                بنود المرتجع
                            </h2>
                            <span class="order-show-badge order-show-badge--muted" style="background: rgba(var(--accent-rgb), 0.12); color: var(--accent-color);">{{ $orderReturn->items->count() }} بند</span>
                        </div>
                        <div class="order-show-card__body order-show-card__body--flush">
                            <div class="table-responsive">
                                <table class="table order-show-items mb-0">
                                    <thead>
                                        <tr>
                                            <th>المنتج</th>
                                            <th class="text-center">الكمية المرتجعة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orderReturn->items as $ri)
                                            <tr>
                                                <td>
                                                    <div class="order-show-product">
                                                        <span class="order-show-product__thumb order-show-product__thumb--placeholder">
                                                            <i class="bi bi-box"></i>
                                                        </span>
                                                        <div>
                                                            <div class="order-show-product__name">{{ $ri->orderItem->product_name ?? '—' }}</div>
                                                            @if($ri->orderItem?->variant_description)
                                                                <div class="order-show-product__variant">{{ $ri->orderItem->variant_description }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="order-show-qty">{{ $ri->quantity }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="order-show-summary">
                        @if($orderReturn->status === 'pending')
                            <div class="order-show-card">
                                <div class="order-show-card__head">
                                    <h2 class="order-show-card__title">
                                        <i class="bi bi-check2-square"></i>
                                        اعتماد / رفض
                                    </h2>
                                </div>
                                <div class="order-show-card__body order-show-form">
                                    <form action="{{ route('admin.order-returns.approve', $orderReturn) }}" method="POST" class="mb-4">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">ملاحظة إدارية (اختياري)</label>
                                            <textarea name="admin_note" rows="2" class="form-control" placeholder="ملاحظة عند الاعتماد">{{ old('admin_note', $orderReturn->admin_note) }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-save text-white w-100" style="background: #22c55e; box-shadow: 0 8px 22px -8px rgba(34, 197, 94, 0.5);">
                                            <i class="bi bi-check-circle me-1"></i>
                                            اعتماد طلب المرتجع
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.order-returns.reject', $orderReturn) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">ملاحظة الرفض (اختياري)</label>
                                            <textarea name="admin_note" rows="2" class="form-control" placeholder="سبب الرفض">{{ old('admin_note') }}</textarea>
                                        </div>
                                        <button type="submit" class="btn w-100 text-white fw-bold" style="background: #ef4444; border: none; border-radius: 0.7rem; padding: 0.7rem 1rem; box-shadow: 0 8px 22px -8px rgba(239, 68, 68, 0.45);">
                                            <i class="bi bi-x-circle me-1"></i>
                                            رفض طلب المرتجع
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="order-show-card">
                                <div class="order-show-card__head">
                                    <h2 class="order-show-card__title">
                                        <i class="bi bi-clock-history"></i>
                                        معالجة الطلب
                                    </h2>
                                </div>
                                <div class="order-show-card__body">
                                    @if($orderReturn->processed_at)
                                        <div class="order-show-receipt__row">
                                            <span>تاريخ المعالجة</span>
                                            <span class="orders-index-date">{{ $orderReturn->processed_at->format('Y/m/d H:i') }}</span>
                                        </div>
                                        <div class="order-show-receipt__row">
                                            <span>معالج بواسطة</span>
                                            <span class="fw-semibold">{{ $orderReturn->processedByUser->name ?? '—' }}</span>
                                        </div>
                                    @endif
                                    @if($orderReturn->admin_note)
                                        <hr class="order-show-receipt__divider">
                                        <div class="order-show-note">{{ $orderReturn->admin_note }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
