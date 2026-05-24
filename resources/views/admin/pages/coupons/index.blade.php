@extends('admin.layouts.master')

@section('page-title')
    الكوبونات
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
                <h5 class="page-title fs-21 mb-1">الكوبونات</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">إضافة كوبون</a>
                    <form action="{{ route('admin.coupons.mark-expired') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">تعليم المنتهية</button>
                    </form>
                    <a href="{{ route('admin.coupons.usage-report') }}" class="btn btn-info" target="_blank">تقرير الاستخدام</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <form action="{{ route('admin.coupons.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="text" name="search" class="form-control" style="width: 200px;" placeholder="بحث بالكود أو الاسم" value="{{ request('search') }}">
                        <select name="status" class="form-select" style="width: 160px;">
                            <option value="">كل الحالات</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>منتهي</option>
                        </select>
                        <select name="type" class="form-select" style="width: 180px;">
                            <option value="">كل أنواع الخصم</option>
                            <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                            <option value="fixed_amount" {{ request('type') === 'fixed_amount' ? 'selected' : '' }}>مبلغ ثابت</option>
                            <option value="buy_x_get_y" {{ request('type') === 'buy_x_get_y' ? 'selected' : '' }}>اشتر واحصل على</option>
                        </select>
                        <input type="date" name="expires_from" class="form-control" style="width: 160px;" value="{{ request('expires_from') }}" placeholder="تاريخ انتهاء من">
                        <input type="date" name="expires_to" class="form-control" style="width: 160px;" value="{{ request('expires_to') }}" placeholder="تاريخ انتهاء إلى">
                        <button type="submit" class="btn btn-secondary">فلترة</button>
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-danger">مسح</a>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>الكود</th>
                                    <th>الاسم</th>
                                    <th>النوع</th>
                                    <th>القيمة</th>
                                    <th>الاستخدام</th>
                                    <th>الحالة</th>
                                    <th>البداية</th>
                                    <th>النهاية</th>
                                    <th>ينطبق على</th>
                                    <th>عمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($coupons as $coupon)
                                    <tr>
                                        <td><strong>{{ $coupon->code }}</strong></td>
                                        <td>{{ $coupon->name }}</td>
                                        <td>
                                            @if($coupon->type === 'percentage')
                                                نسبة مئوية
                                            @elseif($coupon->type === 'fixed_amount')
                                                مبلغ ثابت
                                            @else
                                                اشترِ واحصل على
                                            @endif
                                        </td>
                                        <td>
                                            @if($coupon->type === 'percentage')
                                                {{ $coupon->value }}%
                                            @else
                                                {{ format_money($coupon->value) }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $coupon->usages_count ?? 0 }}
                                            @if($coupon->usage_limit !== null && $coupon->usage_limit > 0)
                                                / {{ $coupon->usage_limit }}
                                            @else
                                                / —
                                            @endif
                                        </td>
                                        <td>
                                            @if($coupon->status === 'active')
                                                <span class="badge bg-success">نشط</span>
                                            @elseif($coupon->status === 'inactive')
                                                <span class="badge bg-secondary">غير نشط</span>
                                            @else
                                                <span class="badge bg-danger">منتهي</span>
                                            @endif
                                        </td>
                                        <td>{{ $coupon->starts_at ? $coupon->starts_at->format('Y-m-d') : '—' }}</td>
                                        <td>{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '—' }}</td>
                                        <td>
                                            @if(($coupon->applicable_to ?? 'entire_store') === 'entire_store')
                                                المتجر كامل
                                            @elseif($coupon->applicable_to === 'specific_products')
                                                منتجات محددة ({{ $coupon->products->count() }})
                                            @elseif($coupon->applicable_to === 'specific_categories')
                                                تصنيفات محددة ({{ $coupon->categories->count() }})
                                            @else
                                                المتجر كامل
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-primary">تعديل</a>
                                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('هل تريد حذف هذا الكوبون؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-muted">لا توجد كوبونات. <a href="{{ route('admin.coupons.create') }}">إضافة كوبون</a></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $coupons->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@stop
