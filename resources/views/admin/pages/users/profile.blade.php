@extends('admin.layouts.master')

@section('page-title')
    ملف المستخدم {{ $user->name }}
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    @php
        $statusLabels = [
            'active' => ['label' => 'نشط', 'class' => 'bg-success'],
            'inactive' => ['label' => 'غير نشط', 'class' => 'bg-secondary'],
            'banned' => ['label' => 'محظور', 'class' => 'bg-danger'],
        ];
        $userStatus = $statusLabels[$user->status] ?? ['label' => $user->status, 'class' => 'bg-secondary'];
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2 my-4">
                <h5 class="page-title mb-0">ملف المستخدم: {{ $user->name }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm">تعديل</a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">العودة للقائمة</a>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            @if ($user->photoUrl())
                                <img src="{{ $user->photoUrl() }}" alt="{{ $user->name }}"
                                    class="rounded-circle mb-3" width="96" height="96" style="object-fit: cover;">
                            @else
                                <div class="avatar avatar-xl bg-primary-transparent text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                    style="width: 96px; height: 96px; font-size: 2rem;">
                                    {{ mb_substr($user->name, 0, 1) }}
                                </div>
                            @endif
                            <h6 class="mb-1">{{ $user->name }}</h6>
                            @if ($user->username)
                                <p class="text-muted small mb-2">{{ $user->username }}</p>
                            @endif
                            <span class="badge {{ $userStatus['class'] }}">{{ $userStatus['label'] }}</span>
                            @if ($user->is_active)
                                <span class="badge bg-success-transparent text-success ms-1">مفعّل</span>
                            @else
                                <span class="badge bg-secondary-transparent text-secondary ms-1">معطّل</span>
                            @endif
                            <div class="mt-3">
                                @foreach ($user->roles as $role)
                                    <span class="badge bg-primary-transparent text-primary me-1">{{ $role->name }}</span>
                                @endforeach
                                @if ($user->roles->isEmpty())
                                    <span class="text-muted small">بدون أدوار</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <p class="text-muted small mb-1">عدد الطلبات</p>
                                    <h4 class="mb-0">{{ $ordersCount }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <p class="text-muted small mb-1">إجمالي الإنفاق</p>
                                    <h4 class="mb-0 fs-6">{{ $currencyService->format($totalSpent) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <p class="text-muted small mb-1">متوسط الطلب</p>
                                    <h4 class="mb-0 fs-6">{{ $currencyService->format($averageOrderValue) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <p class="text-muted small mb-1">نقاط الولاء</p>
                                    <h4 class="mb-0">{{ number_format($user->loyalty_points_balance ?? 0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header border-bottom-0">
                            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#user-details-tab" type="button">
                                        البيانات
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#user-orders-tab" type="button">
                                        الطلبات ({{ $ordersCount }})
                                    </button>
                                </li>
                                @if ($user->addresses->isNotEmpty())
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#user-addresses-tab" type="button">
                                            العناوين
                                        </button>
                                    </li>
                                @endif
                                @if ($user->loyaltyPointTransactions->isNotEmpty())
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#user-loyalty-tab" type="button">
                                            نقاط الولاء
                                        </button>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="card-body tab-content">
                            <div class="tab-pane fade show active" id="user-details-tab">
                                <div class="row g-3">
                                    <div class="col-md-6 col-lg-4">
                                        <p class="text-muted small mb-1">البريد الإلكتروني</p>
                                        <p class="mb-0 fw-semibold">{{ $user->email }}</p>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <p class="text-muted small mb-1">الهاتف</p>
                                        <p class="mb-0 fw-semibold">{{ $user->phone ?? '—' }}</p>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <p class="text-muted small mb-1">تاريخ التسجيل</p>
                                        <p class="mb-0 fw-semibold">{{ $user->created_at?->format('Y-m-d H:i') ?? '—' }}</p>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <p class="text-muted small mb-1">آخر تسجيل دخول</p>
                                        <p class="mb-0 fw-semibold">{{ $user->last_login_at?->format('Y-m-d H:i') ?? '—' }}</p>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <p class="text-muted small mb-1">آخر طلب</p>
                                        <p class="mb-0 fw-semibold">
                                            @if ($lastOrder)
                                                {{ $lastOrder->created_at->format('Y-m-d H:i') }}
                                            @else
                                                —
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <p class="text-muted small mb-1">تأكيد البريد</p>
                                        <p class="mb-0 fw-semibold">
                                            {{ $user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i') : 'غير مؤكد' }}
                                        </p>
                                    </div>
                                    @if ($topProduct?->product)
                                        <div class="col-md-6 col-lg-4">
                                            <p class="text-muted small mb-1">أكثر منتج شراءً</p>
                                            <p class="mb-0">{{ $topProduct->product->name }} <span class="text-muted">({{ $topProduct->total_qty }})</span></p>
                                        </div>
                                    @endif
                                    @if ($topCategory?->product?->category)
                                        <div class="col-md-6 col-lg-4">
                                            <p class="text-muted small mb-1">أكثر تصنيف</p>
                                            <p class="mb-0">{{ $topCategory->product->category->name }} <span class="text-muted">({{ $topCategory->total_qty }})</span></p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="tab-pane fade" id="user-orders-tab">
                                <p class="text-muted small mb-3">انقر على صف الطلب لعرض التفاصيل، أو «عرض كامل» لفتح صفحة الطلب.</p>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 32px;"></th>
                                                <th>رقم الطلب</th>
                                                <th>الحالة</th>
                                                <th>الإجمالي</th>
                                                <th>التاريخ</th>
                                                <th>عمليات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($orders as $order)
                                                <tr class="user-order-toggle" role="button" data-bs-toggle="collapse"
                                                    data-bs-target="#user-order-detail-{{ $order->id }}"
                                                    aria-expanded="false" aria-controls="user-order-detail-{{ $order->id }}">
                                                    <td><i class="bi bi-chevron-down text-muted"></i></td>
                                                    <td><strong>{{ $order->order_number }}</strong></td>
                                                    <td>
                                                        <span class="badge" style="background-color: {{ $order->status?->color ?? '#6c757d' }}">
                                                            {{ $order->status?->name ?? '—' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $currencyService->format((float) $order->total) }}</td>
                                                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary"
                                                            onclick="event.stopPropagation();">عرض كامل</a>
                                                    </td>
                                                </tr>
                                                <tr class="collapse bg-light" id="user-order-detail-{{ $order->id }}">
                                                    <td colspan="6" class="p-0">
                                                        <div class="p-3 border-top">
                                                            <div class="row g-3 mb-3">
                                                                <div class="col-md-3">
                                                                    <span class="text-muted small d-block">المجموع الفرعي</span>
                                                                    <strong>{{ $currencyService->format((float) $order->subtotal) }}</strong>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <span class="text-muted small d-block">الضريبة</span>
                                                                    <strong>{{ $currencyService->format((float) $order->tax_amount) }}</strong>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <span class="text-muted small d-block">الخصم</span>
                                                                    <strong>{{ $currencyService->format((float) $order->discount_amount) }}</strong>
                                                                </div>
                                                                @if ($order->coupon_code)
                                                                    <div class="col-md-3">
                                                                        <span class="text-muted small d-block">كوبون</span>
                                                                        <strong>{{ $order->coupon_code }}</strong>
                                                                    </div>
                                                                @endif
                                                                @if ($order->customer_note)
                                                                    <div class="col-12">
                                                                        <span class="text-muted small d-block">ملاحظة العميل</span>
                                                                        {{ $order->customer_note }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <h6 class="mb-2">عناصر الطلب</h6>
                                                            <table class="table table-sm table-bordered mb-0 bg-white">
                                                                <thead>
                                                                    <tr>
                                                                        <th>المنتج</th>
                                                                        <th>SKU</th>
                                                                        <th>الكمية</th>
                                                                        <th>السعر</th>
                                                                        <th>الإجمالي</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($order->items as $item)
                                                                        <tr>
                                                                            <td>
                                                                                {{ $item->product_name }}
                                                                                @if ($item->variant_description)
                                                                                    <br><small class="text-muted">{{ $item->variant_description }}</small>
                                                                                @endif
                                                                            </td>
                                                                            <td>{{ $item->sku ?? '—' }}</td>
                                                                            <td>{{ $item->quantity }}</td>
                                                                            <td>{{ $currencyService->format((float) $item->unit_price) }}</td>
                                                                            <td>{{ $currencyService->format((float) $item->total) }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">لا توجد طلبات لهذا المستخدم.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            @if ($user->addresses->isNotEmpty())
                                <div class="tab-pane fade" id="user-addresses-tab">
                                    <div class="row g-3">
                                        @foreach ($user->addresses as $address)
                                            <div class="col-md-6">
                                                <div class="border rounded p-3 h-100">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span class="badge bg-light text-dark">{{ $address->type === 'billing' ? 'فاتورة' : 'شحن' }}</span>
                                                        @if ($address->is_default)
                                                            <span class="badge bg-primary">افتراضي</span>
                                                        @endif
                                                    </div>
                                                    <p class="mb-1 fw-semibold">{{ $address->name ?? $user->name }}</p>
                                                    <p class="mb-1 small">{{ $address->phone }}</p>
                                                    <p class="mb-0 small text-muted">
                                                        {{ $address->address_line_1 }}
                                                        @if ($address->address_line_2), {{ $address->address_line_2 }} @endif
                                                        <br>{{ $address->city }}@if ($address->state), {{ $address->state }}@endif
                                                        {{ $address->postal_code }}
                                                        @if ($address->country) — {{ $address->country }} @endif
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($user->loyaltyPointTransactions->isNotEmpty())
                                <div class="tab-pane fade" id="user-loyalty-tab">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>التاريخ</th>
                                                    <th>النوع</th>
                                                    <th>النقاط</th>
                                                    <th>الطلب</th>
                                                    <th>الوصف</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($user->loyaltyPointTransactions as $tx)
                                                    <tr>
                                                        <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                                                        <td>{{ $tx->type }}</td>
                                                        <td>{{ $tx->amount > 0 ? '+' : '' }}{{ $tx->amount }}</td>
                                                        <td>
                                                            @if ($tx->order)
                                                                <a href="{{ route('admin.orders.show', $tx->order) }}">{{ $tx->order->order_number }}</a>
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                        <td>{{ $tx->description ?? '—' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
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
    document.querySelectorAll('.user-order-toggle').forEach(function(row) {
        const targetId = row.getAttribute('data-bs-target');
        if (!targetId) return;
        const detail = document.querySelector(targetId);
        if (!detail) return;
        detail.addEventListener('show.bs.collapse', function() {
            row.querySelector('.bi-chevron-down')?.classList.replace('bi-chevron-down', 'bi-chevron-up');
        });
        detail.addEventListener('hide.bs.collapse', function() {
            row.querySelector('.bi-chevron-up')?.classList.replace('bi-chevron-up', 'bi-chevron-down');
        });
    });
});
</script>
@stop
