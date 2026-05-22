<section class="py-5 bg-gradient-opacity section-fade-up">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h6 class="text-accent fw-bold text-uppercase tracking-wide">أبرز الأقسام</h6>
                <h2 class="fw-bold m-0">تسوق حسب التصنيف</h2>
            </div>
            <a href="{{ route('frontend.shop.index') }}" class="btn btn-outline-light rounded-pill px-4 d-none d-md-block">عرض الكل</a>
        </div>
        <div class="row g-4">
            @forelse($categories->take(6) as $category)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('frontend.category.show', $category->slug) }}" class="text-decoration-none">
                    <div class="glass-card category-card h-100">
                        <div class="category-icon">
                            @if($category->icon)
                                <i class="{{ $category->icon }}"></i>
                            @else
                                <i class="fas fa-box"></i>
                            @endif
                        </div>
                        <h6 class="fw-bold text-white m-0">{{ $category->name }}</h6>
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
