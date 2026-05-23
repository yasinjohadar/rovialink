@php
    $filterAction = $filterAction ?? route('frontend.shop.index');
    $maxPrice = $maxProductPrice ?? 2000;
    $currentMax = (int) request('max_price', $maxPrice);
    $currentMin = (int) request('min_price', 0);
    $selectedCategory = request('category', $activeCategorySlug ?? '');
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
                    <h2 class="shop-filters__heading">تصفية</h2>
                </div>
                <a href="{{ $filterAction }}" id="shop-filters-reset" class="shop-filters__reset">إعادة ضبط</a>
            </header>

            <div class="shop-filters__section">
                <label class="shop-filters__label" for="search-input">بحث</label>
                <div class="shop-filters__search">
                    <i class="fas fa-search shop-filters__search-icon" aria-hidden="true"></i>
                    <input type="search"
                           id="search-input"
                           name="search"
                           value="{{ request('search') }}"
                           class="shop-filters__input"
                           placeholder="ابحث عن منتج، مفتاح، أو برنامج..."
                           autocomplete="off">
                </div>
            </div>

            <div class="shop-filters__section">
                <h3 class="shop-filters__section-title">التصنيف</h3>
                <ul class="shop-filters__list">
                    <li>
                        <label class="shop-filters__option" for="cat-all">
                            <input class="shop-filters__radio"
                                   type="radio"
                                   name="category"
                                   value=""
                                   id="cat-all"
                                   @checked($selectedCategory === '')>
                            <span class="shop-filters__option-indicator" aria-hidden="true"></span>
                            <span class="shop-filters__option-text">جميع التصنيفات</span>
                        </label>
                    </li>
                    @foreach($categories as $category)
                    <li>
                        <label class="shop-filters__option" for="cat-{{ $category->slug }}">
                            <input class="shop-filters__radio"
                                   type="radio"
                                   name="category"
                                   value="{{ $category->slug }}"
                                   id="cat-{{ $category->slug }}"
                                   @checked($selectedCategory === $category->slug)>
                            <span class="shop-filters__option-indicator" aria-hidden="true"></span>
                            <span class="shop-filters__option-text">{{ $category->name }}</span>
                        </label>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="shop-filters__section">
                <h3 class="shop-filters__section-title">
                    نطاق السعر
                    <span class="shop-filters__price-value en-text">
                        حتى <strong id="price-val">{{ min($currentMax, $maxPrice) }}</strong> ر.س
                    </span>
                </h3>
                <div class="shop-filters__range-wrap">
                    <input type="range"
                           class="shop-filters__range"
                           name="max_price"
                           min="0"
                           max="{{ $maxPrice }}"
                           value="{{ min($currentMax, $maxPrice) }}"
                           id="price-range"
                           oninput="document.getElementById('price-val').textContent = this.value">
                </div>
                <input type="hidden" name="min_price" value="{{ $currentMin }}">
            </div>

            <div class="shop-filters__section">
                <h3 class="shop-filters__section-title">العلامة التجارية</h3>
                <ul class="shop-filters__list">
                    <li>
                        <label class="shop-filters__option" for="brand-all">
                            <input class="shop-filters__radio"
                                   type="radio"
                                   name="brand"
                                   value=""
                                   id="brand-all"
                                   @checked(!request('brand'))>
                            <span class="shop-filters__option-indicator" aria-hidden="true"></span>
                            <span class="shop-filters__option-text">الكل</span>
                        </label>
                    </li>
                    @foreach($brands as $brand)
                    <li>
                        <label class="shop-filters__option" for="brand-{{ $brand->slug }}">
                            <input class="shop-filters__radio"
                                   type="radio"
                                   name="brand"
                                   value="{{ $brand->slug }}"
                                   id="brand-{{ $brand->slug }}"
                                   @checked(request('brand') === $brand->slug)>
                            <span class="shop-filters__option-indicator" aria-hidden="true"></span>
                            <span class="shop-filters__option-text">{{ $brand->name }}</span>
                        </label>
                    </li>
                    @endforeach
                </ul>
            </div>

            <button type="submit" class="btn btn-accent shop-filters__submit w-100">
                <i class="fas fa-check ms-2"></i>
                تطبيق التصفية
            </button>
        </div>
    </form>
</aside>
