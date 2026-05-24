@extends('admin.layouts.master')

@section('page-title')
    ملف العميل {{ $customer->name }}
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

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">ملف العميل {{ $customer->name }}</h5>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">العودة لقائمة العملاء</a>
            </div>

            <div class="row mb-3">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $customer->name }}</h6>
                                <div class="text-muted small">
                                    {{ $customer->email }} @if($customer->phone) • {{ $customer->phone }} @endif
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">إجمالي الإنفاق: {{ format_money($totalSpent) }}</span>
                                <div class="small text-muted mt-1">
                                    عدد الطلبات: {{ $ordersCount }}<br>
                                    متوسط قيمة الطلب: {{ format_money($averageOrderValue) }}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <p class="mb-1 text-muted">تاريخ التسجيل</p>
                                    <p class="mb-0 fw-bold">{{ $customer->created_at?->format('Y-m-d H:i') }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1 text-muted">آخر طلب</p>
                                    <p class="mb-0 fw-bold">
                                        @if($lastOrder)
                                            {{ $lastOrder->created_at->format('Y-m-d H:i') }}
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1 text-muted">أكثر منتج شراءً</p>
                                    <p class="mb-0">
                                        @if($topProduct && $topProduct->product)
                                            {{ $topProduct->product->name }}<br>
                                            <small class="text-muted">({{ $topProduct->total_qty }} مرة)</small>
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1 text-muted">أكثر تصنيف</p>
                                    <p class="mb-0">
                                        @if($topCategory && $topCategory->category)
                                            {{ $topCategory->category->name }}<br>
                                            <small class="text-muted">({{ $topCategory->total_qty }} منتج)</small>
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-bottom-0">
                    <ul class="nav nav-tabs card-header-tabs" id="customerTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview"
                                    type="button" role="tab" aria-controls="overview" aria-selected="true">
                                نظرة عامة
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders"
                                    type="button" role="tab" aria-controls="orders" aria-selected="false">
                                الطلبات
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="addresses-tab" data-bs-toggle="tab" data-bs-target="#addresses"
                                    type="button" role="tab" aria-controls="addresses" aria-selected="false">
                                العناوين
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes"
                                    type="button" role="tab" aria-controls="notes" aria-selected="false">
                                الملاحظات الداخلية
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="loyalty-tab" data-bs-toggle="tab" data-bs-target="#loyalty"
                                    type="button" role="tab" aria-controls="loyalty" aria-selected="false">
                                نقاط الولاء
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content" id="customerTabsContent">
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        <h6 class="mb-3">آخر الطلبات</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>رقم الطلب</th>
                                    <th>الحالة</th>
                                    <th>الإجمالي</th>
                                    <th>التاريخ</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($orders->take(5) as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->order_number }}</td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $order->status->color ?? '#6c757d' }}">
                                                {{ $order->status->name ?? '—' }}
                                            </span>
                                        </td>
                                        <td>{{ format_money($order->total) }}</td>
                                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">لا توجد طلبات لهذا العميل بعد.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                        <h6 class="mb-3">جميع الطلبات</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>رقم الطلب</th>
                                    <th>الحالة</th>
                                    <th>الإجمالي</th>
                                    <th>التاريخ</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->order_number }}</td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $order->status->color ?? '#6c757d' }}">
                                                {{ $order->status->name ?? '—' }}
                                            </span>
                                        </td>
                                        <td>{{ format_money($order->total) }}</td>
                                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">لا توجد طلبات لهذا العميل بعد.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="addresses" role="tabpanel" aria-labelledby="addresses-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">عناوين العميل</h6>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#newAddressForm">
                                إضافة عنوان جديد
                            </button>
                        </div>

                        <div id="newAddressForm" class="collapse mb-3">
                            <form action="{{ route('admin.customers.addresses.store', $customer) }}" method="POST">
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
                                            <label class="form-check-label" for="newAddressIsDefault">
                                                جعل العنوان افتراضي
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success">حفظ العنوان</button>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
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
                                        <td>{{ $address->name ?? '—' }}</td>
                                        <td>{{ $address->phone ?? '—' }}</td>
                                        <td>
                                            {{ $address->address_line_1 }}
                                            @if($address->address_line_2)
                                                , {{ $address->address_line_2 }}
                                            @endif
                                            @if($address->city)
                                                , {{ $address->city }}
                                            @endif
                                            @if($address->state)
                                                , {{ $address->state }}
                                            @endif
                                            @if($address->postal_code)
                                                , {{ $address->postal_code }}
                                            @endif
                                            @if($address->country)
                                                , {{ $address->country }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($address->is_default)
                                                <span class="badge bg-success">افتراضي</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
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
                                                @if(!$address->is_default)
                                                    <input type="hidden" name="is_default" value="1">
                                                    <button type="submit" class="btn btn-sm btn-outline-success">تعيين كافتراضي</button>
                                                @endif
                                            </form>

                                            <form action="{{ route('admin.customers.addresses.destroy', [$customer, $address]) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('هل أنت متأكد من حذف هذا العنوان؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">لا توجد عناوين محفوظة لهذا العميل.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="mb-3">إضافة ملاحظة داخلية</h6>
                                <form action="{{ route('admin.customers.notes.store', $customer) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea name="note" rows="4" class="form-control @error('note') is-invalid @enderror" placeholder="اكتب ملاحظاتك الداخلية عن هذا العميل (لا تظهر للعميل)...">{{ old('note') }}</textarea>
                                        @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary">حفظ الملاحظة</button>
                                </form>
                            </div>
                            <div class="col-lg-6">
                                <h6 class="mb-3">سجل الملاحظات</h6>
                                @if($customer->notes->isEmpty())
                                    <p class="text-muted">لا توجد ملاحظات بعد.</p>
                                @else
                                    <ul class="list-group list-group-flush">
                                        @foreach($customer->notes->sortByDesc('created_at') as $note)
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong>{{ $note->admin->name ?? 'النظام' }}</strong>
                                                        <div class="small text-muted">{{ $note->created_at->format('Y-m-d H:i') }}</div>
                                                    </div>
                                                </div>
                                                <p class="mb-0 mt-2 small">{{ $note->note }}</p>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="loyalty" role="tabpanel" aria-labelledby="loyalty-tab">
                        <div class="row">
                            <div class="col-lg-5">
                                <h6 class="mb-3">رصيد النقاط الحالي</h6>
                                <p class="fs-4 mb-4"><strong>{{ number_format($customer->loyalty_points_balance ?? 0, 0) }}</strong> نقطة</p>
                                <h6 class="mb-3">تعديل النقاط يدوياً</h6>
                                <form action="{{ route('admin.customers.loyalty.adjust', $customer) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">القيمة (موجب للإضافة، سالب للخصم)</label>
                                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="مثال: 50 أو -20" value="{{ old('amount') }}" required>
                                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">الوصف / السبب</label>
                                        <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" placeholder="مثال: مكافأة أو تصحيح رصيد" value="{{ old('description') }}" required maxlength="500">
                                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary">تنفيذ التعديل</button>
                                </form>
                            </div>
                            <div class="col-lg-7">
                                <h6 class="mb-3">آخر حركات النقاط</h6>
                                @if($customer->loyaltyPointTransactions->isEmpty())
                                    <p class="text-muted">لا توجد حركات نقاط بعد.</p>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle table-sm mb-0">
                                            <thead class="table-light">
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
                                                        <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                                                        <td>
                                                            @if($tx->type === 'earn')
                                                                <span class="badge bg-success">كسب</span>
                                                            @elseif($tx->type === 'redeem')
                                                                <span class="badge bg-info">استبدال</span>
                                                            @else
                                                                <span class="badge bg-secondary">تعديل يدوي</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="{{ $tx->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                                {{ $tx->amount >= 0 ? '+' : '' }}{{ $tx->amount }}
                                                            </span>
                                                        </td>
                                                        <td class="small">{{ $tx->description ?? '—' }}</td>
                                                        <td>
                                                            @if($tx->order_id)
                                                                <a href="{{ route('admin.orders.show', $tx->order_id) }}">#{{ $tx->order->order_number ?? $tx->order_id }}</a>
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
@stop

