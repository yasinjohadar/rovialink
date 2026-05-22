@extends('admin.layouts.master')

@section('page-title')
    العملات
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
                    <h5 class="page-title fs-21 mb-1">العملات</h5>
                    <p class="text-muted mb-0 small">إدارة العملات، تعيين العملة الافتراضية، وسعر الصرف بالنسبة لها. الأسعار في النظام تُخزَن بالعملة الافتراضية.</p>
                </div>
                <a href="{{ route('admin.currencies.create') }}" class="btn btn-primary btn-sm">إضافة عملة</a>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>الرمز</th>
                                            <th>الاسم</th>
                                            <th>الرمز (عرض)</th>
                                            <th>سعر الصرف للافتراضية</th>
                                            <th>افتراضية</th>
                                            <th>نشطة</th>
                                            <th style="width: 260px;">العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($currencies as $c)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><code>{{ $c->code }}</code></td>
                                                <td>{{ $c->name }}</td>
                                                <td>{{ $c->symbol ?? '—' }}</td>
                                                <td>{{ number_format((float) $c->rate_to_default, 6) }}</td>
                                                <td>
                                                    @if($c->is_default)
                                                        <span class="badge bg-success">افتراضية</span>
                                                    @else
                                                        <form action="{{ route('admin.currencies.set-default', $c) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success">تعيين افتراضية</button>
                                                        </form>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($c->is_active)
                                                        <span class="badge bg-primary">نشطة</span>
                                                    @else
                                                        <span class="badge bg-secondary">غير نشطة</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        <a href="{{ route('admin.currencies.edit', $c) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        <form action="{{ route('admin.currencies.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف هذه العملة؟');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" {{ $c->is_default ? 'disabled' : '' }}>حذف</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">
                                                    لا توجد عملات. <a href="{{ route('admin.currencies.create') }}">إضافة عملة</a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
