@extends('admin.layouts.master')

@section('page-title')
    العملاء
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h5 class="page-title fs-21 mb-1">العملاء</h5>
            </div>

            <div class="card">
                <div class="card-header">
                    <form action="{{ route('admin.customers.index') }}" method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">بحث</label>
                            <input type="text" name="search" class="form-control" placeholder="الاسم أو البريد أو الجوال" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">حالة الطلبات</label>
                            <select name="has_orders" class="form-select">
                                <option value="">الكل</option>
                                <option value="1" {{ request('has_orders') === '1' ? 'selected' : '' }}>لديه طلبات</option>
                                <option value="0" {{ request('has_orders') === '0' ? 'selected' : '' }}>بدون طلبات</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">إجمالي إنفاق من</label>
                            <input type="number" name="min_total" class="form-control" min="0" step="0.01" value="{{ request('min_total') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">تسجيل من</label>
                            <input type="date" name="registered_from" class="form-control" value="{{ request('registered_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">تسجيل إلى</label>
                            <input type="date" name="registered_to" class="form-control" value="{{ request('registered_to') }}">
                        </div>
                        <div class="col-md-1 d-grid">
                            <button type="submit" class="btn btn-secondary">بحث</button>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>العميل</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الجوال</th>
                                    <th>رصيد النقاط</th>
                                    <th>عدد الطلبات</th>
                                    <th>إجمالي الإنفاق</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.customers.show', $customer) }}">
                                                {{ $customer->name }}
                                            </a>
                                        </td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->phone ?? '—' }}</td>
                                        <td>{{ number_format($customer->loyalty_points_balance ?? 0, 0) }} نقطة</td>
                                        <td>{{ $customer->orders_count ?? 0 }}</td>
                                        <td>{{ number_format($customer->total_spent ?? 0, 2) }} ر.س</td>
                                        <td>{{ $customer->created_at?->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">
                                                عرض الملف
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">لا يوجد عملاء مطابقون لشروط البحث.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

