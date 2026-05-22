@extends('admin.layouts.master')

@section('page-title')
    قائمة الماركات
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
                    <h5 class="page-title fs-21 mb-1">الماركات</h5>
                </div>
                <a href="{{ route('admin.brands.create') }}" class="btn btn-primary btn-sm">إضافة ماركة</a>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            <form action="{{ route('admin.brands.index') }}" method="GET" class="d-flex align-items-center gap-2 ms-auto">
                                <input type="text" name="query" class="form-control" style="width: 220px;" placeholder="بحث بالاسم أو الرابط" value="{{ request('query') }}">
                                <button type="submit" class="btn btn-secondary">بحث</button>
                                <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">مسح</a>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" style="width: 50px;">#</th>
                                            <th scope="col" style="width: 70px;">الصورة</th>
                                            <th scope="col">الاسم</th>
                                            <th scope="col">الرابط (Slug)</th>
                                            <th scope="col" style="width: 80px;">الترتيب</th>
                                            <th scope="col" style="width: 180px;">العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($brands as $brand)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration + ($brands->currentPage() - 1) * $brands->perPage() }}</th>
                                                <td>
                                                    @if($brand->image_url)
                                                        <img src="{{ $brand->image_url }}" alt="{{ $brand->name }}" style="width: 50px; height: 50px; object-fit: contain; border-radius: 4px;">
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>{{ $brand->name }}</td>
                                                <td><code class="text-primary">{{ $brand->slug }}</code></td>
                                                <td>{{ $brand->order }}</td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-primary" title="تعديل">تعديل</a>
                                                        <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف هذه الماركة؟');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="حذف">حذف</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">لا توجد ماركات. <a href="{{ route('admin.brands.create') }}">إضافة ماركة</a></td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($brands->hasPages())
                                <div class="mt-3">{{ $brands->links() }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
