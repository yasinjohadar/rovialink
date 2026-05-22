@extends('admin.layouts.master')

@section('page-title')
    تعديل التصنيف
@stop

@section('css')
    <style>
        .image-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #dee2e6;
            margin-top: 10px;
        }
        .cover-preview {
            width: 100%;
            max-width: 400px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #dee2e6;
            margin-top: 10px;
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
                <h5 class="page-title mb-0">تعديل التصنيف: {{ $category->name }}</h5>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.categories.update', $category->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- المعلومات الأساسية -->
                            <div class="col-12">
                                <h6 class="text-primary mb-3">المعلومات الأساسية</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           name="name" placeholder="اسم التصنيف" value="{{ old('name', $category->name) }}" required>
                                    <label>اسم التصنيف <span class="text-danger">*</span></label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           name="slug" placeholder="الرابط (Slug)" value="{{ old('slug', $category->slug) }}">
                                    <label>الرابط (Slug)</label>
                                    <div class="form-text">سيتم إنشاؤه تلقائياً من الاسم إذا تركت فارغاً</div>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('parent_id') is-invalid @enderror" name="parent_id">
                                        <option value="">تصنيف رئيسي</option>
                                        @foreach($parentCategories as $parent)
                                            <option value="{{ $parent->id }}" 
                                                {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>التصنيف الأب</label>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                           name="order" placeholder="الترتيب" value="{{ old('order', $category->order) }}" min="0">
                                    <label>الترتيب</label>
                                    @error('order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              name="description" placeholder="الوصف" style="height: 100px">{{ old('description', $category->description) }}</textarea>
                                    <label>الوصف</label>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- الصور -->
                            <div class="col-12">
                                <h6 class="text-primary mb-3">الصور</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">صورة التصنيف</label>
                                @if($category->image)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($category->image) }}" alt="الصورة الحالية" class="image-preview">
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       name="image" accept="image/*" onchange="previewImage(this, 'image-preview-new')">
                                <img id="image-preview-new" class="image-preview" style="display: none;" alt="معاينة الصورة الجديدة">
                                @error('image')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">صورة الغلاف</label>
                                @if($category->cover_image)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($category->cover_image) }}" alt="صورة الغلاف الحالية" class="cover-preview">
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('cover_image') is-invalid @enderror" 
                                       name="cover_image" accept="image/*" onchange="previewImage(this, 'cover-preview-new')">
                                <img id="cover-preview-new" class="cover-preview" style="display: none;" alt="معاينة صورة الغلاف الجديدة">
                                @error('cover_image')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- SEO -->
                            <div class="col-12">
                                <h6 class="text-primary mb-3">إعدادات SEO</h6>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                           name="meta_title" placeholder="عنوان SEO" value="{{ old('meta_title', $category->meta_title) }}">
                                    <label>عنوان SEO (Meta Title)</label>
                                    @error('meta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                              name="meta_description" placeholder="وصف SEO" style="height: 100px">{{ old('meta_description', $category->meta_description) }}</textarea>
                                    <label>وصف SEO (Meta Description)</label>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                           name="meta_keywords" placeholder="الكلمات المفتاحية" value="{{ old('meta_keywords', $category->meta_keywords) }}">
                                    <label>الكلمات المفتاحية (Meta Keywords)</label>
                                    <div class="form-text">افصل بين الكلمات بفواصل</div>
                                    @error('meta_keywords')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- الحالة -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                        <option value="active" {{ old('status', $category->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="inactive" {{ old('status', $category->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                    <label>الحالة <span class="text-danger">*</span></label>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary px-4 me-2">
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>تحديث التصنيف
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.getElementById(previewId);
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@stop
