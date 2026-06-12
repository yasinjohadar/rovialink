<div class="row g-3 section-fade-up shop-products-grid" id="all-products-container" data-server-rendered="1">
    @forelse($products as $product)
        @include('frontend.partials.product-card', [
            'product' => $product,
            'columnClass' => 'col-sm-6 col-lg-4',
        ])
    @empty
    <div class="col-12">
        <div class="shop-products-empty">
            <span class="shop-products-empty__icon" aria-hidden="true"><i class="fas fa-box-open"></i></span>
            <h5>لا توجد منتجات مطابقة</h5>
            <p>جرّب توسيع نطاق السعر أو اختيار تصنيف آخر</p>
            <a href="{{ route('frontend.shop.index') }}" class="btn btn-accent btn-sm shop-products-empty__btn">عرض كل المنتجات</a>
        </div>
    </div>
    @endforelse
</div>
