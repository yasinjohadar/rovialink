@extends('admin.layouts.master')

@section('page-title')
    تعديل السمة
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
                <h5 class="page-title mb-0">تعديل: {{ $attribute->name }}</h5>
                <div>
                    <a href="{{ route('admin.attributes.values.index', $attribute) }}" class="btn btn-info">إدارة القيم</a>
                    <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">العودة للقائمة</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.attributes.update', $attribute) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم السمة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $attribute->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">نوع العرض <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="select" {{ old('type', $attribute->type) == 'select' ? 'selected' : '' }}>قائمة (Select)</option>
                                    <option value="color" {{ old('type', $attribute->type) == 'color' ? 'selected' : '' }}>لون</option>
                                    <option value="image" {{ old('type', $attribute->type) == 'image' ? 'selected' : '' }}>صورة</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الترتيب</label>
                                <input type="number" min="0" class="form-control" name="order" value="{{ old('order', $attribute->order) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label d-block">ظاهر في المتجر</label>
                                <input type="hidden" name="is_visible" value="0">
                                <input type="checkbox" name="is_visible" value="1" {{ old('is_visible', $attribute->is_visible) ? 'checked' : '' }}> نعم
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                                <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
