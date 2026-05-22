@extends('admin.layouts.master')

@section('page-title')
    تعديل الرأي
@stop

@section('css')
    <style>
        .image-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #dee2e6;
            margin: 5px;
        }
    </style>
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
                <h5 class="page-title mb-0">تعديل الرأي</h5>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>

            <form method="POST" action="{{ route('admin.reviews.update', $review->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-xl-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">المعلومات الأساسية</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">المنتج <span class="text-danger">*</span></label>
                                        <select class="form-select @error('product_id') is-invalid @enderror" name="product_id" required>
                                            <option value="">اختر المنتج</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}" {{ old('product_id', $review->product_id) == $p->id ? 'selected' : '' }}>
                                                    {{ $p->name }}{{ $p->sku ? ' (' . $p->sku . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('product_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">المستخدم</label>
                                        <select class="form-select @error('user_id') is-invalid @enderror" name="user_id">
                                            <option value="">زائر</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id', $review->user_id) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('user_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">التقييم (نجوم) <span class="text-danger">*</span></label>
                                        <select class="form-select @error('rating') is-invalid @enderror" name="rating" required>
                                            <option value="">اختر التقييم</option>
                                            <option value="5" {{ old('rating', $review->rating) == '5' ? 'selected' : '' }}>5 نجوم</option>
                                            <option value="4" {{ old('rating', $review->rating) == '4' ? 'selected' : '' }}>4 نجوم</option>
                                            <option value="3" {{ old('rating', $review->rating) == '3' ? 'selected' : '' }}>3 نجوم</option>
                                            <option value="2" {{ old('rating', $review->rating) == '2' ? 'selected' : '' }}>2 نجوم</option>
                                            <option value="1" {{ old('rating', $review->rating) == '1' ? 'selected' : '' }}>1 نجم</option>
                                        </select>
                                        @error('rating')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                            <option value="pending" {{ old('status', $review->status) == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                            <option value="approved" {{ old('status', $review->status) == 'approved' ? 'selected' : '' }}>معتمد</option>
                                            <option value="rejected" {{ old('status', $review->status) == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                            <option value="spam" {{ old('status', $review->status) == 'spam' ? 'selected' : '' }}>محتوى غير مرغوب</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">عنوان الرأي</label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               name="title" value="{{ old('title', $review->title) }}">
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">التعليق</label>
                                        <textarea class="form-control @error('comment') is-invalid @enderror" 
                                                  name="comment" rows="5">{{ old('comment', $review->comment) }}</textarea>
                                        @error('comment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">الإعدادات</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_verified_purchase" value="1" 
                                               id="is_verified_purchase" {{ old('is_verified_purchase', $review->is_verified_purchase) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_verified_purchase">
                                            شراء موثق
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" 
                                               id="is_featured" {{ old('is_featured', $review->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            رأي مميز
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">الصور المرفقة</h6>
                            </div>
                            <div class="card-body">
                                @if($review->images && count($review->images) > 0)
                                    <div class="mb-3">
                                        <p class="small mb-2">الصور الحالية:</p>
                                        @foreach($review->images as $index => $image)
                                            <div class="d-inline-block position-relative me-2 mb-2">
                                                <img src="{{ Storage::url($image) }}" alt="صورة {{ $index + 1 }}" class="image-preview">
                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                                                        onclick="deleteImage({{ $review->id }}, {{ $index }})" style="border-radius: 50%; width: 25px; height: 25px; padding: 0;">×</button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="images[]" accept="image/*" multiple>
                                <small class="text-muted">يمكنك رفع صور إضافية</small>
                                @error('images.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">إعدادات SEO</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">عنوان SEO</label>
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                           name="meta_title" value="{{ old('meta_title', $review->meta_title) }}">
                                    @error('meta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="form-label">وصف SEO</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                              name="meta_description" rows="3">{{ old('meta_description', $review->meta_description) }}</textarea>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4 mb-4">
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary px-4 me-2">
                        إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>تحديث الرأي
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('script')
    <script>
        function deleteImage(reviewId, imageIndex) {
            if (confirm('هل أنت متأكد من حذف هذه الصورة؟')) {
                fetch(`/admin/reviews/${reviewId}/images/${imageIndex}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        }
    </script>
@stop
