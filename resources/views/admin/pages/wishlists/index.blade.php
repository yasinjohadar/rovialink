@extends('admin.layouts.master')

@section('page-title')
    قائمة المفضلة
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
                    <h5 class="page-title fs-21 mb-1">قائمة المفضلة</h5>
                    <p class="text-muted mb-0 small">عرض عناصر المفضلة للمستخدمين وأكثر المنتجات في المفضلة.</p>
                </div>
            </div>

            @if($topWishlisted->isNotEmpty())
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">الأكثر في المفضلة</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>المنتج</th>
                                                <th>SKU</th>
                                                <th>عدد الإضافات للمفضلة</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($topWishlisted as $product)
                                                <tr>
                                                    <td>{{ $product->name }}</td>
                                                    <td><code>{{ $product->sku ?? '—' }}</code></td>
                                                    <td>{{ $product->wishlists_count }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">تعديل المنتج</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap gap-2 align-items-center">
                            <form action="{{ route('admin.wishlists.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center w-100">
                                <select name="user_id" class="form-select" style="max-width: 220px;">
                                    <option value="">كل المستخدمين</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-secondary">تصفية</button>
                                <a href="{{ route('admin.wishlists.index') }}" class="btn btn-outline-secondary">مسح</a>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>المستخدم</th>
                                            <th>المنتج</th>
                                            <th>التاريخ</th>
                                            <th style="width: 100px;">إجراء</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($items as $item)
                                            <tr>
                                                <td>{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</td>
                                                <td>
                                                    <div>{{ $item->user->name ?? '—' }}</div>
                                                    <small class="text-muted">{{ $item->user->email ?? '' }}</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($item->product && $item->product->primary_image_url)
                                                            <img src="{{ $item->product->primary_image_url }}" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                        @endif
                                                        <div>
                                                            <div class="fw-semibold">{{ $item->product->name ?? 'منتج محذوف' }}</div>
                                                            @if($item->product)
                                                                <a href="{{ route('admin.products.edit', $item->product) }}" class="small">تعديل المنتج</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <form action="{{ route('admin.wishlists.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('هل تريد حذف هذا العنصر من المفضلة؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    لا توجد عناصر في المفضلة حالياً.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($items->hasPages())
                                <div class="mt-3">{{ $items->links() }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
