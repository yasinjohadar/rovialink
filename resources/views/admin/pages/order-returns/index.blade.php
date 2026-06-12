@extends('admin.layouts.master')

@section('page-title')
    طلبات المرتجع
@stop

@section('styles')
    @include('frontend.layouts.theme-variables')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-orders-index.css') }}?v=1">
@endsection

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid orders-index-page my-4">
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

            <div class="orders-index-hero">
                <div class="orders-index-hero__top">
                    <div>
                        <h1 class="orders-index-hero__title">طلبات المرتجع</h1>
                        <p class="orders-index-hero__subtitle">مراجعة طلبات الإرجاع والموافقة عليها أو رفضها</p>
                    </div>
                </div>
                <div class="orders-index-stats">
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">إجمالي الطلبات</div>
                        <div class="orders-index-stat__value">{{ number_format($returns->total()) }}</div>
                    </div>
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">قيد الانتظار</div>
                        <div class="orders-index-stat__value">{{ number_format($statusCounts['pending'] ?? 0) }}</div>
                    </div>
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">معتمدة</div>
                        <div class="orders-index-stat__value">{{ number_format($statusCounts['approved'] ?? 0) }}</div>
                    </div>
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">مرفوضة</div>
                        <div class="orders-index-stat__value">{{ number_format($statusCounts['rejected'] ?? 0) }}</div>
                    </div>
                </div>
            </div>

            <div class="orders-index-panel">
                <div class="orders-index-panel__head">
                    <h2 class="orders-index-panel__title">
                        <i class="bi bi-funnel"></i>
                        تصفية طلبات المرتجع
                    </h2>
                </div>
                <div class="p-3 p-md-4 border-bottom">
                    <form action="{{ route('admin.order-returns.index') }}" method="GET" class="orders-index-filters">
                        <div class="orders-index-filters__field">
                            <label class="form-label small fw-semibold mb-1">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>معتمد</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            </select>
                        </div>
                        <div class="orders-index-filters__actions">
                            <button type="submit" class="btn btn-search">
                                <i class="bi bi-search me-1"></i> تصفية
                            </button>
                            <a href="{{ route('admin.order-returns.index') }}" class="btn btn-clear">
                                <i class="bi bi-x-lg me-1"></i> مسح
                            </a>
                        </div>
                    </form>
                </div>

                <div class="orders-index-table-wrap">
                    <div class="table-responsive">
                        <table class="table orders-index-table align-middle mb-0">
                            <thead>
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
                                        <td><span class="text-muted fw-semibold">#{{ $r->id }}</span></td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $r->order) }}" class="orders-index-order-no text-decoration-none">
                                                <i class="bi bi-receipt"></i>
                                                {{ $r->order->order_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="orders-index-customer">{{ $r->requestedByUser->name ?? '—' }}</span>
                                        </td>
                                        <td>
                                            @if($r->status === 'pending')
                                                <span class="orders-index-status-badge" style="background-color: #f59e0b;">قيد الانتظار</span>
                                            @elseif($r->status === 'approved')
                                                <span class="orders-index-status-badge" style="background-color: #22c55e;">معتمد</span>
                                            @else
                                                <span class="orders-index-status-badge" style="background-color: #ef4444;">مرفوض</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($r->reason, 50) ?: '—' }}</td>
                                        <td class="orders-index-date">{{ $r->created_at->format('Y/m/d — H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.order-returns.show', $r) }}" class="orders-index-view-btn">
                                                <i class="bi bi-eye"></i>
                                                عرض
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="orders-index-empty">
                                                <i class="bi bi-inbox d-block"></i>
                                                <p>لا توجد طلبات مرتجع</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($returns->hasPages())
                        <div id="orders-pagination">{{ $returns->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
