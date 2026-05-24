@extends('admin.layouts.master')

@section('page-title')
    إضافة منتج جديد
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
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h4 class="mb-0">إضافة منتج جديد</h4>
                    <p class="mb-0 text-muted">إنشاء منتج رقمي جديد مع التحكم في السعر والسمات والملفات</p>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-right me-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <!-- العمود الرئيسي -->
                    <div class="col-lg-8">

                        <!-- المعلومات الأساسية -->
                        <div class="card custom-card mb-4">
                            <div class="card-header">
                                <div class="card-title">المعلومات الأساسية</div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الرابط (Slug)</label>
                                        <input type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" value="{{ old('slug') }}">
                                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">التصنيف</label>
                                        <select name="category_id" class="form-select">
                                            <option value="">-- اختر --</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ (string) old('category_id', $selectedCategoryId ?? '') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الماركة</label>
                                        <select name="brand_id" class="form-select">
                                            <option value="">— لا ماركة —</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">SKU</label>
                                        <input type="text" class="form-control" name="sku" value="{{ old('sku') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">وصف مختصر</label>
                                        <textarea name="short_description" class="form-control" rows="2">{{ old('short_description') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">الوصف</label>
                                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="10">{{ old('description') }}</textarea>
                                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('admin.pages.products.partials.seo-fields')

                        <!-- المنتج الرقمي -->
                        <div class="card custom-card mb-4">
                            <div class="card-header">
                                <div class="card-title">المنتج الرقمي (إن وُجد)</div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-check mt-2">
                                            <input type="hidden" name="is_digital" value="0">
                                            <input class="form-check-input" type="checkbox" name="is_digital" value="1" id="is_digital_cb" {{ old('is_digital') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_digital_cb">منتج رقمي قابل للتحميل</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">حد مرات التحميل (اختياري)</label>
                                        <input type="number" min="1" class="form-control" name="digital_download_limit" value="{{ old('digital_download_limit') }}">
                                        <small class="text-muted">اتركه فارغاً لعدد غير محدود</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">عدد أيام صلاحية التحميل (اختياري)</label>
                                        <input type="number" min="1" class="form-control" name="digital_download_expiry_days" value="{{ old('digital_download_expiry_days') }}">
                                        <small class="text-muted">اتركه فارغاً بدون انتهاء</small>
                                    </div>
                                </div>

                                <div class="mt-3" id="digital-files-section" style="{{ old('is_digital') ? '' : 'display:none;' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="text-primary mb-0">ملفات التحميل</h6>
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-digital-file-row">إضافة ملف</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle" id="digital-files-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>العنوان</th>
                                                    <th>الملف</th>
                                                    <th style="width:120px">الترتيب</th>
                                                    <th style="width:80px"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="digital-files-tbody">
                                                @php $digitalFiles = old('digital_files', [['title' => '', 'order' => 0]]); @endphp
                                                @foreach($digitalFiles as $idx => $df)
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name="digital_files[{{ $idx }}][title]" value="{{ $df['title'] ?? '' }}" placeholder="مثال: ملف PDF">
                                                        </td>
                                                        <td>
                                                            <input type="file" class="form-control" name="digital_files[{{ $idx }}][file]">
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" class="form-control" name="digital_files[{{ $idx }}][order]" value="{{ $df['order'] ?? 0 }}">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-digital-file">حذف</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <small class="text-muted d-block">ملاحظة: يجب رفع ملف على الأقل لكل صف جديد.</small>
                                </div>
                            </div>
                        </div>

                        <!-- السعر -->
                        <div class="card custom-card mb-4">
                            <div class="card-header">
                                <div class="card-title">السعر</div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">السعر ($) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" class="form-control" name="price" value="{{ old('price', 0) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">سعر المقارنة ($)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" name="compare_at_price" value="{{ old('compare_at_price') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- سمات المنتج -->
                        <div class="card custom-card mb-4">
                            <div class="card-header">
                                <div class="card-title">سمات هذا المنتج</div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    اختر السمات التي تنطبق على هذا المنتج (مثل اللون، المقاس). بعد الحفظ ستُنقل لصفحة التعديل لإضافة المتغيرات.
                                </p>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($attributes as $attr)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="attribute_ids[]" value="{{ $attr->id }}" id="create_attr_{{ $attr->id }}" {{ in_array($attr->id, old('attribute_ids', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="create_attr_{{ $attr->id }}">{{ $attr->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @if($attributes->isEmpty())
                                    <p class="text-muted small mb-0">
                                        لا توجد سمات. يمكنك
                                        <a href="{{ route('admin.attributes.index') }}">إنشاء سمات من هنا</a>
                                        ثم العودة.
                                    </p>
                                @endif
                            </div>
                        </div>

                    </div>

                    <!-- الشريط الجانبي -->
                    <div class="col-lg-4">

                        @include('admin.pages.products.partials.ai-tools', ['aiModels' => $aiModels ?? collect()])

                        <!-- الحالة والنشر -->
                        <div class="card custom-card mb-4">
                            <div class="card-header">
                                <div class="card-title">إعدادات النشر</div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">الحالة</label>
                                    <select name="status" class="form-select">
                                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>أرشيف</option>
                                    </select>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="hidden" name="is_featured" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">منتج مميز</label>
                                </div>
                                <div class="form-check">
                                    <input type="hidden" name="is_visible" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_visible" value="1" id="is_visible" {{ old('is_visible', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_visible">ظاهر في المتجر</label>
                                </div>
                            </div>
                        </div>

                        <!-- التقييمات والتعليقات -->
                        <div class="card custom-card mb-4">
                            <div class="card-header">
                                <div class="card-title">التقييمات والتعليقات</div>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input type="hidden" name="allow_reviews" value="0">
                                    <input class="form-check-input" type="checkbox" name="allow_reviews" value="1" id="allow_reviews" {{ old('allow_reviews', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_reviews">
                                        السماح بالتعليقات والتقييمات لهذا المنتج
                                    </label>
                                </div>
                                <div id="reviews_approval_wrap">
                                    <label class="form-label">نشر التعليقات</label>
                                    <select name="reviews_require_approval" class="form-select">
                                        <option value="default" {{ old('reviews_require_approval', 'default') === 'default' ? 'selected' : '' }}>استخدام الإعداد الافتراضي للمتجر</option>
                                        <option value="1" {{ old('reviews_require_approval') === '1' ? 'selected' : '' }}>بعد موافقة الإدارة</option>
                                        <option value="0" {{ old('reviews_require_approval') === '0' ? 'selected' : '' }}>تلقائياً بدون موافقة</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- صور المنتج -->
                        <div class="card custom-card mb-4">
                            <div class="card-header">
                                <div class="card-title">صور المنتج</div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">الصورة الرئيسية</label>
                                    <input type="file" name="primary_image" class="form-control" accept="image/*">
                                    <small class="text-muted d-block mt-1">ستظهر هذه الصورة كصورة أساسية للمنتج في القوائم وصفحة المنتج.</small>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">معرض الصور</label>
                                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                                    <small class="text-muted d-block mt-1">يمكنك رفع عدة صور إضافية لعرضها في معرض الصور.</small>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الحفظ -->
                        <div class="card custom-card">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-save me-2"></i>
                                    حفظ المنتج
                                </button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary w-100">
                                    <i class="bi bi-x-circle me-2"></i>
                                    إلغاء
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>
@stop

@section('script')
<!-- TinyMCE Editor (نفس محرر المدونة) -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
<script>
function initProductDescriptionEditor() {
    if (typeof tinymce === 'undefined') {
        setTimeout(initProductDescriptionEditor, 100);
        return;
    }
    tinymce.init({
        selector: '#description',
        height: 400,
        directionality: 'rtl',
        language: 'ar',
        language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@latest/langs6/ar.js',
        promotion: false,
        branding: false,
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code codesample fullscreen insertdatetime media table help wordcount emoticons directionality',
        toolbar: 'undo redo | blocks | bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | link image media table | codesample code | fullscreen | help',
        menubar: 'file edit view insert format tools table help',
        menu: {
            file: { title: 'ملف', items: 'newdocument restoredraft | preview | print' },
            edit: { title: 'تحرير', items: 'undo redo | cut copy paste | selectall | searchreplace' },
            view: { title: 'عرض', items: 'code | visualaid visualchars visualblocks | preview fullscreen' },
            insert: { title: 'إدراج', items: 'image link media codesample | charmap emoticons hr | pagebreak nonbreaking anchor | insertdatetime' },
            format: { title: 'تنسيق', items: 'bold italic underline strikethrough | formats blockformats fontformats fontsizes align | forecolor backcolor | removeformat' },
            tools: { title: 'أدوات', items: 'code wordcount' },
            table: { title: 'جدول', items: 'inserttable | cell row column | tableprops deletetable' },
            help: { title: 'تعليمات', items: 'help' }
        },
        content_style: 'body { font-family: "Segoe UI", Tahoma, Arial, sans-serif; font-size: 14px; direction: rtl; }',
        elementpath: true,
        resize: true,
        contextmenu: 'link image table',
        paste_as_text: false,
        paste_data_images: true,
        relative_urls: false,
        remove_script_host: false,
        image_advtab: true,
        image_uploadtab: true,
        automatic_uploads: true,
        images_upload_url: '/upload',
        media_live_embeds: true,
        codesample_languages: [
            { text: 'HTML/XML', value: 'markup' },
            { text: 'JavaScript', value: 'javascript' },
            { text: 'CSS', value: 'css' },
            { text: 'PHP', value: 'php' },
            { text: 'Python', value: 'python' },
            { text: 'JSON', value: 'json' },
            { text: 'Bash/Shell', value: 'bash' }
        ],
        codesample_global_prismjs: true,
        setup: function(editor) {
            editor.on('init', function() {});
            editor.on('error', function(e) { console.error('TinyMCE error:', e); });
        }
    }).catch(function(err) { console.error('TinyMCE init error:', err); });
}
document.addEventListener('DOMContentLoaded', function() { setTimeout(initProductDescriptionEditor, 200); });
</script>
<script>
(function() {
    const cb = document.getElementById('is_digital_cb');
    const section = document.getElementById('digital-files-section');
    const tbody = document.getElementById('digital-files-tbody');
    const addBtn = document.getElementById('add-digital-file-row');

    function toggleSection() {
        if (!cb || !section) return;
        section.style.display = cb.checked ? '' : 'none';
    }

    function nextIndex() {
        return tbody.querySelectorAll('tr').length;
    }

    if (cb) cb.addEventListener('change', toggleSection);
    toggleSection();

    if (addBtn) addBtn.addEventListener('click', function() {
        const idx = nextIndex();
        const html = `
            <tr>
                <td><input type="text" class="form-control" name="digital_files[${idx}][title]" placeholder="مثال: ملف PDF"></td>
                <td><input type="file" class="form-control" name="digital_files[${idx}][file]"></td>
                <td><input type="number" min="0" class="form-control" name="digital_files[${idx}][order]" value="0"></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-digital-file">حذف</button></td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', html);
    });

    if (tbody) tbody.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-digital-file')) {
            e.target.closest('tr').remove();
        }
    });
})();
</script>
@endsection
