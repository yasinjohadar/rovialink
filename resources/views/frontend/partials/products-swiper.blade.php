<section class="py-5 bg-gradient-opacity section-fade-up">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h6 class="text-accent fw-bold text-uppercase tracking-wide">وصل حديثاً</h6>
                <h2 class="fw-bold m-0">أحدث المنتجات</h2>
            </div>
            <a href="{{ route('frontend.shop.index') }}" class="btn btn-outline-light rounded-pill px-4 d-none d-md-block">جميع المنتجات</a>
        </div>

        <div class="swiper products-swiper home-catalog-swiper position-relative">
            <div class="swiper-wrapper">
                @forelse($newArrivals as $product)
                <div class="swiper-slide h-auto">
                    @include('frontend.partials.product-card', ['product' => $product, 'inSwiper' => true])
                </div>
                @empty
                <div class="swiper-slide">
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-4x text-secondary opacity-50 mb-3"></i>
                        <h5>لا توجد منتجات حالياً</h5>
                    </div>
                </div>
                @endforelse
            </div>

            <div class="swiper-pagination home-catalog-pagination products-pagination"></div>
            <div class="swiper-button-next home-catalog-next products-next"></div>
            <div class="swiper-button-prev home-catalog-prev products-prev"></div>
        </div>

        <div class="text-center mt-4 d-md-none">
            <a href="{{ route('frontend.shop.index') }}" class="btn btn-outline-light rounded-pill px-4">جميع المنتجات</a>
        </div>
    </div>
</section>
