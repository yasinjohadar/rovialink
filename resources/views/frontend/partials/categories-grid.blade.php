<div class="row g-4 section-fade-up" id="categories-grid">
    @forelse($categories as $category)
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('frontend.category.show', $category->slug) }}" class="text-decoration-none d-block h-100">
            <div class="glass-card p-4 h-100 text-center position-relative category-card-interactive">
                <span class="elegant-card__shine" aria-hidden="true"></span>
                <div class="category-card-interactive__media mx-auto mb-3 position-relative z-1">
                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="category-card-interactive__img">
                </div>
                <h4 class="fw-bold text-white position-relative z-1">{{ $category->name }}</h4>
                @if($category->description)
                    <p class="text-secondary small position-relative z-1 mb-4">{{ Str::limit($category->description, 100) }}</p>
                @endif
                <span class="badge bg-white bg-opacity-10 text-accent position-relative z-1">
                    {{ $category->products_count ?? 0 }} منتج
                </span>
                <div class="mt-3 position-relative z-1">
                    <span class="btn btn-outline-light rounded-pill px-4">تصفح المنتجات</span>
                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <h5 class="text-white">لا توجد تصنيفات متاحة حالياً</h5>
        <a href="{{ route('frontend.shop.index') }}" class="btn btn-accent mt-3">تصفح المتجر</a>
    </div>
    @endforelse
</div>

<div class="text-center mt-5 pb-4">
    <a href="{{ route('frontend.shop.index') }}" class="btn btn-outline-light rounded-pill px-5">تصفح كافة المنتجات</a>
</div>
