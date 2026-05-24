@extends('frontend.layouts.master')

@section('content')
    @include('frontend.partials.checkout-hero')

    <main class="container py-5 section-fade-up checkout-order-page">
        <div class="text-center mb-5">
            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width:88px;height:88px;">
                <i class="fas fa-check-circle fa-3x text-success"></i>
            </div>
            <h1 class="fw-bold mb-2">تم الدفع بنجاح!</h1>
            <p class="text-secondary mb-0">شكراً لك. طلبك <span class="en-text fw-bold text-accent">#{{ $order->order_number }}</span> قيد المعالجة.</p>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-lg-8">
                <div class="glass-card p-4">
                    <h5 class="account-panel__title mb-4"><i class="fas fa-box me-2"></i> تفاصيل الطلب</h5>
                    <div class="table-responsive account-themed-table-wrap">
                        <table class="table account-themed-table mb-0">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>الكمية</th>
                                    <th>السعر</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="py-3">{{ $item->product_name }}</td>
                                    <td class="py-3 en-text">{{ $item->quantity }}</td>
                                    <td class="py-3 en-text">{{ format_money($item->unit_price) }}</td>
                                    <td class="py-3 en-text text-accent fw-bold">{{ format_money($item->total) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @php $hasDownloads = $order->items->flatMap(fn ($i) => $i->downloads ?? collect())->isNotEmpty(); @endphp
                @if($hasDownloads)
                <div class="glass-card p-4 mt-4">
                    <h5 class="account-panel__title mb-3"><i class="fas fa-download me-2"></i> التحميلات الرقمية</h5>
                    <div class="d-flex flex-column gap-2">
                        @foreach($order->items as $item)
                            @foreach($item->downloads ?? [] as $download)
                            <a href="{{ route('frontend.downloads.show', $download->download_token) }}"
                               class="account-sidebar__footer-link">
                                <i class="fas fa-file-download"></i>
                                <span>{{ $download->file->title ?? 'ملف' }} — {{ $item->product_name }}</span>
                            </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="glass-card p-4">
                    <h5 class="account-panel__title mb-3">ملخص الدفع</h5>
                    @if($payment ?? null)
                    <p class="mb-2"><span class="text-secondary">الحالة:</span>
                        <span class="badge bg-success">{{ $payment->status }}</span>
                    </p>
                    <p class="mb-2"><span class="text-secondary">الوسيلة:</span> {{ $payment->paymentMethod?->name }}</p>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>الإجمالي</span>
                        <span class="text-accent en-text">{{ format_money($order->total) }}</span>
                    </div>
                    <a href="{{ route('frontend.account.orders.show', $order) }}" class="btn btn-accent w-100 mt-4">عرض الطلب</a>
                    <a href="{{ route('frontend.shop.index') }}" class="btn btn-glass w-100 mt-2">متابعة التسوق</a>
                </div>
            </div>
        </div>
    </main>
@endsection
