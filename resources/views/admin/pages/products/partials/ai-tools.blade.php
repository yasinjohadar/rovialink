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
        return { name, category_id: categoryId, price, features, ai_model_id: modelId() };
    }

    async function post(url, body) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify(body),
        });
        const data = await res.json();
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
        status('جاري توليد وصف شامل (قد يستغرق دقيقة أو أكثر)...');
        this.disabled = true;
        try {
            const data = await post(copyUrl, base);
            const shortEl = document.querySelector('[name="short_description"]');
            const descEl = document.querySelector('[name="description"]');
            if (shortEl && data.short_description) shortEl.value = data.short_description;
            if (descEl && data.description) {
                descEl.value = data.description;
                if (window.tinymce?.get('description')) {
                    window.tinymce.get('description').setContent(data.description);
                }
            }
            status('تم توليد الوصف بنجاح');
        } catch (e) {
            status(e.message, true);
        } finally {
            this.disabled = false;
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
