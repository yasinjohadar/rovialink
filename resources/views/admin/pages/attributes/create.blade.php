@extends('admin.layouts.master')

@section('page-title')
    إضافة سمة
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
                <h5 class="page-title mb-0">إضافة سمة منتج</h5>
                <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.attributes.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم السمة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required placeholder="مثال: اللون، المقاس">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">نوع العرض <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="select" {{ old('type', 'select') == 'select' ? 'selected' : '' }}>قائمة (Select)</option>
                                    <option value="color" {{ old('type') == 'color' ? 'selected' : '' }}>لون (مربعات ألوان)</option>
                                    <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>صورة</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الترتيب</label>
                                <input type="number" min="0" class="form-control" name="order" value="{{ old('order', 0) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label d-block">ظاهر في المتجر</label>
                                <input type="hidden" name="is_visible" value="0">
                                <input type="checkbox" name="is_visible" value="1" {{ old('is_visible', true) ? 'checked' : '' }}> نعم
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">حفظ السمة</button>
                                <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
