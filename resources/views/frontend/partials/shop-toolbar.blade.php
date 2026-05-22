<div class="glass-panel p-3 mb-4 section-fade-up d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
    <div class="text-secondary small">
        عرض <span class="text-white fw-bold en-text">{{ $products->total() }}</span> نتيجة
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center gap-2">
            <label class="text-secondary whitespace-nowrap mb-0 small text-nowrap" for="sort-select">ترتيب حسب:</label>
            <select class="form-select form-select-sm bg-glass text-white border-secondary rounded-3" id="sort-select"
                name="sort" form="shop-filters-form" style="min-width: 150px">
                <option value="popular" @selected(request('sort', 'popular') === 'popular')>الأكثر شعبية</option>
                <option value="newest" @selected(request('sort') === 'newest')>الأحدث</option>
                <option value="price-asc" @selected(request('sort') === 'price-asc')>السعر: من الأقل للأعلى</option>
                <option value="price-desc" @selected(request('sort') === 'price-desc')>السعر: من الأعلى للأقل</option>
                <option value="rating" @selected(request('sort') === 'rating')>الأعلى تقييماً</option>
            </select>
        </div>
    </div>
</div>
