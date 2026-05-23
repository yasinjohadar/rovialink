<div class="dashboard-section d-none" id="section-wishlist">
    <div class="glass-card p-4 section-fade-up">
        <div class="account-panel__head">
            <h5 class="account-panel__title m-0"><i class="fas fa-heart me-2" aria-hidden="true"></i> قائمة المفضلة</h5>
            <a href="{{ route('frontend.wishlist') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">عرض الصفحة</a>
        </div>

        <div class="row g-3 g-md-4" id="dashboard-wishlist-items">
            @forelse($wishlistProducts as $product)
                @include('frontend.partials.product-card', [
                    'product' => $product,
                    'columnClass' => 'col-sm-6 col-lg-4',
                ])
            @empty
            <div class="col-12 shop-products-empty py-5">
                <i class="far fa-heart" aria-hidden="true"></i>
                <h5>قائمة المفضلة فارغة</h5>
                <p class="mb-3">تصفّح المتجر واضغط على أيقونة القلب لإضافة منتجات.</p>
                <a href="{{ route('frontend.shop.index') }}" class="btn btn-accent rounded-pill px-4">تصفح المنتجات</a>
            </div>
            @endforelse
        </div>
    </div>
</div>
