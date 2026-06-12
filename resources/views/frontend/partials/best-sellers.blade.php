<section class="home-best-sellers-section section-fade-up">
    <div class="container py-4 py-lg-5">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h6 class="text-accent fw-bold text-uppercase tracking-wide">عروض حصرية</h6>
                <h2 class="fw-bold m-0">المنتجات الأكثر مبيعاً</h2>
            </div>
            <a href="{{ route('frontend.shop.index') }}" class="btn btn-outline-light rounded-pill px-4 d-none d-md-block">جميع المنتجات</a>
        </div>

        <div class="row g-3">
            @forelse($bestSellers as $product)
                @include('frontend.partials.product-card', [
                    'product' => $product,
                    'columnClass' => 'col-md-6 col-lg-4 col-xl-3',
                ])
            @empty
            <div class="col-12 shop-products-empty">
                <i class="fas fa-box-open" aria-hidden="true"></i>
                <h5>لا توجد منتجات حالياً</h5>
            </div>
            @endforelse
        </div>
    </div>

    <div class="home-section-wave home-section-wave--to-features" aria-hidden="true">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,48 C360,80 1080,16 1440,48 L1440,80 L0,80 Z"/>
        </svg>
    </div>
</section>
