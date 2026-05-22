@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الرأي
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الرأي</h5>
                </div>
                <div>
                    @can('review-edit')
                        <a href="{{ route('admin.reviews.edit', $review->id) }}" class="btn btn-primary btn-sm me-2">
                            <i class="bi bi-pencil"></i> تعديل
                        </a>
                    @endcan
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary btn-sm">
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">معلومات الرأي</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>المنتج:</strong>
                                    <p>
                                        @if($review->product)
                                            <a href="{{ route('admin.products.edit', $review->product) }}">{{ $review->product->name }}</a>
                                            @if($review->product->sku)
                                                <small class="text-muted">({{ $review->product->sku }})</small>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <strong>المستخدم:</strong>
                                    <p>
                                        @if($review->user)
                                            {{ $review->user->name }} ({{ $review->user->email }})
                                            @if($review->is_verified_purchase)
                                                <span class="badge bg-success ms-1" title="شراء موثق">✓</span>
                                            @endif
                                        @else
                                            <span class="text-muted">زائر</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>التقييم:</strong>
                                    <p>
                                        <span class="text-warning">{{ str_repeat('★', $review->rating) }}</span>
                                        <span class="text-muted">{{ str_repeat('☆', 5 - $review->rating) }}</span>
                                        <span class="ms-2">({{ $review->rating }}/5)</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <strong>الحالة:</strong>
                                    <p>
                                        <span class="badge {{ $review->status_badge }}">{{ $review->status_text }}</span>
                                        @if($review->is_featured)
                                            <span class="badge bg-warning text-dark ms-1">مميز</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($review->title)
                                <div class="mb-3">
                                    <strong>عنوان الرأي:</strong>
                                    <p>{{ $review->title }}</p>
                                </div>
                            @endif

                            @if($review->comment)
                                <div class="mb-3">
                                    <strong>التعليق:</strong>
                                    <p>{!! nl2br(e($review->comment)) !!}</p>
                                </div>
                            @endif

                            @if($review->images && count($review->images) > 0)
                                <div class="mb-3">
                                    <strong>الصور المرفقة:</strong>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @foreach($review->images as $image)
                                            <img src="{{ Storage::url($image) }}" alt="صورة مرفقة" 
                                                 class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($review->admin_response)
                                <div class="mb-3 p-3 bg-light rounded">
                                    <strong>رد الإدارة:</strong>
                                    <p class="mb-1">{!! nl2br(e($review->admin_response)) !!}</p>
                                    <small class="text-muted">
                                        بواسطة: {{ $review->adminResponder->name ?? '-' }}
                                        @if($review->admin_response_at)
                                            - {{ $review->admin_response_at->format('Y-m-d H:i') }}
                                        @endif
                                    </small>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>مفيد:</strong>
                                    <p>{{ $review->helpful_count }} شخص</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>غير مفيد:</strong>
                                    <p>{{ $review->not_helpful_count }} شخص</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">العمليات السريعة</h6>
                        </div>
                        <div class="card-body">
                            @can('review-approve')
                                @if($review->status != 'approved')
                                    <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm w-100">
                                            <i class="bi bi-check-circle"></i> اعتماد الرأي
                                        </button>
                                    </form>
                                @endif
                            @endcan

                            @can('review-reject')
                                @if($review->status != 'rejected')
                                    <form action="{{ route('admin.reviews.reject', $review->id) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm w-100">
                                            <i class="bi bi-x-circle"></i> رفض الرأي
                                        </button>
                                    </form>
                                @endif
                            @endcan

                            @can('review-reply')
                                <button type="button" class="btn btn-info btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#replyModal">
                                    <i class="bi bi-reply"></i> الرد على الرأي
                                </button>
                            @endcan
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">معلومات إضافية</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 150px;">تاريخ الإنشاء</th>
                                    <td>{{ $review->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>آخر تحديث</th>
                                    <td>{{ $review->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>نسبة المفيد</th>
                                    <td>{{ $review->helpful_percentage }}%</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reply Modal -->
    @can('review-reply')
        <div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.reviews.reply', $review->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="replyModalLabel">الرد على الرأي</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">نص الرد</label>
                                <textarea class="form-control" name="reply_text" rows="5" required>{{ old('reply_text', $review->admin_response) }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">إرسال الرد</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@stop
