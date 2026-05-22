@extends('admin.layouts.master')

@section('page-title')
    سمات المنتجات
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
                <h5 class="page-title fs-21 mb-1">سمات المنتجات (اللون، المقاس، إلخ)</h5>
                <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">إضافة سمة</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>الاسم</th>
                                    <th>النوع</th>
                                    <th>عدد القيم</th>
                                    <th>الترتيب</th>
                                    <th>ظاهر في المتجر</th>
                                    <th>عمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attributes as $attr)
                                    <tr>
                                        <td><strong>{{ $attr->name }}</strong></td>
                                        <td>
                                            @if($attr->type === 'color')
                                                لون
                                            @elseif($attr->type === 'image')
                                                صورة
                                            @else
                                                قائمة
                                            @endif
                                        </td>
                                        <td>{{ $attr->values_count }}</td>
                                        <td>{{ $attr->order }}</td>
                                        <td>{{ $attr->is_visible ? 'نعم' : 'لا' }}</td>
                                        <td>
                                            <a href="{{ route('admin.attributes.values.index', $attr) }}" class="btn btn-sm btn-info">القيم</a>
                                            <a href="{{ route('admin.attributes.edit', $attr) }}" class="btn btn-sm btn-primary">تعديل</a>
                                            <form action="{{ route('admin.attributes.destroy', $attr) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف السمة وجميع قيمها؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">لا توجد سمات. <a href="{{ route('admin.attributes.create') }}">إضافة سمة</a></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $attributes->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@stop
