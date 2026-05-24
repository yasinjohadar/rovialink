@extends('admin.layouts.master')

@section('page-title')
    تعديل المنتج
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
                <h5 class="page-title mb-0">تعديل: {{ $product->name }}</h5>
                <div>
                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-info">عرض</a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">العودة</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12"><h6 class="text-primary mb-2">المعلومات الأساسية</h6></div>
                            <div class="col-md-6">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الرابط (Slug)</label>
                                <input type="text" class="form-control" name="slug" value="{{ old('slug', $product->slug) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">التصنيف</label>
                                <select name="category_id" class="form-select">
                                    <option value="">-- اختر --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الماركة</label>
                                <select name="brand_id" class="form-select">
                                    <option value="">— لا ماركة —</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SKU</label>
                                <input type="text" class="form-control" name="sku" value="{{ old('sku', $product->sku) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">وصف مختصر</label>
                                <textarea name="short_description" class="form-control" rows="2">{{ old('short_description', $product->short_description) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="10">{{ old('description', $product->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        @include('admin.pages.products.partials.ai-tools', ['aiModels' => $aiModels ?? collect()])
                        @include('admin.pages.products.partials.seo-fields', ['product' => $product])

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">السعر ($) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control" name="price" value="{{ old('price', $product->price) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">سعر المقارنة</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="compare_at_price" value="{{ old('compare_at_price', $product->compare_at_price) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الحالة</label>
                                <select name="status" class="form-select">
                                    <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="archived" {{ old('status', $product->status) == 'archived' ? 'selected' : '' }}>أرشيف</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input type="hidden" name="is_featured" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label">منتج مميز</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input type="hidden" name="is_visible" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_visible" value="1" {{ old('is_visible', $product->is_visible) ? 'checked' : '' }}>
                                    <label class="form-check-label">ظاهر في المتجر</label>
                                </div>
                            </div>
                            <div class="col-12"><hr><h6 class="text-primary mb-2">التقييمات والتعليقات</h6></div>
                            <div class="col-md-6">
                                @php
                                    $rra = old('reviews_require_approval');
                                    if ($rra === null || $rra === '') {
                                        $rra = $product->reviews_require_approval === null ? 'default' : ($product->reviews_require_approval ? '1' : '0');
                                    }
                                @endphp
                                <div class="form-check mt-2">
                                    <input type="hidden" name="allow_reviews" value="0">
                                    <input class="form-check-input" type="checkbox" name="allow_reviews" value="1" id="allow_reviews" {{ old('allow_reviews', $product->allow_reviews ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_reviews">السماح بالتعليقات والتقييمات لهذا المنتج</label>
                                </div>
                            </div>
                            <div class="col-md-6" id="reviews_approval_wrap">
                                <label class="form-label">نشر التعليقات</label>
                                <select name="reviews_require_approval" class="form-select">
                                    <option value="default" {{ $rra === 'default' ? 'selected' : '' }}>استخدام الإعداد الافتراضي للمتجر</option>
                                    <option value="1" {{ $rra === '1' ? 'selected' : '' }}>بعد موافقة الإدارة</option>
                                    <option value="0" {{ $rra === '0' ? 'selected' : '' }}>تلقائياً بدون موافقة</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label d-block">الصورة الرئيسية الحالية</label>
                                @php
                                    $primaryImage = $product->primary_image;
                                @endphp
                                @if($primaryImage)
                                    <div class="mb-2">
                                        <img src="{{ $primaryImage->url }}" alt="" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                    </div>
                                @else
                                    <p class="text-muted small">لم يتم تعيين صورة رئيسية بعد، سيتم استخدام أول صورة من المعرض.</p>
                                @endif
                                <div class="mb-3">
                                    <label class="form-label">تعيين صورة رئيسية جديدة (اختياري)</label>
                                    <input type="file" name="primary_image" class="form-control" accept="image/*">
                                    <small class="text-muted d-block mt-1">في حال اختيار صورة هنا سيتم استخدامها كصورة رئيسية جديدة.</small>
                                </div>
                                <label class="form-label">معرض الصور الحالي</label>
                                <div class="d-flex gap-2 flex-wrap mb-2">
                                    @foreach($product->images as $img)
                                        <div class="position-relative">
                                            <img src="{{ $img->url }}" alt="" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                            <form action="{{ route('admin.products.images.delete', [$product, $img]) }}" method="POST" class="position-absolute top-0 start-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger p-1" onclick="return confirm('حذف الصورة؟');">&times;</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                                <label class="form-label">إضافة صور جديدة للمعرض</label>
                                <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                            </div>

                            <div class="col-12"><hr><h6 class="text-primary mb-2">المنتج الرقمي</h6></div>
                            <div class="col-md-4">
                                <div class="form-check mt-2">
                                    <input type="hidden" name="is_digital" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_digital" value="1" id="is_digital_cb"
                                           {{ old('is_digital', $product->is_digital) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_digital_cb">منتج رقمي قابل للتحميل</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">حد مرات التحميل (اختياري)</label>
                                <input type="number" min="1" class="form-control" name="digital_download_limit" value="{{ old('digital_download_limit', $product->digital_download_limit) }}">
                                <small class="text-muted">اتركه فارغاً لعدد غير محدود</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">عدد أيام صلاحية التحميل (اختياري)</label>
                                <input type="number" min="1" class="form-control" name="digital_download_expiry_days" value="{{ old('digital_download_expiry_days', $product->digital_download_expiry_days) }}">
                                <small class="text-muted">اتركه فارغاً بدون انتهاء</small>
                            </div>

                            <div class="col-12" id="digital-files-section" style="{{ old('is_digital', $product->is_digital) ? '' : 'display:none;' }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="text-primary mb-2">ملفات التحميل</h6>
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
                                            @php
                                                $digitalFiles = old('digital_files', $product->files->map(fn($f) => ['id' => $f->id, 'title' => $f->title, 'order' => $f->order, 'url' => $f->url])->toArray());
                                            @endphp
                                            @foreach($digitalFiles as $idx => $df)
                                                <tr class="{{ !empty($df['delete']) ? 'd-none' : '' }}">
                                                    <td>
                                                        @if(!empty($df['id']))
                                                            <input type="hidden" name="digital_files[{{ $idx }}][id]" value="{{ $df['id'] }}">
                                                        @endif
                                                        <input type="hidden" class="digital-delete-flag" name="digital_files[{{ $idx }}][delete]" value="{{ $df['delete'] ?? 0 }}">
                                                        <input type="text" class="form-control" name="digital_files[{{ $idx }}][title]" value="{{ $df['title'] ?? '' }}" placeholder="مثال: ملف PDF">
                                                        @if(!empty($df['url']))
                                                            <a class="small d-block mt-1" href="{{ $df['url'] }}" target="_blank">عرض الملف الحالي</a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="file" class="form-control" name="digital_files[{{ $idx }}][file]">
                                                        <small class="text-muted">اتركه فارغاً للإبقاء على الملف الحالي</small>
                                                    </td>
                                                    <td>
                                                        <input type="number" min="0" class="form-control" name="digital_files[{{ $idx }}][order]" value="{{ $df['order'] ?? 0 }}">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-digital-file" data-has-id="{{ !empty($df['id']) ? 1 : 0 }}">حذف</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <small class="text-muted d-block">يمكنك إضافة ملفات جديدة أو استبدال الملفات الحالية.</small>
                            </div>

                            <div class="col-12"><hr><h6 class="text-primary mb-2">سمات هذا المنتج</h6></div>
                            <div class="col-12">
                                <p class="text-muted small">اختر السمات التي تنطبق على هذا المنتج (مثل اللون، المقاس) ثم أنشئ متغيرات لكل تركيبة.</p>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($attributes as $attr)
                                        <div class="form-check">
                                            <input class="form-check-input product-attribute-cb" type="checkbox" name="attribute_ids[]" value="{{ $attr->id }}" id="attr_{{ $attr->id }}"
                                                {{ in_array($attr->id, old('attribute_ids', $product->attributes->pluck('id')->toArray())) ? 'checked' : '' }}
                                                data-attribute-id="{{ $attr->id }}" data-values='@json($attr->values->map(fn($v) => ['id' => $v->id, 'value' => $v->value])->values())'>
                                            <label class="form-check-label" for="attr_{{ $attr->id }}">{{ $attr->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12"><hr><h6 class="text-primary mb-2">المتغيرات</h6></div>
                            <div class="col-12">
                                <p class="text-muted small">كل متغير = تركيبة قيم (مثلاً أحمر + مقاس M) مع سعر وSKU اختياري.</p>
                                <button type="button" class="btn btn-outline-primary btn-sm mb-2" id="btn-generate-variants">توليد متغيرات من السمات المختارة</button>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="variants-table">
                                        <thead>
                                            <tr>
                                                <th>السمات</th>
                                                <th style="width:120px">السعر</th>
                                                <th style="width:120px">SKU</th>
                                                <th style="width:80px"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="variants-tbody">
                                            @php $variantsData = old('variants', $product->variants->map(fn($v) => ['id' => $v->id, 'attribute_value_ids' => $v->attributeValues->pluck('id')->toArray(), 'price' => $v->price, 'sku' => $v->sku, 'display_name' => $v->display_name])->toArray()); @endphp
                                            @foreach($variantsData as $idx => $v)
                                                <tr>
                                                    <td>
                                                        {{ $v['display_name'] ?? '—' }}
                                                        @foreach(($v['attribute_value_ids'] ?? []) as $avid)
                                                            <input type="hidden" name="variants[{{ $idx }}][attribute_value_ids][]" value="{{ $avid }}">
                                                        @endforeach
                                                        @if(!empty($v['id']))
                                                            <input type="hidden" name="variants[{{ $idx }}][id]" value="{{ $v['id'] }}">
                                                        @endif
                                                    </td>
                                                    <td><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="variants[{{ $idx }}][price]" value="{{ $v['price'] ?? $product->price ?? '' }}"></td>
                                                    <td><input type="text" class="form-control form-control-sm" name="variants[{{ $idx }}][sku]" value="{{ $v['sku'] ?? '' }}"></td>
                                                    <td><button type="button" class="btn btn-sm btn-outline-danger remove-variant">حذف</button></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
    const digitalCb = document.getElementById('is_digital_cb');
    const digitalSection = document.getElementById('digital-files-section');
    const digitalTbody = document.getElementById('digital-files-tbody');
    const addDigitalBtn = document.getElementById('add-digital-file-row');

    const tbody = document.getElementById('variants-tbody');
    const productPrice = {{ json_encode($product->price ?? 0) }};

    function toggleDigital() {
        if (!digitalCb || !digitalSection) return;
        digitalSection.style.display = digitalCb.checked ? '' : 'none';
    }
    if (digitalCb) digitalCb.addEventListener('change', toggleDigital);
    toggleDigital();

    function nextDigitalIndex() {
        return digitalTbody ? digitalTbody.querySelectorAll('tr').length : 0;
    }
    if (addDigitalBtn && digitalTbody) addDigitalBtn.addEventListener('click', function() {
        const idx = nextDigitalIndex();
        const html = `
            <tr>
                <td>
                    <input type="hidden" class="digital-delete-flag" name="digital_files[${idx}][delete]" value="0">
                    <input type="text" class="form-control" name="digital_files[${idx}][title]" placeholder="مثال: ملف PDF">
                </td>
                <td>
                    <input type="file" class="form-control" name="digital_files[${idx}][file]">
                </td>
                <td>
                    <input type="number" min="0" class="form-control" name="digital_files[${idx}][order]" value="0">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-digital-file" data-has-id="0">حذف</button>
                </td>
            </tr>
        `;
        digitalTbody.insertAdjacentHTML('beforeend', html);
    });
    if (digitalTbody) digitalTbody.addEventListener('click', function(e) {
        if (!e.target.classList.contains('remove-digital-file')) return;
        const row = e.target.closest('tr');
        const hasId = e.target.getAttribute('data-has-id') === '1';
        if (hasId) {
            const del = row.querySelector('.digital-delete-flag');
            if (del) del.value = '1';
            row.classList.add('d-none');
        } else {
            row.remove();
        }
    });

    document.getElementById('btn-generate-variants').addEventListener('click', function() {
        const checked = document.querySelectorAll('.product-attribute-cb:checked');
        const attributes = [];
        checked.forEach(function(cb) {
            const id = parseInt(cb.dataset.attributeId, 10);
            let values = [];
            try { values = JSON.parse(cb.dataset.values || '[]'); } catch(e) {}
            if (values.length) attributes.push({ id, values });
        });
        if (attributes.length === 0) {
            alert('اختر سمة واحدة على الأقل أولاً.');
            return;
        }
        function cartesian(arrays) {
            if (arrays.length === 0) return [[]];
            const [first, ...rest] = arrays;
            const restProduct = cartesian(rest);
            return first.flatMap(v => restProduct.map(r => [v, ...r]));
        }
        const valueArrays = attributes.map(a => a.values);
        const combinations = cartesian(valueArrays);
        const nextIndex = tbody.querySelectorAll('tr').length;
        combinations.forEach(function(combo, i) {
            const idx = nextIndex + i;
            const parts = combo.map(v => v.value);
            const displayName = parts.join(' / ');
            let html = '<tr><td>' + displayName;
            combo.forEach(function(v) {
                html += '<input type="hidden" name="variants[' + idx + '][attribute_value_ids][]" value="' + v.id + '">';
            });
            html += '</td>';
            html += '<td><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="variants[' + idx + '][price]" value="' + productPrice + '"></td>';
            html += '<td><input type="text" class="form-control form-control-sm" name="variants[' + idx + '][sku]" value=""></td>';
            html += '<td><button type="button" class="btn btn-sm btn-outline-danger remove-variant">حذف</button></td></tr>';
            tbody.insertAdjacentHTML('beforeend', html);
        });
    });

    tbody.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variant')) {
            e.target.closest('tr').remove();
        }
    });
})();
</script>
@endsection
