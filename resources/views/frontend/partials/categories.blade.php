<section class="py-5 bg-gradient-opacity section-fade-up">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h6 class="text-accent fw-bold text-uppercase tracking-wide">أبرز الأقسام</h6>
                <h2 class="fw-bold m-0">تسوق حسب التصنيف</h2>
            </div>
            <a href="{{ route('frontend.shop.index') }}" class="btn btn-outline-light rounded-pill px-4 d-none d-md-block">عرض الكل</a>
        </div>
        <div class="row g-3 g-md-4 categories-home-row">
            @forelse($categories->take(6) as $category)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('frontend.category.show', $category->slug) }}" class="category-card-link d-block text-decoration-none">
                    <div class="glass-card category-card category-card--home category-card--visual">
                        <span class="elegant-card__shine" aria-hidden="true"></span>
                        <div class="category-card__media category-card__media--square">
                            <img src="{{ $category->image_url }}"
                                 alt="{{ $category->name }}"
                                 loading="lazy"
                                 decoding="async">
                        </div>
                        <div class="category-card__footer">
                            <h6 class="category-card__name">{{ $category->name }}</h6>
                            <span class="category-card__hint">استكشف <i class="fas fa-arrow-left ms-1 small"></i></span>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center py-4">
                <p class="text-secondary">لا توجد تصنيفات حالياً</p>
            </div>
            @endforelse
        </div>
        <div class="text-center mt-4 d-md-none">
            <a href="{{ route('frontend.shop.index') }}" class="btn btn-outline-light rounded-pill px-4">عرض الكل</a>
        </div>
    </div>
</section>
