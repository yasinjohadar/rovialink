<div class="shop-toolbar section-fade-up">
    <div class="shop-toolbar__inner">
        <div class="shop-toolbar__meta">
            <span class="shop-toolbar__meta-icon" aria-hidden="true"><i class="fas fa-cubes"></i></span>
            <p class="shop-toolbar__count">
                عرض <strong class="en-text">{{ $products->total() }}</strong> منتج رقمي
            </p>
        </div>
        <div class="shop-toolbar__sort">
            <label class="shop-toolbar__sort-label" for="sort-select">
                <i class="fas fa-arrow-down-wide-short" aria-hidden="true"></i>
                ترتيب حسب
            </label>
            <div class="shop-toolbar__select-wrap">
                <select class="shop-toolbar__select"
                        id="sort-select"
                        name="sort"
                        form="shop-filters-form">
                    <option value="popular" @selected(request('sort', 'popular') === 'popular')>الأكثر شعبية</option>
                    <option value="newest" @selected(request('sort') === 'newest')>الأحدث</option>
                    <option value="price-asc" @selected(request('sort') === 'price-asc')>السعر: من الأقل للأعلى</option>
                    <option value="price-desc" @selected(request('sort') === 'price-desc')>السعر: من الأعلى للأقل</option>
                    <option value="rating" @selected(request('sort') === 'rating')>الأعلى تقييماً</option>
                </select>
                <i class="fas fa-chevron-down shop-toolbar__select-chevron" aria-hidden="true"></i>
            </div>
        </div>
    </div>
</div>
