@php
    $aiModels = $aiModels ?? collect();
@endphp
<div class="card custom-card mb-4 border-primary-subtle">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="card-title mb-0">
            <i class="bi bi-stars text-primary me-1"></i> الذكاء الاصطناعي (Laravel AI)
        </div>
        @if($aiModels->isNotEmpty())
        <select id="product-ai-model" class="form-select form-select-sm" style="max-width: 220px;">
            @foreach($aiModels as $m)
                <option value="{{ $m->id }}" @selected($m->is_default)>{{ $m->name }}</option>
            @endforeach
        </select>
        @endif
    </div>
    <div class="card-body">
        @if($aiModels->isEmpty())
            <p class="text-muted small mb-0">
                لا يوجد نموذج AI نشط.
                <a href="{{ route('admin.ai.models.create') }}">أضف نموذجاً</a>
            </p>
        @else
            <div class="mb-2">
                <label class="form-label small mb-1">ملاحظات للذكاء الاصطناعي (اختياري)</label>
                <textarea id="product-ai-features" class="form-control form-control-sm" rows="2"
                    placeholder="مثال: إضافة Elementor، متوافقة مع ووردبريس 6+، 40 ويدجت، دعم RTL..."></textarea>
                <small class="text-muted d-block">كلما أضفت تفاصيل، كان الوصف الشامل أدق.</small>
                <small class="text-muted d-block">للوصف الطويل: اضبط «الحد الأقصى للـ tokens» في نموذج AI على 8000 أو أكثر.</small>
            </div>
            <div class="d-flex flex-wrap gap-2 mb-2">
                <button type="button" class="btn btn-outline-primary btn-sm" id="btn-ai-product-copy">
                    <i class="bi bi-magic"></i> توليد وصف شامل
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-ai-product-seo">
                    <i class="bi bi-search"></i> تحسين SEO
                </button>
            </div>
            <div id="product-ai-status" class="small text-muted"></div>
        @endif
    </div>
</div>

@push('scripts')
<script>
(function () {
    const copyUrl = @json(route('admin.ai.products.generate-copy'));
    const seoUrl = @json(route('admin.ai.products.generate-seo'));
    const csrf = @json(csrf_token());

    function status(msg, isError) {
        const el = document.getElementById('product-ai-status');
        if (!el) return;
        el.textContent = msg;
        el.className = 'small ' + (isError ? 'text-danger' : 'text-success');
    }

    function modelId() {
        const sel = document.getElementById('product-ai-model');
        return sel ? sel.value : null;
    }

    function payloadBase() {
        const name = document.querySelector('[name="name"]')?.value?.trim();
        const categoryId = document.querySelector('[name="category_id"]')?.value;
        const price = document.querySelector('[name="price"]')?.value;
        const features = document.getElementById('product-ai-features')?.value?.trim() || '';
        const shortDescription = document.querySelector('[name="short_description"]')?.value?.trim() || '';
        return { name, category_id: categoryId, price, features, short_description: shortDescription, ai_model_id: modelId() };
    }

    function isBadDescription(html) {
        if (!html || typeof html !== 'string') return true;
        const plain = html.replace(/<[^>]+>/g, '').trim();
        if (plain.length < 200) return true;
        return /^\|?\s*(short_description|description)\s*$/i.test(plain);
    }

    async function parseJsonResponse(res) {
        const contentType = res.headers.get('content-type') || '';
        const text = await res.text();

        if (contentType.includes('application/json') || (text.trim().startsWith('{') || text.trim().startsWith('['))) {
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('رد الخادم غير صالح (JSON تالف).');
            }
        }

        if (res.status === 419) {
            throw new Error('انتهت الجلسة. حدّث الصفحة وسجّل الدخول مرة أخرى.');
        }
        if (res.status === 504 || res.status === 502) {
            throw new Error('انتهت مهلة الخادم (504/502). التوليد يستغرق وقتاً — جرّب نموذجاً أسرع أو زِد مهلة PHP/nginx على الاستضافة.');
        }
        if (res.status === 401 || res.status === 403) {
            throw new Error('غير مصرح. تأكد من تسجيل الدخول كمسؤول.');
        }

        throw new Error('الخادم أعاد صفحة HTML بدل JSON (غالباً خطأ استضافة أو مهلة). راجع سجل Laravel.');
    }

    async function post(url, body) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(body),
        });
        const data = await parseJsonResponse(res);
        if (!res.ok || !data.success) {
            throw new Error(data.message || 'فشل الطلب');
        }
        return data.data;
    }

    document.getElementById('btn-ai-product-copy')?.addEventListener('click', async function () {
        const base = payloadBase();
        if (!base.name) {
            status('أدخل اسم المنتج أولاً', true);
            return;
        }

        const shortEl = document.querySelector('[name="short_description"]');
        const descEl = document.querySelector('[name="description"]');
        const editor = window.tinymce?.get('description');
        const btn = this;

        btn.disabled = true;

        try {
            status('الخطوة 1/2: جاري توليد الوصف المختصر...');
            const shortData = await post(copyUrl, { ...base, step: 'short' });

            if (shortEl && shortData.short_description) {
                shortEl.value = shortData.short_description;
                base.short_description = shortData.short_description;
            }

            status('الخطوة 2/2: جاري توليد الوصف الكامل (قد يستغرق دقيقة)...');
            const longData = await post(copyUrl, { ...base, step: 'description' });

            if (longData.description && !isBadDescription(longData.description)) {
                if (descEl) descEl.value = longData.description;
                if (editor) {
                    editor.setContent(longData.description);
                    editor.save();
                }
                status('تم توليد الوصف المختصر والكامل بنجاح');
            } else if (longData.description && isBadDescription(longData.description)) {
                status('الوصف الكامل ضعيف من النموذج — جرّب gemini-2.0-flash أو زِد max tokens.', true);
            } else {
                status('تم الوصف المختصر فقط — فشل الوصف الطويل. غيّر النموذج أو أعد المحاولة.', true);
            }
        } catch (e) {
            status(e.message, true);
        } finally {
            btn.disabled = false;
        }
    });

    document.getElementById('btn-ai-product-seo')?.addEventListener('click', async function () {
        const base = payloadBase();
        if (!base.name) {
            status('أدخل اسم المنتج أولاً', true);
            return;
        }
        const description = document.querySelector('[name="description"]')?.value || base.name;
        status('جاري تحسين SEO...');
        this.disabled = true;
        try {
            const data = await post(seoUrl, { ...base, description });
            const map = {
                meta_title: data.meta_title,
                meta_description: data.meta_description,
                meta_keywords: data.meta_keywords,
            };
            Object.keys(map).forEach(function (key) {
                const el = document.querySelector('[name="' + key + '"]');
                if (el && map[key]) el.value = map[key];
            });
            status('تم تحديث حقول SEO');
        } catch (e) {
            status(e.message, true);
        } finally {
            this.disabled = false;
        }
    });
})();
</script>
@endpush
