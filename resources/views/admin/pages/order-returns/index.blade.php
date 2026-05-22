@extends('admin.layouts.master')

@section('page-title')
    طلبات المرتجع
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
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">طلبات المرتجع</h5>
                    <p class="text-muted mb-0 small">مراجعة طلبات الإرجاع والموافقة عليها أو رفضها.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap gap-2 align-items-center">
                            <form action="{{ route('admin.order-returns.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                                <select name="status" class="form-select" style="max-width: 180px;">
                                    <option value="">كل الحالات</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>معتمد</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                </select>
                                <button type="submit" class="btn btn-secondary">تصفية</button>
                                <a href="{{ route('admin.order-returns.index') }}" class="btn btn-outline-secondary">مسح</a>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 60px;">#</th>
                                            <th>الطلب</th>
                                            <th>طالب المرتجع</th>
                                            <th>الحالة</th>
                                            <th>السبب</th>
                                            <th>التاريخ</th>
                                            <th style="width: 120px;">إجراء</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($returns as $r)
                                            <tr>
                                                <td>{{ $r->id }}</td>
                                                <td>
                                                    <a href="{{ route('admin.orders.show', $r->order) }}">{{ $r->order->order_number }}</a>
                                                </td>
                                                <td>{{ $r->requestedByUser->name ?? '—' }}</td>
                                                <td>
                                                    @if($r->status === 'pending')
                                                        <span class="badge bg-warning text-dark">قيد الانتظار</span>
                                                    @elseif($r->status === 'approved')
                                                        <span class="badge bg-success">معتمد</span>
                                                    @else
                                                        <span class="badge bg-danger">مرفوض</span>
                                                    @endif
                                                </td>
                                                <td>{{ Str::limit($r->reason, 40) ?: '—' }}</td>
                                                <td>{{ $r->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.order-returns.show', $r) }}" class="btn btn-sm btn-primary">عرض</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">لا توجد طلبات مرتجع.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($returns->hasPages())
                                <div class="mt-3">{{ $returns->links() }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
