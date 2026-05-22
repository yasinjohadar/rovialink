<div class="row g-4 section-fade-up" id="all-products-container" data-server-rendered="1">
    @forelse($products as $product)
        @include('frontend.partials.product-card', [
            'product' => $product,
            'columnClass' => 'col-sm-6 col-lg-4',
        ])
    @empty
    <div class="col-12 text-center py-5">
        <i class="fas fa-box-open fa-4x text-secondary opacity-50 mb-3"></i>
        <h5 class="text-white">لا توجد منتجات</h5>
        <p class="text-secondary">جرب تغيير معايير البحث أو التصفية</p>
    </div>
    @endforelse
</div>
