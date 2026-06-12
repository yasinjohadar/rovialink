@php
    $filterAction = $filterAction ?? route('frontend.shop.index');
    $maxPrice = $maxProductPrice ?? 2000;
    $currentMax = (int) request('max_price', $maxPrice);
    $currentMin = (int) request('min_price', 0);
    $selectedCategory = request('category', $activeCategorySlug ?? '');
    $selectedBrand = request('brand', '');
    $searchQuery = request('search', '');
    $hasActiveFilters = filled($searchQuery)
        || filled($selectedCategory)
        || filled($selectedBrand)
        || $currentMax < $maxPrice;
@endphp
<aside class="col-lg-3">
    <form method="GET"
          action="{{ $filterAction }}"
          id="shop-filters-form"
          class="shop-filters section-fade-up sticky-top">
        <div class="shop-filters__card">
            <header class="shop-filters__header">
                <div class="shop-filters__title">
                    <span class="shop-filters__title-icon" aria-hidden="true"><i class="fas fa-sliders"></i></span>
                    <div>
                        <h2 class="shop-filters__heading">تصفية المنتجات</h2>
                        <p class="shop-filters__tagline">ابحث وحدّد ما يناسبك</p>
                    </div>
                </div>
                <a href="{{ $filterAction }}" id="shop-filters-reset" class="shop-filters__reset" @if(! $hasActiveFilters) hidden @endif>
                    <i class="fas fa-rotate-left" aria-hidden="true"></i>
                    <span>إعادة ضبط</span>
                </a>
            </header>

            @if($hasActiveFilters)
                <div class="shop-filters__active" id="shop-filters-active">
                    <span class="shop-filters__active-label">مفعّل الآن</span>
                    <div class="shop-filters__active-chips">
                        @if($searchQuery)
                            <span class="shop-filters__active-chip"><i class="fas fa-search"></i> {{ Str::limit($searchQuery, 24) }}</span>
                        @endif
                        @if($selectedCategory)
                            @php $catLabel = $categories->firstWhere('slug', $selectedCategory)?->name ?? $selectedCategory; @endphp
                            <span class="shop-filters__active-chip"><i class="fas fa-layer-group"></i> {{ $catLabel }}</span>
                        @endif
                        @if($selectedBrand)
                            @php $brandLabel = $brands->firstWhere('slug', $selectedBrand)?->name ?? $selectedBrand; @endphp
                            <span class="shop-filters__active-chip"><i class="fas fa-tag"></i> {{ $brandLabel }}</span>
                        @endif
                        @if($currentMax < $maxPrice)
                            <span class="shop-filters__active-chip en-text"><i class="fas fa-dollar-sign"></i> حتى {{ min($currentMax, $maxPrice) }}$</span>
                        @endif
                    </div>
                </div>
            @endif

            <div class="shop-filters__section">
                <div class="shop-filters__section-head">
                    <span class="shop-filters__section-icon" aria-hidden="true"><i class="fas fa-search"></i></span>
                    <h3 class="shop-filters__section-title">بحث سريع</h3>
                </div>
                <div class="shop-filters__search">
                    <i class="fas fa-search shop-filters__search-icon" aria-hidden="true"></i>
                    <input type="search"
                           id="search-input"
                           name="search"
                           value="{{ $searchQuery }}"
                           class="shop-filters__input"
                           placeholder="برنامج، مفتاح، اشتراك..."
                           autocomplete="off">
                </div>
            </div>

            <div class="shop-filters__section">
                <div class="shop-filters__section-head">
                    <span class="shop-filters__section-icon" aria-hidden="true"><i class="fas fa-layer-group"></i></span>
                    <h3 class="shop-filters__section-title">التصنيف</h3>
                </div>
                <ul class="shop-filters__chips" role="list">
                    <li>
                        <label class="shop-filters__chip" for="cat-all">
                            <input class="shop-filters__radio"
                                   type="radio"
                                   name="category"
                                   value=""
                                   id="cat-all"
                                   @checked($selectedCategory === '')>
                            <span class="shop-filters__chip-text">الكل</span>
                        </label>
                    </li>
                    @foreach($categories as $category)
                    <li>
                        <label class="shop-filters__chip" for="cat-{{ $category->slug }}">
                            <input class="shop-filters__radio"
                                   type="radio"
                                   name="category"
                                   value="{{ $category->slug }}"
                                   id="cat-{{ $category->slug }}"
                                   @checked($selectedCategory === $category->slug)>
                            <span class="shop-filters__chip-text">{{ $category->name }}</span>
                        </label>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="shop-filters__section">
                <div class="shop-filters__section-head">
                    <span class="shop-filters__section-icon" aria-hidden="true"><i class="fas fa-tags"></i></span>
                    <h3 class="shop-filters__section-title">نطاق السعر</h3>
                </div>
                <div class="shop-filters__price-panel">
                    <div class="shop-filters__price-badges en-text">
                        <span class="shop-filters__price-badge">0 $</span>
                        <span class="shop-filters__price-badge shop-filters__price-badge--max">
                            حتى <strong id="price-val">{{ min($currentMax, $maxPrice) }}</strong> $
                        </span>
                    </div>
                    <div class="shop-filters__range-wrap">
                        <div class="shop-filters__range-track" aria-hidden="true">
                            <div class="shop-filters__range-fill" id="price-range-fill"></div>
                        </div>
                        <input type="range"
                               class="shop-filters__range"
                               name="max_price"
                               min="0"
                               max="{{ $maxPrice }}"
                               value="{{ min($currentMax, $maxPrice) }}"
                               id="price-range"
                               data-max="{{ $maxPrice }}">
                    </div>
                </div>
                <input type="hidden" name="min_price" value="{{ $currentMin }}">
            </div>

            @if($brands->isNotEmpty())
            <div class="shop-filters__section">
                <div class="shop-filters__section-head">
                    <span class="shop-filters__section-icon" aria-hidden="true"><i class="fas fa-certificate"></i></span>
                    <h3 class="shop-filters__section-title">العلامة التجارية</h3>
                </div>
                <ul class="shop-filters__chips shop-filters__chips--brands" role="list">
                    <li>
                        <label class="shop-filters__chip" for="brand-all">
                            <input class="shop-filters__radio"
                                   type="radio"
                                   name="brand"
                                   value=""
                                   id="brand-all"
                                   @checked(! $selectedBrand)>
                            <span class="shop-filters__chip-text">الكل</span>
                        </label>
                    </li>
                    @foreach($brands as $brand)
                    <li>
                        <label class="shop-filters__chip" for="brand-{{ $brand->slug }}">
                            <input class="shop-filters__radio"
                                   type="radio"
                                   name="brand"
                                   value="{{ $brand->slug }}"
                                   id="brand-{{ $brand->slug }}"
                                   @checked($selectedBrand === $brand->slug)>
                            <span class="shop-filters__chip-text">{{ $brand->name }}</span>
                        </label>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <button type="submit" class="btn btn-accent shop-filters__submit w-100">
                <i class="fas fa-filter ms-2" aria-hidden="true"></i>
                تطبيق التصفية
            </button>
        </div>
    </form>
</aside>
