@extends('admin.layouts.master')

@section('page-title')
    طلب مرتجع #{{ $orderReturn->id }}
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
                <h5 class="page-title mb-0">طلب مرتجع #{{ $orderReturn->id }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.orders.show', $orderReturn->order) }}" class="btn btn-secondary">عرض الطلب الأصلي</a>
                    <a href="{{ route('admin.order-returns.index') }}" class="btn btn-outline-secondary">قائمة طلبات المرتجع</a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">الطلب الأصلي</h6>
                            <a href="{{ route('admin.orders.show', $orderReturn->order) }}">{{ $orderReturn->order->order_number }}</a>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>الحالة:</strong>
                                @if($orderReturn->status === 'pending')
                                    <span class="badge bg-warning text-dark">قيد الانتظار</span>
                                @elseif($orderReturn->status === 'approved')
                                    <span class="badge bg-success">معتمد</span>
                                @else
                                    <span class="badge bg-danger">مرفوض</span>
                                @endif
                            </p>
                            <p class="mb-1"><strong>السبب:</strong> {{ $orderReturn->reason ?: '—' }}</p>
                            <p class="mb-1"><strong>طالب المرتجع:</strong> {{ $orderReturn->requestedByUser->name ?? '—' }}</p>
                            <p class="mb-0"><strong>تاريخ الطلب:</strong> {{ $orderReturn->requested_at ? $orderReturn->requested_at->format('Y-m-d H:i') : $orderReturn->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h6 class="mb-0">بنود المرتجع</h6></div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>المنتج</th>
                                        <th>الكمية المرتجعة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderReturn->items as $ri)
                                        <tr>
                                            <td>
                                                {{ $ri->orderItem->product_name ?? '—' }}
                                                @if($ri->orderItem->variant_description)
                                                    <br><small class="text-muted">{{ $ri->orderItem->variant_description }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $ri->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    @if($orderReturn->status === 'pending')
                        <div class="card mb-3">
                            <div class="card-header"><h6 class="mb-0">اعتماد / رفض</h6></div>
                            <div class="card-body">
                                <form action="{{ route('admin.order-returns.approve', $orderReturn) }}" method="POST" class="mb-3">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label">ملاحظة إدارية (اختياري)</label>
                                        <textarea name="admin_note" rows="2" class="form-control" placeholder="ملاحظة عند الاعتماد">{{ old('admin_note', $orderReturn->admin_note) }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">اعتماد طلب المرتجع</button>
                                </form>
                                <form action="{{ route('admin.order-returns.reject', $orderReturn) }}" method="POST">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label">ملاحظة (اختياري)</label>
                                        <textarea name="admin_note" rows="2" class="form-control" placeholder="سبب الرفض">{{ old('admin_note') }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100">رفض طلب المرتجع</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="card mb-3">
                            <div class="card-header"><h6 class="mb-0">معالجة الطلب</h6></div>
                            <div class="card-body">
                                @if($orderReturn->processed_at)
                                    <p class="mb-1"><strong>تاريخ المعالجة:</strong> {{ $orderReturn->processed_at->format('Y-m-d H:i') }}</p>
                                    <p class="mb-0"><strong>معالج بواسطة:</strong> {{ $orderReturn->processedByUser->name ?? '—' }}</p>
                                @endif
                                @if($orderReturn->admin_note)
                                    <hr>
                                    <p class="mb-0"><strong>ملاحظة إدارية:</strong><br>{{ $orderReturn->admin_note }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
