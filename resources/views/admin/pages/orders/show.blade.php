@extends('admin.layouts.master')

@section('page-title')
    طلب {{ $order->order_number }}
@stop

@section('styles')
    @include('frontend.layouts.theme-variables')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-order-show.css') }}?v=4">
@endsection

@section('content')
    @php
        $accentColor = app(\App\Services\ThemeColorService::class)->accentHex();
        $statusColor = $order->status?->color ?? $accentColor;
        $itemsCount = $order->items->sum('quantity');
        $contact = $order->contact_address;
        $customerInitial = $contact?->full_name
            ? mb_substr($contact->full_name, 0, 1)
            : ($order->user?->name ? mb_substr($order->user->name, 0, 1) : '?');

        $paymentStatusMap = [
            'completed' => ['label' => 'مكتمل', 'class' => 'order-show-badge--success'],
            'pending' => ['label' => 'قيد الانتظار', 'class' => 'order-show-badge--warning'],
            'failed' => ['label' => 'فشل', 'class' => 'order-show-badge--danger'],
            'refunded' => ['label' => 'مسترد', 'class' => 'order-show-badge--muted'],
        ];
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid order-show-page my-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <div class="order-show-hero">
                <div class="order-show-hero__inner">
                    <div class="order-show-hero__top">
                        <div>
                            <a href="{{ route('admin.orders.index') }}" class="order-show-back mb-3">
                                <i class="bi bi-arrow-right"></i>
                                العودة للقائمة
                            </a>
                            <h1 class="order-show-hero__title">طلب {{ $order->order_number }}</h1>
                            <div class="order-show-hero__meta">
                                <span><i class="bi bi-calendar3"></i> {{ $order->created_at->format('Y/m/d — H:i') }}</span>
                                @if($order->coupon_code)
                                    <span><i class="bi bi-tag"></i> {{ $order->coupon_code }}</span>
                                @endif
                                @if($order->user)
                                    <span>
                                        <i class="bi bi-person"></i>
                                        <a href="{{ route('admin.customers.show', $order->user) }}" class="text-white text-decoration-underline">
                                            {{ $order->user->name }}
                                        </a>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <span class="order-show-status">
                            <span class="order-show-status__dot" style="background: {{ $statusColor }};"></span>
                            {{ $order->status?->name ?? 'غير معروف' }}
                        </span>
                    </div>

                    <div class="order-show-hero__stats">
                        <div class="order-show-stat order-show-stat--highlight">
                            <div class="order-show-stat__label">إجمالي الطلب</div>
                            <div class="order-show-stat__value">{{ $currencyService->format((float) $order->total) }}</div>
                        </div>
                        <div class="order-show-stat">
                            <div class="order-show-stat__label">عدد المنتجات</div>
                            <div class="order-show-stat__value">{{ $itemsCount }} قطعة</div>
                        </div>
                        <div class="order-show-stat">
                            <div class="order-show-stat__label">بنود الطلب</div>
                            <div class="order-show-stat__value">{{ $order->items->count() }} منتج</div>
                        </div>
                        <div class="order-show-stat">
                            <div class="order-show-stat__label">المدفوعات</div>
                            <div class="order-show-stat__value">{{ $order->payments->count() ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    {{-- عناصر الطلب --}}
                    <div class="order-show-card">
                        <div class="order-show-card__head">
                            <h2 class="order-show-card__title">
                                <i class="bi bi-bag-check"></i>
                                عناصر الطلب
                            </h2>
                            <span class="order-show-badge order-show-badge--muted">{{ $order->items->count() }} بند</span>
                        </div>
                        <div class="order-show-card__body order-show-card__body--flush">
                            <div class="table-responsive">
                                <table class="table order-show-items mb-0">
                                    <thead>
                                        <tr>
                                            <th>المنتج</th>
                                            <th>SKU</th>
                                            <th class="text-center">الكمية</th>
                                            <th>السعر</th>
                                            <th>الإجمالي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                            @php
                                                $thumb = $item->product?->card_image_url ?? $item->product?->primary_image_url;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="order-show-product">
                                                        @if($thumb)
                                                            <img src="{{ $thumb }}" alt="" class="order-show-product__thumb" loading="lazy">
                                                        @else
                                                            <span class="order-show-product__thumb order-show-product__thumb--placeholder">
                                                                <i class="bi bi-image"></i>
                                                            </span>
                                                        @endif
                                                        <div>
                                                            <div class="order-show-product__name">{{ $item->product_name }}</div>
                                                            @if($item->variant_description)
                                                                <div class="order-show-product__variant">{{ $item->variant_description }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><code class="small text-muted">{{ $item->sku ?? '—' }}</code></td>
                                                <td class="text-center"><span class="order-show-qty">{{ $item->quantity }}</span></td>
                                                <td>{{ $currencyService->format((float) $item->unit_price) }}</td>
                                                <td class="order-show-price-total">{{ $currencyService->format((float) $item->total) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- المدفوعات --}}
                    @if($order->payments->isNotEmpty())
                        <div class="order-show-card">
                            <div class="order-show-card__head">
                                <h2 class="order-show-card__title">
                                    <i class="bi bi-credit-card"></i>
                                    المدفوعات
                                </h2>
                            </div>
                            <div class="order-show-card__body">
                                <div class="order-show-payments">
                                    @foreach($order->payments as $pay)
                                        @php
                                            $payMeta = $paymentStatusMap[$pay->status] ?? ['label' => $pay->status, 'class' => 'order-show-badge--muted'];
                                        @endphp
                                        <div class="order-show-payment">
                                            <div class="flex-grow-1">
                                                <div class="order-show-payment__method">{{ $pay->paymentMethod?->name ?? '—' }}</div>
                                                @if($pay->transaction_id)
                                                    <div class="order-show-payment__tx">
                                                        <i class="bi bi-hash"></i> {{ $pay->transaction_id }}
                                                    </div>
                                                @endif
                                                <span class="order-show-badge {{ $payMeta['class'] }} mt-2">{{ $payMeta['label'] }}</span>
                                            </div>
                                            <div class="text-end">
                                                <div class="order-show-payment__amount">
                                                    {{ number_format($pay->amount, 2) }} {{ $pay->currency }}
                                                </div>
                                                <a href="{{ route('admin.payments.show', $pay) }}" class="btn btn-sm btn-outline-primary mt-2">
                                                    التفاصيل
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- بيانات المشتري --}}
                    <div class="order-show-card">
                        <div class="order-show-card__head">
                            <h2 class="order-show-card__title">
                                <i class="bi bi-person-vcard"></i>
                                بيانات المشتري
                            </h2>
                        </div>
                        <div class="order-show-card__body">
                            @if($contact || $order->user)
                                <div class="order-show-customer">
                                    <div class="order-show-customer__avatar">{{ $customerInitial }}</div>
                                    <div>
                                        <div class="order-show-customer__name">
                                            {{ $contact?->full_name ?? $order->user?->name ?? '—' }}
                                        </div>
                                        <div class="order-show-customer__detail">
                                            @if($contact?->phone)
                                                <span><i class="bi bi-telephone"></i> {{ $contact->phone }}</span>
                                            @endif
                                            @if($order->user?->email)
                                                <span><i class="bi bi-envelope"></i> {{ $order->user->email }}</span>
                                            @endif
                                            @if($contact?->city)
                                                <span><i class="bi bi-geo-alt"></i> {{ $contact->city }}</span>
                                            @endif
                                            @if($contact?->address_line_1)
                                                <span><i class="bi bi-house"></i> {{ $contact->address_line_1 }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted mb-0">لا تتوفر بيانات المشتري.</p>
                            @endif
                        </div>
                    </div>

                    {{-- طلبات المرتجع --}}
                    <div class="order-show-card">
                        <div class="order-show-card__head">
                            <h2 class="order-show-card__title">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                طلبات المرتجع
                            </h2>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#createReturnForm">
                                <i class="bi bi-plus-lg me-1"></i> إنشاء مرتجع
                            </button>
                        </div>
                        <div class="order-show-card__body">
                            @php
                                $returnedQty = [];
                                foreach ($order->returns->where('status', 'approved') as $ret) {
                                    foreach ($ret->items as $ri) {
                                        $returnedQty[$ri->order_item_id] = ($returnedQty[$ri->order_item_id] ?? 0) + $ri->quantity;
                                    }
                                }
                            @endphp

                            @if($order->returns->isNotEmpty())
                                @foreach($order->returns as $ret)
                                    <div class="order-show-return-item">
                                        <div>
                                            <span class="order-show-badge @if($ret->status === 'pending') order-show-badge--warning @elseif($ret->status === 'approved') order-show-badge--success @else order-show-badge--danger @endif">
                                                @if($ret->status === 'pending') قيد الانتظار
                                                @elseif($ret->status === 'approved') معتمد
                                                @else مرفوض
                                                @endif
                                            </span>
                                            <span class="ms-2">{{ $ret->reason ? Str::limit($ret->reason, 60) : '—' }}</span>
                                            <div class="small text-muted mt-1">{{ $ret->created_at->format('Y-m-d H:i') }}</div>
                                        </div>
                                        <a href="{{ route('admin.order-returns.show', $ret) }}" class="btn btn-sm btn-primary">عرض</a>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">لا توجد طلبات مرتجع لهذا الطلب.</p>
                            @endif

                            <div class="collapse mt-3" id="createReturnForm">
                                <hr>
                                <h6 class="mb-3 fw-bold">إنشاء طلب مرتجع جديد</h6>
                                <form action="{{ route('admin.order-returns.store', $order) }}" method="POST" class="order-show-form">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">سبب المرتجع (اختياري)</label>
                                        <input type="text" name="reason" class="form-control" placeholder="مثل: عيب، تغيير رأي">
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>المنتج</th>
                                                    <th>في الطلب</th>
                                                    <th>مرتجع مسبقاً</th>
                                                    <th>كمية المرتجع</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $idx = 0; @endphp
                                                @foreach($order->items as $item)
                                                    @php $already = $returnedQty[$item->id] ?? 0; $max = $item->quantity - $already; @endphp
                                                    @if($max > 0)
                                                        <tr>
                                                            <td>
                                                                {{ $item->product_name }}
                                                                @if($item->variant_description)
                                                                    <br><small class="text-muted">{{ $item->variant_description }}</small>
                                                                @endif
                                                            </td>
                                                            <td>{{ $item->quantity }}</td>
                                                            <td>{{ $already }}</td>
                                                            <td>
                                                                <input type="hidden" name="items[{{ $idx }}][order_item_id]" value="{{ $item->id }}">
                                                                <input type="number" name="items[{{ $idx }}][quantity]" min="1" max="{{ $max }}" value="{{ $max }}" class="form-control form-control-sm" style="width: 90px;">
                                                            </td>
                                                        </tr>
                                                        @php $idx++; @endphp
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @php
                                        $hasReturnable = $order->items->contains(function ($item) use ($returnedQty) {
                                            return ($returnedQty[$item->id] ?? 0) < $item->quantity;
                                        });
                                    @endphp
                                    @if($hasReturnable)
                                        <button type="submit" class="btn btn-save text-white">إرسال طلب المرتجع</button>
                                    @else
                                        <p class="text-muted small mb-0">تم إرجاع كل بنود هذا الطلب مسبقاً.</p>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="order-show-summary">
                        {{-- ملخص الأسعار --}}
                        <div class="order-show-card">
                            <div class="order-show-card__head">
                                <h2 class="order-show-card__title">
                                    <i class="bi bi-receipt"></i>
                                    ملخص الأسعار
                                </h2>
                            </div>
                            <div class="order-show-card__body">
                                <div class="order-show-receipt">
                                    <div class="order-show-receipt__row">
                                        <span>المجموع الفرعي</span>
                                        <span>{{ $currencyService->format((float) $order->subtotal) }}</span>
                                    </div>
                                    <div class="order-show-receipt__row">
                                        <span>الضريبة</span>
                                        <span>{{ $currencyService->format((float) $order->tax_amount) }}</span>
                                    </div>
                                    @if($order->discount_amount > 0)
                                        <div class="order-show-receipt__row order-show-receipt__row--discount">
                                            <span>الخصم</span>
                                            <span>-{{ $currencyService->format((float) $order->discount_amount) }}</span>
                                        </div>
                                    @endif
                                    <hr class="order-show-receipt__divider">
                                    <div class="order-show-receipt__total">
                                        <span>الإجمالي</span>
                                        <span>{{ $currencyService->format((float) $order->total) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- تحديث الحالة --}}
                        <div class="order-show-card">
                            <div class="order-show-card__head">
                                <h2 class="order-show-card__title">
                                    <i class="bi bi-sliders"></i>
                                    تحديث الحالة
                                </h2>
                            </div>
                            <div class="order-show-card__body">
                                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="order-show-form">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">حالة الطلب</label>
                                        <select name="order_status_id" class="form-select @error('order_status_id') is-invalid @enderror">
                                            @foreach(\App\Models\OrderStatus::ordered()->get() as $s)
                                                <option value="{{ $s->id }}" {{ $order->order_status_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('order_status_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ملاحظة إدارية (اختياري)</label>
                                        <textarea name="admin_note" rows="3" class="form-control @error('admin_note') is-invalid @enderror" placeholder="سبب تغيير الحالة أو أي ملاحظات داخلية...">{{ old('admin_note', $order->admin_note) }}</textarea>
                                        @error('admin_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <button type="submit" class="btn btn-save text-white w-100">
                                        <i class="bi bi-check2-circle me-1"></i> حفظ الحالة
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if($order->customer_note)
                            <div class="order-show-card">
                                <div class="order-show-card__head">
                                    <h2 class="order-show-card__title">
                                        <i class="bi bi-chat-left-quote"></i>
                                        ملاحظة العميل
                                    </h2>
                                </div>
                                <div class="order-show-card__body">
                                    <div class="order-show-note">{{ $order->customer_note }}</div>
                                </div>
                            </div>
                        @endif

                        @if($order->statusHistory->isNotEmpty())
                            <div class="order-show-card">
                                <div class="order-show-card__head">
                                    <h2 class="order-show-card__title">
                                        <i class="bi bi-clock-history"></i>
                                        سجل الحالة
                                    </h2>
                                </div>
                                <div class="order-show-card__body">
                                    <ul class="order-show-timeline">
                                        @foreach($order->statusHistory as $history)
                                            <li class="order-show-timeline__item">
                                                <span class="order-show-timeline__dot"></span>
                                                <div class="order-show-timeline__status">{{ $history->newStatus->name ?? '—' }}</div>
                                                <div class="order-show-timeline__meta">
                                                    {{ $history->user->name ?? 'النظام' }}
                                                    · {{ $history->created_at->format('Y/m/d H:i') }}
                                                </div>
                                                @if($history->note)
                                                    <div class="order-show-timeline__note">{{ $history->note }}</div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
