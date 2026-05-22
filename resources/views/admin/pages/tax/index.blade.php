@extends('admin.layouts.master')

@section('page-title')
    فئات ومعدلات الضرائب
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
                    <h5 class="page-title fs-21 mb-1">فئات ومعدلات الضرائب</h5>
                    <p class="text-muted mb-0 small">أنشئ فئات ضريبية (قياسي، مخفّض، معفى) وحدد معدلات ضريبية مختلفة لكل دولة / مدينة / رمز بريدي.</p>
                </div>
                <a href="{{ route('admin.tax.classes.create') }}" class="btn btn-primary btn-sm">إضافة فئة ضريبية</a>
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
                                            <th>الاسم</th>
                                            <th>الرابط (Slug)</th>
                                            <th>افتراضية</th>
                                            <th>عدد المعدلات</th>
                                            <th style="width: 220px;">العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($classes as $class)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $class->name }}</td>
                                                <td><code class="text-primary">{{ $class->slug }}</code></td>
                                                <td>
                                                    @if($class->is_default)
                                                        <span class="badge bg-success">افتراضية</span>
                                                    @else
                                                        <span class="badge bg-secondary">غير افتراضية</span>
                                                    @endif
                                                </td>
                                                <td>{{ $class->rates_count }}</td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('admin.tax.classes.rates', $class) }}" class="btn btn-sm btn-outline-primary">معدلات الضرائب</a>
                                                        <a href="{{ route('admin.tax.classes.edit', $class) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        <form action="{{ route('admin.tax.classes.destroy', $class) }}" method="POST" onsubmit="return confirm('حذف هذه الفئة الضريبية؟ سيتم حذف جميع المعدلات التابعة لها.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" {{ $class->is_default ? 'disabled' : '' }}>حذف</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    لا توجد فئات ضرائب بعد.
                                                    <a href="{{ route('admin.tax.classes.create') }}">إضافة أول فئة ضريبية</a>
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

