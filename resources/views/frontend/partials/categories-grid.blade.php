<div class="row g-4 section-fade-up" id="categories-grid">
    @forelse($categories as $category)
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('frontend.category.show', $category->slug) }}" class="text-decoration-none d-block h-100">
            <div class="glass-card p-4 h-100 text-center position-relative category-card-interactive">
                <span class="elegant-card__shine" aria-hidden="true"></span>
                @if($category->image)
                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="rounded-3 mb-3 position-relative z-1 category-card-interactive__img" style="height: 80px; width: 80px; object-fit: cover;">
                @else
                    <div class="elegant-card__icon-wrap category-icon mx-auto mb-3 position-relative z-1">
                        <i class="fas fa-folder-open category-icon__glyph"></i>
                    </div>
                @endif
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
