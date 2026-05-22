@extends('admin.layouts.master')

@section('page-title')
    تعديل قالب إيميل
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
                    <h4 class="mb-0">تعديل قالب: {{ $template->name }}</h4>
                    <p class="mb-0 text-muted">استخدم المتغيرات مثل <code>&#123;&#123; order.id &#125;&#125;</code> و <code>&#123;&#123; customer.name &#125;&#125;</code></p>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-right me-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.email-templates.update', $template) }}" id="email-template-form">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card custom-card mb-4">
                            <div class="card-header">
                                <div class="card-title">المعلومات الأساسية</div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">المفتاح (فريد) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('key') is-invalid @enderror" name="key" value="{{ old('key', $template->key) }}" required>
                                        @error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الاسم المعروض <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $template->name) }}" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الحدث <span class="text-danger">*</span></label>
                                        <select name="event" class="form-select @error('event') is-invalid @enderror" required>
                                            @foreach($events as $key => $label)
                                                <option value="{{ $key }}" {{ old('event', $template->event) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('event')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">اللغة <span class="text-danger">*</span></label>
                                        <select name="locale" class="form-select @error('locale') is-invalid @enderror" required>
                                            @foreach($locales as $code => $label)
                                                <option value="{{ $code }}" {{ old('locale', $template->locale) === $code ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('locale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">موضوع الرسالة <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('subject') is-invalid @enderror" name="subject" value="{{ old('subject', $template->subject) }}" required>
                                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">وصف (اختياري)</label>
                                        <input type="text" class="form-control" name="description" value="{{ old('description', $template->description) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card custom-card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                <div class="card-title mb-0">محتوى الرسالة (HTML)</div>
                                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-editor" role="tab">محرر نصوص</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#tab-html" role="tab">HTML</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="tab-editor" role="tabpanel">
                                        <textarea name="body_html" id="body_html" class="form-control @error('body_html') is-invalid @enderror" rows="12">{{ old('body_html', $template->body_html) }}</textarea>
                                        @error('body_html')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="tab-pane fade" id="tab-html" role="tabpanel">
                                        <textarea id="body_html_raw" class="form-control font-monospace" rows="16" dir="ltr">{{ old('body_html', $template->body_html) }}</textarea>
                                        <p class="small text-muted mt-2">تعديل HTML مباشرة. سيتم مزامنته مع المحرر عند التبديل بين التبويبات.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card custom-card mb-4">
                            <div class="card-header"><div class="card-title">الحالة</div></div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">القالب نشط ويُستخدم للإرسال</label>
                                </div>
                            </div>
                        </div>
                        <div class="card custom-card mb-4">
                            <div class="card-header"><div class="card-title">متغيرات متاحة</div></div>
                            <div class="card-body small">
                                <p class="mb-2">استخدم الصيغة <code>&#123;&#123; اسم_المتغير &#125;&#125;</code> في الموضوع والمحتوى.</p>
                                <ul class="mb-0 ps-3">
                                    <li><code>customer.name</code>, <code>customer.email</code></li>
                                    <li><code>order.id</code>, <code>order.total</code>, <code>order.status</code></li>
                                    <li><code>return.id</code>, <code>return.status</code> (للمرتجعات)</li>
                                    <li><code>user.name</code>, <code>reset_link</code> (للمستخدم وكلمة المرور)</li>
                                </ul>
                            </div>
                        </div>
                        <div class="card custom-card">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-save me-2"></i>
                                    حفظ التعديلات
                                </button>
                                <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary w-100">إلغاء</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('script')
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var bodyEl = document.getElementById('body_html');
    var rawEl = document.getElementById('body_html_raw');
    var tabEditor = document.querySelector('#tab-editor');
    var tabHtml = document.querySelector('#tab-html');

    function initTinyMCE() {
        if (typeof tinymce === 'undefined') {
            setTimeout(initTinyMCE, 100);
            return;
        }
        tinymce.init({
            selector: '#body_html',
            height: 380,
            directionality: 'rtl',
            language: 'ar',
            language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@latest/langs6/ar.js',
            promotion: false,
            branding: false,
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount directionality',
            toolbar: 'undo redo | blocks | bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | link image media table | code fullscreen | help',
            menubar: 'file edit view insert format tools table help',
            content_style: 'body { font-family: "Segoe UI", Tahoma, Arial, sans-serif; font-size: 14px; direction: rtl; }',
            resize: true,
            setup: function(editor) {
                editor.on('init', function() {
                    if (rawEl) rawEl.value = editor.getContent();
                });
            }
        }).then(function(editors) {
            var editor = editors[0];
            if (!editor || !rawEl) return;
            document.querySelector('a[href="#tab-html"]').addEventListener('shown.bs.tab', function() {
                rawEl.value = editor.getContent();
            });
            document.querySelector('a[href="#tab-editor"]').addEventListener('shown.bs.tab', function() {
                editor.setContent(rawEl.value);
            });
        }).catch(function(err) { console.error('TinyMCE error:', err); });
    }

    document.getElementById('email-template-form').addEventListener('submit', function() {
        if (typeof tinymce !== 'undefined') {
            var ed = tinymce.get('body_html');
            if (ed) ed.save();
        }
        if (document.querySelector('#tab-html.active') && rawEl && bodyEl) {
            bodyEl.value = rawEl.value;
        }
    });

    setTimeout(initTinyMCE, 200);
});
</script>
@endsection
