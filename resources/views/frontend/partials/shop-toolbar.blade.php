<div class="shop-toolbar section-fade-up">
    <div class="shop-toolbar__inner">
        <p class="shop-toolbar__count">
            عرض <strong class="en-text">{{ $products->total() }}</strong> نتيجة
        </p>
        <div class="shop-toolbar__sort">
            <label class="shop-toolbar__sort-label" for="sort-select">ترتيب حسب:</label>
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
        </div>
    </div>
</div>
