@extends('admin.layouts.master')

@section('page-title')
    إنشاء كوبون جديد
@stop

@section('styles')
<style>
    .coupon-form-section {
        background: var(--default-body-bg-color, #f8f9fa);
        border-radius: 12px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--default-border-color, rgba(0,0,0,.08));
    }
    .coupon-form-section:last-of-type { margin-bottom: 0; }
    .coupon-section-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--default-text-color, #1e293b);
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--primary-color, #4f46e5);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .coupon-section-title i { opacity: 0.9; }
    .coupon-form .form-label {
        font-weight: 500;
        color: var(--default-text-color, #334155);
    }
    .coupon-form .form-control,
    .coupon-form .form-select {
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
    }
    .coupon-form .form-control:focus,
    .coupon-form .form-select:focus {
        border-color: var(--primary-color, #4f46e5);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }
    .coupon-form-actions {
        padding-top: 1.5rem;
        margin-top: 1rem;
        border-top: 1px solid var(--default-border-color, rgba(0,0,0,.08));
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .page-header-coupon {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .page-header-coupon .page-title { margin-bottom: 0; }
    .btn-create-coupon {
        border-radius: 8px;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .hint-text {
        font-size: 0.8125rem;
        color: var(--default-text-color-muted, #64748b);
        margin-top: 0.25rem;
    }
</style>
@endsection

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
            <div class="page-header-coupon">
                <h5 class="page-title fs-5 fw-semibold">إنشاء كوبون جديد</h5>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-create-coupon">
                    <i class="ri-arrow-right-line"></i>
                    العودة للقائمة
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.coupons.store') }}" class="coupon-form">
                        @csrf

                        <div class="coupon-form-section">
                            <div class="coupon-section-title">
                                <i class="ri-coupon-2-line"></i>
                                المعلومات الأساسية
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">الكود <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" placeholder="مثال: WELCOME10" dir="ltr" style="text-align: left;">
                                    <p class="hint-text">اتركه فارغاً لإنشاء كود عشوائي تلقائياً</p>
                                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">اسم الكوبون <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="مثال: خصم ترحيبي">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">الوصف <span class="text-muted">(اختياري)</span></label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="وصف مختصر للكوبون">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="coupon-form-section">
                            <div class="coupon-section-title">
                                <i class="ri-percent-line"></i>
                                نوع الخصم والقيمة
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">نوع الخصم <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select" required>
                                        <option value="percentage" {{ old('type', 'percentage') == 'percentage' ? 'selected' : '' }}>نسبة مئوية (%)</option>
                                        <option value="fixed_amount" {{ old('type') == 'fixed_amount' ? 'selected' : '' }}>مبلغ ثابت (ر.س)</option>
                                        <option value="buy_x_get_y" {{ old('type') == 'buy_x_get_y' ? 'selected' : '' }}>اشترِ واحصل على</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">القيمة <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="value" value="{{ old('value') }}" required placeholder="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">حد أدنى للطلب <span class="text-muted">(اختياري)</span></label>
                                    <input type="number" min="0" step="0.01" class="form-control" name="minimum_order_amount" value="{{ old('minimum_order_amount', 0) }}" placeholder="0 ر.س">
                                </div>
                            </div>
                        </div>

                        <div class="coupon-form-section">
                            <div class="coupon-section-title">
                                <i class="ri-store-2-line"></i>
                                ينطبق الكوبون على
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">نطاق التطبيق <span class="text-danger">*</span></label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <label class="form-check">
                                            <input type="radio" name="applicable_to" value="entire_store" class="form-check-input" {{ old('applicable_to', 'entire_store') === 'entire_store' ? 'checked' : '' }}>
                                            <span class="form-check-label">عام على كل المتجر</span>
                                        </label>
                                        <label class="form-check">
                                            <input type="radio" name="applicable_to" value="specific_products" class="form-check-input" {{ old('applicable_to') === 'specific_products' ? 'checked' : '' }}>
                                            <span class="form-check-label">منتجات محددة</span>
                                        </label>
                                        <label class="form-check">
                                            <input type="radio" name="applicable_to" value="specific_categories" class="form-check-input" {{ old('applicable_to') === 'specific_categories' ? 'checked' : '' }}>
                                            <span class="form-check-label">تصنيفات محددة</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12" id="coupon-products-wrap" style="display: {{ old('applicable_to') === 'specific_products' ? 'block' : 'none' }};">
                                    <label class="form-label">اختر المنتجات <span class="text-danger">*</span></label>
                                    <select name="product_ids[]" id="coupon-product-ids" class="form-select" multiple size="8">
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}" {{ in_array($p->id, old('product_ids', [])) ? 'selected' : '' }}>{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="hint-text">استخدم Ctrl أو Shift لاختيار أكثر من منتج</p>
                                </div>
                                <div class="col-12" id="coupon-categories-wrap" style="display: {{ old('applicable_to') === 'specific_categories' ? 'block' : 'none' }};">
                                    <label class="form-label">اختر التصنيفات <span class="text-danger">*</span></label>
                                    <select name="category_ids[]" id="coupon-category-ids" class="form-select" multiple size="8">
                                        @foreach($categories as $c)
                                            <option value="{{ $c->id }}" {{ in_array($c->id, old('category_ids', [])) ? 'selected' : '' }}>{{ $c->parent_id ? '— ' : '' }}{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="hint-text">استخدم Ctrl أو Shift لاختيار أكثر من تصنيف</p>
                                </div>
                            </div>
                        </div>
                            <div class="coupon-section-title">
                                <i class="ri-calendar-check-line"></i>
                                الاستخدام والصلاحية
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">حد الاستخدام الكلي <span class="text-muted">(اختياري)</span></label>
                                    <input type="number" min="1" class="form-control" name="usage_limit" value="{{ old('usage_limit', 1) }}" placeholder="1">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">حالة الكوبون <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                        <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>منتهي الصلاحية</option>
                                    </select>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-6">
                                    <label class="form-label">تاريخ البدء <span class="text-muted">(اختياري)</span></label>
                                    <input type="date" class="form-control" name="starts_at" value="{{ old('starts_at') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">تاريخ الانتهاء <span class="text-muted">(اختياري)</span></label>
                                    <input type="date" class="form-control" name="expires_at" value="{{ old('expires_at') }}">
                                </div>
                            </div>
                        </div>

                        <div class="coupon-form-actions">
                            <button type="submit" class="btn btn-primary btn-create-coupon">
                                <i class="ri-add-circle-line"></i>
                                إنشاء الكوبون
                            </button>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-create-coupon">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.querySelectorAll('input[name="applicable_to"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var productsWrap = document.getElementById('coupon-products-wrap');
            var categoriesWrap = document.getElementById('coupon-categories-wrap');
            productsWrap.style.display = this.value === 'specific_products' ? 'block' : 'none';
            categoriesWrap.style.display = this.value === 'specific_categories' ? 'block' : 'none';
            if (this.value !== 'specific_products') document.getElementById('coupon-product-ids').selectedIndex = -1;
            if (this.value !== 'specific_categories') document.getElementById('coupon-category-ids').selectedIndex = -1;
        });
    });
    </script>
@endsection
