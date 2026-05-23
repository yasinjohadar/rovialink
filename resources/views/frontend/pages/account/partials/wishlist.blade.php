<div class="dashboard-section d-none" id="section-wishlist">
    <div class="glass-card p-4 section-fade-up">
        <h5 class="account-panel__title mb-4"><i class="fas fa-heart me-2" aria-hidden="true"></i> قائمة المفضلة</h5>
        <div class="row g-3" id="dashboard-wishlist-items">
            @forelse($wishlistProducts as $product)
            <div class="col-md-6 col-lg-4">
                <div class="glass-card h-100 d-flex flex-column product-card position-relative">
                    <form method="POST" action="{{ route('frontend.account.wishlist.remove', $product) }}" class="position-absolute top-0 end-0 m-2" style="z-index:2;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger rounded-circle" style="width:28px;height:28px;padding:0;" title="إزالة">
                            <i class="fas fa-times small"></i>
                        </button>
                    </form>
                    <a href="{{ route('frontend.product.show', $product->slug) }}" class="product-img text-white text-center d-block overflow-hidden rounded-top">
                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
                    </a>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h6 class="fw-bold text-white mb-2">
                            <a href="{{ route('frontend.product.show', $product->slug) }}" class="text-white text-decoration-none">{{ Str::limit($product->name, 50) }}</a>
                        </h6>
                        <div class="product-price mt-auto">
                            <span class="current-price text-accent fw-bold">{{ number_format($product->effective_price, 2) }} ر.س</span>
                            @if($product->has_discount)
                            <span class="original-price ms-2 text-secondary text-decoration-line-through small">{{ number_format($product->compare_at_price, 2) }}</span>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('frontend.cart.store') }}" class="js-add-to-cart-form mt-2">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-sm btn-accent rounded-pill w-100">
                                <i class="fas fa-cart-plus me-1"></i> أضف للسلة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="far fa-heart fa-3x text-secondary opacity-50 mb-3 d-block"></i>
                <p class="text-secondary">قائمة المفضلة فارغة</p>
                <a href="{{ route('frontend.shop.index') }}" class="btn btn-sm btn-accent rounded-pill px-3">تصفح المنتجات</a>
            </div>
            @endforelse
        </div>
    </div>
</div>
