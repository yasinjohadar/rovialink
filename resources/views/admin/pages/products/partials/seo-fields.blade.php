<div class="card custom-card mb-4">
    <div class="card-header">
        <div class="card-title">تحسين محركات البحث (SEO)</div>
    </div>
    <div class="card-body">
        @include('admin.partials.seo-audit-panel', [
            'seoAuditType' => 'product',
            'aiModels' => $aiModels ?? collect(),
        ])
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">عنوان SEO</label>
                <input type="text" class="form-control" name="meta_title" value="{{ old('meta_title', $product->meta_title ?? '') }}" maxlength="255">
            </div>
            <div class="col-12">
                <label class="form-label">وصف SEO</label>
                <textarea name="meta_description" class="form-control" rows="2" maxlength="500">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">كلمات مفتاحية</label>
                <input type="text" class="form-control" name="meta_keywords" value="{{ old('meta_keywords', $product->meta_keywords ?? '') }}" placeholder="كلمة1, كلمة2">
            </div>
        </div>
    </div>
</div>
