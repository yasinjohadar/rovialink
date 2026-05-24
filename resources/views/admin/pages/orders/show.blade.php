@extends('admin.layouts.master')

@section('page-title')
    طلب {{ $order->order_number }}
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">طلب {{ $order->order_number }}</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>

            <div class="row mb-3">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">موجز الطلب</h6>
                            <span class="badge" style="background-color: {{ $order->status?->color ?? '#6c757d' }}">{{ $order->status?->name ?? 'غير معروف' }}</span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <p class="mb-1 text-muted">الإجمالي</p>
                                    <p class="mb-0 fw-bold">{{ $currencyService->format((float) $order->total) }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1 text-muted">الكوبون</p>
                                    <p class="mb-0">{{ $order->coupon_code ? $order->coupon_code : '—' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1 text-muted">إنشاء الطلب</p>
                                    <p class="mb-0">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($order->payments->isNotEmpty())
                    <div class="card mb-3">
                        <div class="card-header"><h6 class="mb-0">المدفوعات</h6></div>
                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <thead class="table-light"><tr><th>الوسيلة</th><th>المبلغ</th><th>الحالة</th><th>المعاملة</th><th></th></tr></thead>
                                <tbody>
                                    @foreach($order->payments as $pay)
                                    <tr>
                                        <td>{{ $pay->paymentMethod?->name ?? '—' }}</td>
                                        <td>{{ number_format($pay->amount, 2) }} {{ $pay->currency }}</td>
                                        <td>{{ $pay->status }}</td>
                                        <td><code class="small">{{ $pay->transaction_id ?? '—' }}</code></td>
                                        <td><a href="{{ route('admin.payments.show', $pay) }}" class="btn btn-sm btn-outline-primary">تفاصيل</a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">عناصر الطلب</h6>
                            <span class="badge" style="background-color: {{ $order->status?->color ?? '#6c757d' }}">{{ $order->status?->name ?? 'غير معروف' }}</span>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>المنتج</th>
                                        <th>SKU</th>
                                        <th>الكمية</th>
                                        <th>السعر</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                {{ $item->product_name }}
                                                @if($item->variant_description)
                                                    <br><small class="text-muted">{{ $item->variant_description }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $item->sku ?? '-' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $currencyService->format((float) $item->unit_price) }}</td>
                                            <td>{{ $currencyService->format((float) $item->total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header"><h6 class="mb-0">بيانات المشتري</h6></div>
                        <div class="card-body">
                            @if($order->contact_address)
                                <p class="mb-0">{{ $order->contact_address->full_name }}</p>
                                @if($order->contact_address->address_line_2)
                                    <p class="mb-0 text-muted small">{{ $order->contact_address->address_line_2 }}</p>
                                @endif
                                <p class="mb-0">{{ $order->contact_address->phone }}</p>
                            @else
                                <p class="text-muted mb-0">—</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header"><h6 class="mb-0">ملخص الأسعار</h6></div>
                        <div class="card-body">
                            <p class="d-flex justify-content-between"><span>المجموع الفرعي</span><span>{{ $currencyService->format((float) $order->subtotal) }}</span></p>
                            <p class="d-flex justify-content-between"><span>الضريبة</span><span>{{ $currencyService->format((float) $order->tax_amount) }}</span></p>
                            @if($order->discount_amount > 0)
                                <p class="d-flex justify-content-between text-success"><span>الخصم</span><span>-{{ $currencyService->format((float) $order->discount_amount) }}</span></p>
                            @endif
                            <hr>
                            <p class="d-flex justify-content-between fw-bold"><span>الإجمالي</span><span>{{ $currencyService->format((float) $order->total) }}</span></p>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header"><h6 class="mb-0">تحديث الحالة وملاحظة إدارية</h6></div>
                        <div class="card-body">
                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label mb-1">حالة الطلب</label>
                                    <select name="order_status_id" class="form-select @error('order_status_id') is-invalid @enderror">
                                    @foreach(\App\Models\OrderStatus::ordered()->get() as $s)
                                        <option value="{{ $s->id }}" {{ $order->order_status_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                    </select>
                                    @error('order_status_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label mb-1">ملاحظة إدارية (اختياري)</label>
                                    <textarea name="admin_note" rows="3" class="form-control @error('admin_note') is-invalid @enderror" placeholder="سبب تغيير الحالة أو أي ملاحظات داخلية...">{{ old('admin_note', $order->admin_note) }}</textarea>
                                    @error('admin_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <button type="submit" class="btn btn-primary w-100">حفظ الحالة</button>
                            </form>
                        </div>
                    </div>

                    @if($order->customer_note)
                        <div class="card mt-3">
                            <div class="card-header"><h6 class="mb-0">ملاحظة العميل</h6></div>
                            <div class="card-body"><p class="mb-0">{{ $order->customer_note }}</p></div>
                        </div>
                    @endif

                    @if($order->statusHistory->isNotEmpty())
                        <div class="card mt-3">
                            <div class="card-header"><h6 class="mb-0">سجل الحالة (Timeline)</h6></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach($order->statusHistory as $history)
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong>{{ $history->newStatus->name ?? '—' }}</strong>
                                                    <div class="small text-muted">
                                                        بواسطة {{ $history->user->name ?? 'النظام' }}
                                                    </div>
                                                </div>
                                                <span class="small text-muted">{{ $history->created_at->format('Y-m-d H:i') }}</span>
                                            </div>
                                            @if($history->note)
                                                <p class="mb-0 mt-2 small text-muted">{{ $history->note }}</p>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- طلبات المرتجع --}}
                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">طلبات المرتجع</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#createReturnForm">إنشاء طلب مرتجع</button>
                        </div>
                        <div class="card-body">
                            @php
                                $returnedQty = [];
                                foreach ($order->returns->where('status', 'approved') as $ret) {
                                    foreach ($ret->items as $ri) {
                                        $returnedQty[$ri->order_item_id] = ($returnedQty[$ri->order_item_id] ?? 0) + $ri->quantity;
                                    }
                                }
                            @endphp

                            @if($order->returns->isNotEmpty())
                                <ul class="list-group list-group-flush mb-3">
                                    @foreach($order->returns as $ret)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge @if($ret->status === 'pending') bg-warning text-dark @elseif($ret->status === 'approved') bg-success @else bg-danger @endif">
                                                    @if($ret->status === 'pending') قيد الانتظار
                                                    @elseif($ret->status === 'approved') معتمد
                                                    @else مرفوض
                                                    @endif
                                                </span>
                                                <span class="ms-2">{{ $ret->reason ? Str::limit($ret->reason, 50) : '—' }}</span>
                                                <span class="text-muted small ms-2">{{ $ret->created_at->format('Y-m-d H:i') }}</span>
                                            </div>
                                            <a href="{{ route('admin.order-returns.show', $ret) }}" class="btn btn-sm btn-primary">عرض</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted mb-3">لا توجد طلبات مرتجع لهذا الطلب.</p>
                            @endif

                            <div class="collapse" id="createReturnForm">
                                <hr>
                                <h6 class="mb-2">إنشاء طلب مرتجع جديد</h6>
                                <form action="{{ route('admin.order-returns.store', $order) }}" method="POST">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label">سبب المرتجع (اختياري)</label>
                                        <input type="text" name="reason" class="form-control" placeholder="مثل: عيب، تغيير رأي">
                                    </div>
                                    <table class="table table-sm">
                                        <thead><tr><th>المنتج</th><th>الكمية في الطلب</th><th>مرتجع مسبقاً</th><th>كمية المرتجع</th></tr></thead>
                                        <tbody>
                                            @php $idx = 0; @endphp
                                            @foreach($order->items as $item)
                                                @php $already = $returnedQty[$item->id] ?? 0; $max = $item->quantity - $already; @endphp
                                                @if($max > 0)
                                                <tr>
                                                    <td>{{ $item->product_name }} @if($item->variant_description)<br><small class="text-muted">{{ $item->variant_description }}</small>@endif</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ $already }}</td>
                                                    <td>
                                                        <input type="hidden" name="items[{{ $idx }}][order_item_id]" value="{{ $item->id }}">
                                                        <input type="number" name="items[{{ $idx }}][quantity]" min="1" max="{{ $max }}" value="{{ $max }}" class="form-control form-control-sm" style="width: 80px;">
                                                    </td>
                                                </tr>
                                                @php $idx++; @endphp
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @php
                                        $hasReturnable = $order->items->contains(function ($item) use ($returnedQty) {
                                            return ($returnedQty[$item->id] ?? 0) < $item->quantity;
                                        });
                                    @endphp
                                    @if($hasReturnable)
                                        <button type="submit" class="btn btn-primary">إرسال طلب المرتجع</button>
                                    @else
                                        <p class="text-muted small mb-0">تم إرجاع كل بنود هذا الطلب مسبقاً.</p>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
