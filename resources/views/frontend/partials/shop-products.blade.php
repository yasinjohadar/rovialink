<div class="row g-3 section-fade-up shop-products-grid" id="all-products-container" data-server-rendered="1">
    @forelse($products as $product)
        @include('frontend.partials.product-card', [
            'product' => $product,
            'columnClass' => 'col-sm-6 col-lg-4',
        ])
    @empty
    <div class="col-12 shop-products-empty">
        <i class="fas fa-box-open" aria-hidden="true"></i>
        <h5>لا توجد منتجات</h5>
        <p>جرب تغيير معايير البحث أو التصفية</p>
    </div>
    @endforelse
</div>
