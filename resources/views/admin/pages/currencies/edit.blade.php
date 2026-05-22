@extends('admin.layouts.master')

@section('page-title')
    تعديل العملة
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
                <h5 class="page-title mb-0">تعديل العملة: {{ $currency->code }}</h5>
                <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.currencies.update', $currency) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">رمز العملة <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $currency->code) }}" maxlength="10" required>
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $currency->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رمز العرض (Symbol)</label>
                                <input type="text" name="symbol" class="form-control @error('symbol') is-invalid @enderror" value="{{ old('symbol', $currency->symbol) }}">
                                @error('symbol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">سعر الصرف للعملة الافتراضية <span class="text-danger">*</span></label>
                                <input type="number" name="rate_to_default" step="0.000001" min="0" class="form-control @error('rate_to_default') is-invalid @enderror" value="{{ old('rate_to_default', $currency->rate_to_default) }}">
                                <small class="text-muted">كم وحدة من العملة الافتراضية تعادل وحدة واحدة من هذه العملة.</small>
                                @error('rate_to_default')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الترتيب</label>
                                <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $currency->order) }}" min="0">
                                @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 d-flex align-items-end gap-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default', $currency->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">عملة افتراضية</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $currency->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">نشطة</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">تحديث العملة</button>
                                <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
