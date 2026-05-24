@extends('admin.layouts.master')

@section('page-title')
    المدفوعات
@stop

@section('content')
    @include('admin.pages.payments.partials.alerts')

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h4 class="mb-0">المدفوعات</h4>
                    <p class="mb-0 text-muted">تتبع عمليات الدفع والاسترداد — Webhook: <code class="small">{{ $webhookUrl }}</code></p>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('admin.payments.settings.index') }}" class="btn btn-outline-primary">إعدادات الدفع</a>
                    <a href="{{ route('admin.payments.webhooks') }}" class="btn btn-outline-secondary">سجل Webhooks</a>
                    <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-primary">وسائل الدفع</a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="">الكل</option>
                                @foreach(['pending','completed','failed','refunded','cancelled'] as $st)
                                <option value="{{ $st }}" @selected(request('status')===$st)>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الوسيلة</label>
                            <select name="payment_method_id" class="form-select">
                                <option value="">الكل</option>
                                @foreach($methods as $m)
                                <option value="{{ $m->id }}" @selected(request('payment_method_id')==$m->id)>{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">بحث</label>
                            <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="رقم الطلب أو معرف المعاملة">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">تصفية</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الطلب</th>
                                <th>الوسيلة</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>
                                    @if($payment->order)
                                    <a href="{{ route('admin.orders.show', $payment->order) }}">#{{ $payment->order->order_number }}</a>
                                    @else — @endif
                                </td>
                                <td>{{ $payment->paymentMethod?->name ?? '—' }}</td>
                                <td>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                                <td><span class="badge bg-secondary">{{ $payment->status }}</span></td>
                                <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                <td><a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary">تفاصيل</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">لا توجد مدفوعات</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
