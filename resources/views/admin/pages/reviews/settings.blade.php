@extends('admin.layouts.master')

@section('page-title')
    إعدادات التقييمات
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

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
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إعدادات التقييمات والتعليقات</h5>
                </div>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary btn-sm">العودة لآراء العملاء</a>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">نشر التعليقات والتقييمات الجديدة</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">اختر كيف تظهر التعليقات والتقييمات الجديدة من العملاء (ينطبق على المنتجات التي تستخدم الإعداد الافتراضي).</p>
                            <form method="POST" action="{{ route('admin.review-settings.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="reviews_require_approval" id="approval_1" value="1" {{ $reviewsRequireApproval ? 'checked' : '' }}>
                                        <label class="form-check-label" for="approval_1">
                                            <strong>تحتاج موافقة الإدارة قبل النشر</strong> — التعليقات تظهر بعد اعتمادها من لوحة التحكم.
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="reviews_require_approval" id="approval_0" value="0" {{ !$reviewsRequireApproval ? 'checked' : '' }}>
                                        <label class="form-check-label" for="approval_0">
                                            <strong>نشر تلقائياً</strong> — التعليقات تظهر مباشرة دون مراجعة.
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
