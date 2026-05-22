@php
    $filterAction = $filterAction ?? route('frontend.shop.index');
    $maxPrice = $maxProductPrice ?? 2000;
    $currentMax = (int) request('max_price', $maxPrice);
    $currentMin = (int) request('min_price', 0);
    $selectedCategory = request('category', $activeCategorySlug ?? '');
@endphp
<aside class="col-lg-3">
    <form method="GET" action="{{ $filterAction }}" id="shop-filters-form" class="glass-panel p-4 sticky-top section-fade-up" style="top: 100px; z-index: 10;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold m-0"><i class="fas fa-filter text-accent me-2"></i> تصفية</h5>
            <a href="{{ $filterAction }}" id="shop-filters-reset" class="btn btn-sm btn-outline-secondary py-0 border-0" style="font-size: 0.85rem">إعادة ضبط</a>
        </div>

        <div class="mb-4">
            <label class="form-label small text-secondary" for="search-input">بحث</label>
            <input type="text" id="search-input" name="search" value="{{ request('search') }}"
                class="form-control bg-glass text-white rounded-3 border-secondary" placeholder="ابحث عن منتج...">
        </div>

        <div class="mb-4">
            <h6 class="fw-bold mb-3">التصنيف</h6>
            <div class="form-check mb-2">
                <input class="form-check-input bg-glass border-secondary" type="radio" name="category" value=""
                    id="cat-all" @checked($selectedCategory === '')>
                <label class="form-check-label text-secondary" for="cat-all">جميع التصنيفات</label>
            </div>
            @foreach($categories as $category)
            <div class="form-check mb-2">
                <input class="form-check-input bg-glass border-secondary" type="radio" name="category"
                    value="{{ $category->slug }}" id="cat-{{ $category->slug }}"
                    @checked($selectedCategory === $category->slug)>
                <label class="form-check-label text-secondary" for="cat-{{ $category->slug }}">{{ $category->name }}</label>
            </div>
            @endforeach
        </div>

        <hr class="border-secondary border-opacity-25 mb-4">

        <div class="mb-4">
            <h6 class="fw-bold mb-3">السعر (حتى: <span id="price-val" class="text-accent en-text fw-bold">{{ $currentMax }}</span> ر.س)</h6>
            <input type="range" class="form-range" name="max_price" min="0" max="{{ $maxPrice }}"
                value="{{ min($currentMax, $maxPrice) }}" id="price-range"
                oninput="document.getElementById('price-val').textContent = this.value">
            <input type="hidden" name="min_price" value="{{ $currentMin }}">
        </div>

        <hr class="border-secondary border-opacity-25 mb-4">

        <div class="mb-4">
            <h6 class="fw-bold mb-3">العلامة التجارية</h6>
            <div class="form-check mb-2">
                <input class="form-check-input bg-glass border-secondary" type="radio" name="brand" value=""
                    id="brand-all" @checked(!request('brand'))>
                <label class="form-check-label text-secondary" for="brand-all">الكل</label>
            </div>
            @foreach($brands as $brand)
            <div class="form-check mb-2">
                <input class="form-check-input bg-glass border-secondary" type="radio" name="brand"
                    value="{{ $brand->slug }}" id="brand-{{ $brand->slug }}"
                    @checked(request('brand') === $brand->slug)>
                <label class="form-check-label text-secondary" for="brand-{{ $brand->slug }}">{{ $brand->name }}</label>
            </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-accent w-100 rounded-3 fw-bold">تطبيق التصفية</button>
    </form>
</aside>
