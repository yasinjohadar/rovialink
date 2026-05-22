@extends('admin.layouts.master')

@section('page-title')
    تعديل الماركة
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تعديل: {{ $brand->name }}</h5>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.brands.update', $brand) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12"><h6 class="text-primary mb-2">المعلومات الأساسية</h6></div>
                            <div class="col-md-6">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $brand->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الرابط (Slug)</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" value="{{ old('slug', $brand->slug) }}">
                                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">صورة الماركة</label>
                                @if($brand->image_url)
                                    <div class="mb-2">
                                        <img src="{{ $brand->image_url }}" alt="{{ $brand->name }}" style="max-height: 60px; object-fit: contain;">
                                        <span class="text-muted small d-block">الصورة الحالية</span>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" accept="image/*">
                                <small class="text-muted">اترك الحقل فارغاً للإبقاء على الصورة الحالية</small>
                                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الترتيب</label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror" name="order" value="{{ old('order', $brand->order) }}" min="0">
                                @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                                <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
