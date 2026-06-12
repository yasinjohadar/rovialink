@extends('admin.layouts.master')

@section('page-title')
    ملف العميل {{ $customer->name }}
@stop

@section('styles')
    @include('frontend.layouts.theme-variables')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-order-show.css') }}?v=4">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-orders-index.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-customer-show.css') }}?v=1">
@endsection

@section('content')
    @php
        $customerInitial = $customer->name ? mb_substr($customer->name, 0, 1) : '?';
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
                            <a href="{{ route('admin.customers.index') }}" class="order-show-back mb-3">
                                <i class="bi bi-arrow-right"></i>
                                العودة لقائمة العملاء
                            </a>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="order-show-customer__avatar">{{ $customerInitial }}</div>
                                <div>
                                    <h1 class="order-show-hero__title mb-1">{{ $customer->name }}</h1>
                                    <div class="customer-show-hero-contact">
                                        <span class="customer-show-hero-contact__item" dir="ltr">
                                            <i class="bi bi-envelope"></i>
                                            {{ $customer->email }}
                                            <button type="button"
                                                class="customer-show-hero-copy js-copy-email"
                                                data-email="{{ $customer->email }}"
                                                title="نسخ البريد الإلكتروني"
                                                aria-label="نسخ البريد الإلكتروني">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </span>
                                        @if($customer->phone)
                                            <span class="customer-show-hero-contact__item" dir="ltr">
                                                <i class="bi bi-telephone"></i>
                                                {{ $customer->phone }}
                                            </span>
                                        @endif
                                        <span class="customer-show-hero-contact__item">
                                            <i class="bi bi-calendar3"></i>
                                            عضو منذ {{ $customer->created_at?->format('Y/m/d') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="order-show-status">
                            <i class="bi bi-star-fill"></i>
                            {{ number_format($customer->loyalty_points_balance ?? 0, 0) }} نقطة
                        </span>
                    </div>

                    <div class="order-show-hero__stats">
                        <div class="order-show-stat order-show-stat--highlight">
                            <div class="order-show-stat__label">إجمالي الإنفاق</div>
                            <div class="order-show-stat__value">{{ format_money($totalSpent) }}</div>
                        </div>
                        <div class="order-show-stat">
                            <div class="order-show-stat__label">عدد الطلبات</div>
                            <div class="order-show-stat__value">{{ $ordersCount }}</div>
                        </div>
                        <div class="order-show-stat">
                            <div class="order-show-stat__label">متوسط قيمة الطلب</div>
                            <div class="order-show-stat__value">{{ format_money($averageOrderValue) }}</div>
                        </div>
                        <div class="order-show-stat">
                            <div class="order-show-stat__label">آخر طلب</div>
                            <div class="order-show-stat__value" style="font-size: 0.95rem;">
                                @if($lastOrder)
                                    {{ $lastOrder->created_at->format('Y/m/d') }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="customer-show-insights">
                <div class="customer-show-insight">
                    <div class="customer-show-insight__label">تاريخ التسجيل</div>
                    <p class="customer-show-insight__value">{{ $customer->created_at?->format('Y/m/d — H:i') }}</p>
                </div>
                <div class="customer-show-insight">
                    <div class="customer-show-insight__label">آخر طلب</div>
                    <p class="customer-show-insight__value">
                        @if($lastOrder)
                            {{ $lastOrder->created_at->format('Y/m/d — H:i') }}
                        @else
                            —
                        @endif
                    </p>
                </div>
                <div class="customer-show-insight">
                    <div class="customer-show-insight__label">أكثر منتج شراءً</div>
                    <p class="customer-show-insight__value">
                        @if($topProduct && $topProduct->product)
                            {{ $topProduct->product->name }}
                        @else
                            —
                        @endif
                    </p>
                    @if($topProduct && $topProduct->product)
                        <div class="customer-show-insight__sub">{{ $topProduct->total_qty }} مرة</div>
                    @endif
                </div>
                <div class="customer-show-insight">
                    <div class="customer-show-insight__label">أكثر تصنيف</div>
                    <p class="customer-show-insight__value">
                        @if($topCategory?->product?->category)
                            {{ $topCategory->product->category->name }}
                        @else
                            —
                        @endif
                    </p>
                    @if($topCategory?->product?->category)
                        <div class="customer-show-insight__sub">{{ $topCategory->total_qty }} منتج</div>
                    @endif
                </div>
            </div>

            <div class="order-show-card customer-show-tabs-card">
                <div class="order-show-card__head border-bottom-0 pb-0">
                    <ul class="nav nav-tabs card-header-tabs w-100" id="customerTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview"
                                type="button" role="tab" aria-controls="overview" aria-selected="true">
                                <i class="bi bi-grid me-1"></i> نظرة عامة
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders"
                                type="button" role="tab" aria-controls="orders" aria-selected="false">
                                <i class="bi bi-bag me-1"></i> الطلبات
                                <span class="badge rounded-pill bg-secondary ms-1">{{ $ordersCount }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="addresses-tab" data-bs-toggle="tab" data-bs-target="#addresses"
                                type="button" role="tab" aria-controls="addresses" aria-selected="false">
                                <i class="bi bi-geo-alt me-1"></i> العناوين
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes"
                                type="button" role="tab" aria-controls="notes" aria-selected="false">
                                <i class="bi bi-journal-text me-1"></i> الملاحظات
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="loyalty-tab" data-bs-toggle="tab" data-bs-target="#loyalty"
                                type="button" role="tab" aria-controls="loyalty" aria-selected="false">
                                <i class="bi bi-star me-1"></i> نقاط الولاء
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="customerTabsContent">
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        <div class="order-show-card__head">
                            <h2 class="order-show-card__title">
                                <i class="bi bi-clock-history"></i>
                                آخر الطلبات
                            </h2>
                            @if($ordersCount > 5)
                                <button type="button" class="customer-show-btn-outline" data-bs-toggle="tab" data-bs-target="#orders">
                                    عرض الكل
                                </button>
                            @endif
                        </div>
                        <div class="order-show-card__body order-show-card__body--flush">
                            @include('admin.pages.customers.partials.orders-table', ['orders' => $orders->take(5)])
                        </div>
                    </div>

                    <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                        <div class="order-show-card__head">
                            <h2 class="order-show-card__title">
                                <i class="bi bi-bag-check"></i>
                                جميع الطلبات
                            </h2>
                            <span class="order-show-badge order-show-badge--muted">{{ $ordersCount }} طلب</span>
                        </div>
                        <div class="order-show-card__body order-show-card__body--flush">
                            @include('admin.pages.customers.partials.orders-table', ['orders' => $orders])
                        </div>
                    </div>

                    <div class="tab-pane fade" id="addresses" role="tabpanel" aria-labelledby="addresses-tab">
                        <div class="order-show-card__head">
                            <h2 class="order-show-card__title">
                                <i class="bi bi-geo-alt"></i>
                                عناوين العميل
                            </h2>
                            <button class="customer-show-btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#newAddressForm">
                                <i class="bi bi-plus-lg me-1"></i> إضافة عنوان
                            </button>
                        </div>
                        <div class="order-show-card__body">
                            <div id="newAddressForm" class="collapse mb-4">
                                <form action="{{ route('admin.customers.addresses.store', $customer) }}" method="POST" class="order-show-form">
                                    @csrf
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <label class="form-label">النوع</label>
                                            <select name="type" class="form-select" required>
                                                <option value="billing">فاتورة / تواصل</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">الاسم</label>
                                            <input type="text" name="name" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">الجوال</label>
                                            <input type="text" name="phone" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">المدينة</label>
                                            <input type="text" name="city" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row g-2 mt-2">
                                        <div class="col-md-3">
                                            <label class="form-label">الدولة (رمز)</label>
                                            <input type="text" name="country" class="form-control" maxlength="2" placeholder="SA">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">المنطقة / الولاية</label>
                                            <input type="text" name="state" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">الرمز البريدي</label>
                                            <input type="text" name="postal_code" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">عنوان 1</label>
                                            <input type="text" name="address_line_1" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row g-2 mt-2">
                                        <div class="col-md-9">
                                            <label class="form-label">عنوان 2 (اختياري)</label>
                                            <input type="text" name="address_line_2" class="form-control">
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="newAddressIsDefault" name="is_default">
                                                <label class="form-check-label" for="newAddressIsDefault">جعل العنوان افتراضي</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-save">حفظ العنوان</button>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table class="table order-show-items mb-0">
                                    <thead>
                                        <tr>
                                            <th>النوع</th>
                                            <th>الاسم</th>
                                            <th>الجوال</th>
                                            <th>العنوان</th>
                                            <th>افتراضي</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($customer->addresses as $address)
                                            <tr>
                                                <td>{{ $address->type === 'billing' ? 'فاتورة / تواصل' : $address->type }}</td>
                                                <td class="fw-semibold">{{ $address->name ?? '—' }}</td>
                                                <td dir="ltr">{{ $address->phone ?? '—' }}</td>
                                                <td class="small">
                                                    {{ $address->address_line_1 }}
                                                    @if($address->address_line_2), {{ $address->address_line_2 }}@endif
                                                    @if($address->city), {{ $address->city }}@endif
                                                    @if($address->state), {{ $address->state }}@endif
                                                    @if($address->postal_code), {{ $address->postal_code }}@endif
                                                    @if($address->country), {{ $address->country }}@endif
                                                </td>
                                                <td>
                                                    @if($address->is_default)
                                                        <span class="order-show-badge order-show-badge--success">افتراضي</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-end text-nowrap">
                                                    @if(!$address->is_default)
                                                        <form action="{{ route('admin.customers.addresses.update', [$customer, $address]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="type" value="{{ $address->type }}">
                                                            <input type="hidden" name="name" value="{{ $address->name }}">
                                                            <input type="hidden" name="phone" value="{{ $address->phone }}">
                                                            <input type="hidden" name="country" value="{{ $address->country }}">
                                                            <input type="hidden" name="city" value="{{ $address->city }}">
                                                            <input type="hidden" name="state" value="{{ $address->state }}">
                                                            <input type="hidden" name="postal_code" value="{{ $address->postal_code }}">
                                                            <input type="hidden" name="address_line_1" value="{{ $address->address_line_1 }}">
                                                            <input type="hidden" name="address_line_2" value="{{ $address->address_line_2 }}">
                                                            <input type="hidden" name="is_default" value="1">
                                                            <button type="submit" class="customer-show-btn-outline btn-sm">تعيين كافتراضي</button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('admin.customers.addresses.destroy', [$customer, $address]) }}" method="POST" class="d-inline ms-1"
                                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا العنوان؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">
                                                    <div class="orders-index-empty py-4">
                                                        <i class="bi bi-geo-alt d-block"></i>
                                                        <p class="mb-0">لا توجد عناوين محفوظة لهذا العميل.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                        <div class="order-show-card__body">
                            <div class="row g-4">
                                <div class="col-lg-5">
                                    <h2 class="order-show-card__title mb-3">
                                        <i class="bi bi-pencil-square"></i>
                                        إضافة ملاحظة داخلية
                                    </h2>
                                    <form action="{{ route('admin.customers.notes.store', $customer) }}" method="POST" class="order-show-form">
                                        @csrf
                                        <div class="mb-3">
                                            <textarea name="note" rows="4" class="form-control @error('note') is-invalid @enderror"
                                                placeholder="اكتب ملاحظاتك الداخلية عن هذا العميل (لا تظهر للعميل)...">{{ old('note') }}</textarea>
                                            @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <button type="submit" class="btn btn-save">حفظ الملاحظة</button>
                                    </form>
                                </div>
                                <div class="col-lg-7">
                                    <h2 class="order-show-card__title mb-3">
                                        <i class="bi bi-journal-text"></i>
                                        سجل الملاحظات
                                    </h2>
                                    @if($customer->notes->isEmpty())
                                        <p class="text-muted">لا توجد ملاحظات بعد.</p>
                                    @else
                                        @foreach($customer->notes->sortByDesc('created_at') as $note)
                                            <div class="customer-show-note-item">
                                                <div class="customer-show-note-item__head">
                                                    <span class="customer-show-note-item__author">{{ $note->admin->name ?? 'النظام' }}</span>
                                                    <span class="customer-show-note-item__date">{{ $note->created_at->format('Y/m/d — H:i') }}</span>
                                                </div>
                                                <p class="customer-show-note-item__body">{{ $note->note }}</p>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="loyalty" role="tabpanel" aria-labelledby="loyalty-tab">
                        <div class="order-show-card__body">
                            <div class="row g-4">
                                <div class="col-lg-5">
                                    <div class="customer-show-loyalty-balance">
                                        <span class="customer-show-loyalty-balance__value">{{ number_format($customer->loyalty_points_balance ?? 0, 0) }}</span>
                                        <span class="customer-show-loyalty-balance__label">نقطة</span>
                                    </div>
                                    <h2 class="order-show-card__title mb-3">
                                        <i class="bi bi-sliders"></i>
                                        تعديل النقاط يدوياً
                                    </h2>
                                    <form action="{{ route('admin.customers.loyalty.adjust', $customer) }}" method="POST" class="order-show-form">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">القيمة (موجب للإضافة، سالب للخصم)</label>
                                            <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                                placeholder="مثال: 50 أو -20" value="{{ old('amount') }}" required>
                                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">الوصف / السبب</label>
                                            <input type="text" name="description" class="form-control @error('description') is-invalid @enderror"
                                                placeholder="مثال: مكافأة أو تصحيح رصيد" value="{{ old('description') }}" required maxlength="500">
                                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <button type="submit" class="btn btn-save">تنفيذ التعديل</button>
                                    </form>
                                </div>
                                <div class="col-lg-7">
                                    <h2 class="order-show-card__title mb-3">
                                        <i class="bi bi-clock-history"></i>
                                        آخر حركات النقاط
                                    </h2>
                                    @if($customer->loyaltyPointTransactions->isEmpty())
                                        <p class="text-muted">لا توجد حركات نقاط بعد.</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table order-show-items mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>التاريخ</th>
                                                        <th>النوع</th>
                                                        <th>القيمة</th>
                                                        <th>الوصف</th>
                                                        <th>الطلب</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($customer->loyaltyPointTransactions as $tx)
                                                        <tr>
                                                            <td class="small text-muted">{{ $tx->created_at->format('Y/m/d — H:i') }}</td>
                                                            <td>
                                                                @if($tx->type === 'earn')
                                                                    <span class="order-show-badge order-show-badge--success">كسب</span>
                                                                @elseif($tx->type === 'redeem')
                                                                    <span class="order-show-badge order-show-badge--warning">استبدال</span>
                                                                @else
                                                                    <span class="order-show-badge order-show-badge--muted">تعديل يدوي</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="fw-bold {{ $tx->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                                    {{ $tx->amount >= 0 ? '+' : '' }}{{ $tx->amount }}
                                                                </span>
                                                            </td>
                                                            <td class="small">{{ $tx->description ?? '—' }}</td>
                                                            <td>
                                                                @if($tx->order_id)
                                                                    <a href="{{ route('admin.orders.show', $tx->order_id) }}" class="text-decoration-none">
                                                                        #{{ $tx->order->order_number ?? $tx->order_id }}
                                                                    </a>
                                                                @else
                                                                    —
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.js-copy-email');
        if (!btn) return;
        e.preventDefault();

        const email = btn.dataset.email;
        if (!email) return;

        const onSuccess = function() {
            const icon = btn.querySelector('i');
            if (!icon) return;
            const original = icon.className;
            icon.className = 'bi bi-check2';
            btn.classList.add('is-copied');
            setTimeout(function() {
                icon.className = original;
                btn.classList.remove('is-copied');
            }, 1600);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(email).then(onSuccess).catch(function() {
                fallbackCopy(email, onSuccess);
            });
        } else {
            fallbackCopy(email, onSuccess);
        }
    });

    function fallbackCopy(text, onSuccess) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            onSuccess();
        } catch (err) {
            alert('تعذر نسخ البريد الإلكتروني');
        }
        document.body.removeChild(textarea);
    }
});
</script>
@stop
