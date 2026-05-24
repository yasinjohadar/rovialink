@extends('frontend.layouts.master')

@section('content')
<div class="page-hero">
    <div class="page-hero-content container">
        <div class="page-hero-icon"><i class="fas fa-receipt"></i></div>
        <h1 class="page-hero-title">طلب #{{ $order->order_number }}</h1>
        <p class="page-hero-subtitle">{{ $order->created_at->translatedFormat('d F Y — H:i') }}</p>
        <nav class="page-hero-breadcrumb" aria-label="breadcrumb">
            <a href="{{ route('frontend.home') }}">الرئيسية</a>
            <i class="fas fa-chevron-left sep"></i>
            <a href="{{ route('frontend.account') }}">حسابي</a>
            <i class="fas fa-chevron-left sep"></i>
            <span class="current">تفاصيل الطلب</span>
        </nav>
    </div>
    <div class="page-hero-wave">
        <svg viewBox="0 0 1440 65" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,30 C360,65 1080,0 1440,30 L1440,65 L0,65 Z" style="fill:var(--page-bg)"/>
        </svg>
    </div>
</div>

<div class="container py-4 account-order-page">
    @if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="glass-card account-order-card p-4 mb-4">
                <div class="account-panel__head">
                    <h2 class="account-panel__title m-0">المنتجات</h2>
                    @include('frontend.pages.account.partials.order-status-badge', ['order' => $order])
                </div>
                <div class="table-responsive account-themed-table-wrap">
                    <table class="table account-themed-table mb-0">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>الكمية</th>
                                <th>السعر</th>
                                <th>المجموع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="account-order-product">
                                        <img src="{{ product_image_url($item->product?->primary_image?->path ?? null, $item->product_id) }}"
                                             alt="" width="48" height="48" class="account-order-product__img">
                                        <span class="account-order-product__name">{{ $item->product_name }}</span>
                                    </div>
                                </td>
                                <td class="en-text">{{ $item->quantity }}</td>
                                <td class="en-text">{{ number_format($item->unit_price, 2) }} {{ $order->currency ?? 'SAR' }}</td>
                                <td class="en-text text-accent fw-bold">{{ number_format($item->total, 2) }} {{ $order->currency ?? 'SAR' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @php $hasDownloads = $order->items->flatMap->downloads->isNotEmpty(); @endphp
            @if($hasDownloads)
            <div class="glass-card account-order-card p-4 mb-4">
                <h2 class="account-panel__title mb-3"><i class="fas fa-download me-2"></i> التحميلات الرقمية</h2>
                <ul class="list-unstyled mb-0">
                    @foreach($order->items as $item)
                        @foreach($item->downloads as $download)
                        <li class="mb-2">
                            <a href="{{ route('frontend.downloads.show', $download->download_token) }}" class="btn btn-sm btn-accent rounded-pill">
                                <i class="fas fa-download me-1"></i> تحميل — {{ $item->product_name }}
                            </a>
                            @if($download->expires_at)
                            <span class="account-order-muted small ms-2">ينتهي {{ $download->expires_at->format('Y-m-d') }}</span>
                            @endif
                        </li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
            @else
            <div class="glass-card account-order-card p-4 mb-4">
                <p class="account-order-muted mb-0"><i class="fas fa-bolt text-accent me-2"></i> منتج رقمي — التسليم يتم بعد تأكيد الدفع. تواصل مع الدعم إذا لم تصلك روابط التحميل.</p>
            </div>
            @endif

            @if($order->status?->is_final && !$hasPendingReturn)
            <div class="glass-card account-order-card p-4">
                <h2 class="account-panel__title mb-3">طلب إرجاع</h2>
                <form method="POST" action="{{ route('frontend.account.orders.return', $order) }}">
                    @csrf
                    <textarea name="reason" class="form-control bg-glass account-order-input border-secondary mb-3" rows="3" placeholder="سبب طلب الإرجاع..." required>{{ old('reason') }}</textarea>
                    <button type="submit" class="btn btn-outline-warning rounded-pill px-4">إرسال طلب إرجاع</button>
                </form>
            </div>
            @elseif($hasPendingReturn)
            <div class="alert alert-warning">طلب الإرجاع قيد المراجعة.</div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="glass-card account-order-card account-order-summary p-4 position-sticky" style="top:100px;">
                <h2 class="account-panel__title mb-3">ملخص الطلب</h2>
                <div class="d-flex justify-content-between account-order-muted mb-2">
                    <span>المجموع الفرعي</span>
                    <span class="en-text account-order-value">{{ number_format($order->subtotal, 2) }} {{ $order->currency ?? 'SAR' }}</span>
                </div>
                @if($order->discount_amount > 0)
                <div class="d-flex justify-content-between text-success mb-2">
                    <span>الخصم @if($order->coupon_code)({{ $order->coupon_code }})@endif</span>
                    <span class="en-text">−{{ number_format($order->discount_amount, 2) }} {{ $order->currency ?? 'SAR' }}</span>
                </div>
                @endif
                @if($order->tax_amount > 0)
                <div class="d-flex justify-content-between account-order-muted mb-2">
                    <span>الضريبة</span>
                    <span class="en-text account-order-value">{{ number_format($order->tax_amount, 2) }} {{ $order->currency ?? 'SAR' }}</span>
                </div>
                @endif
                <hr class="account-order-divider">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="account-order-total-label">الإجمالي</span>
                    <span class="account-order-total-value en-text">{{ number_format($order->total, 2) }} {{ $order->currency ?? 'SAR' }}</span>
                </div>

                @php $latestPayment = $order->payments->sortByDesc('id')->first(); @endphp
                @if($latestPayment)
                <div class="mt-4 pt-3 account-order-payment">
                    <h3 class="account-panel__title account-panel__title--sm mb-2">الدفع</h3>
                    <div class="d-flex justify-content-between account-order-muted small mb-2">
                        <span>الوسيلة</span>
                        <span class="account-order-value">{{ $latestPayment->paymentMethod?->displayLabel() ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between account-order-muted small mb-2">
                        <span>الحالة</span>
                        @php
                            $paymentStatusMap = [
                                'completed' => ['label' => 'مكتمل', 'class' => 'text-success'],
                                'pending' => ['label' => 'قيد الانتظار', 'class' => 'text-warning'],
                                'failed' => ['label' => 'فشل', 'class' => 'text-danger'],
                                'cancelled' => ['label' => 'ملغي', 'class' => 'account-order-muted'],
                                'refunded' => ['label' => 'مسترد', 'class' => 'text-info'],
                            ];
                            $paymentUi = $paymentStatusMap[$latestPayment->status] ?? ['label' => $latestPayment->status, 'class' => 'account-order-muted'];
                        @endphp
                        <span class="{{ $paymentUi['class'] }}">{{ $paymentUi['label'] }}</span>
                    </div>
                    @if($latestPayment->status === 'pending')
                    <form method="POST" action="{{ route('frontend.checkout.retry', $order) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-accent w-100 rounded-pill">إكمال الدفع</button>
                    </form>
                    @endif
                </div>
                @endif

                <div class="mt-4 d-grid gap-2">
                    <form method="POST" action="{{ route('frontend.account.orders.reorder', $order) }}">
                        @csrf
                        <button type="submit" class="btn btn-accent w-100 rounded-pill">إعادة الطلب للسلة</button>
                    </form>
                    <a href="{{ route('frontend.account') }}#orders" class="btn btn-glass w-100 rounded-pill">العودة للوحة</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
