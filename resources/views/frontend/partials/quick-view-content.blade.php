    <div class="row gutter-lg">
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="product-gallery product-gallery-sticky">
                <div class="swiper-container product-single-swiper swiper-theme nav-inner">
                    <div class="swiper-wrapper row cols-1 gutter-no">
                        @forelse($product->images as $image)
                        <div class="swiper-slide">
                            <figure class="product-image">
                                @php $imgUrl = product_image_url($image->path, $product->id); @endphp
                                <img src="{{ $imgUrl }}" data-zoom-image="{{ $imgUrl }}" alt="{{ $product->name }}" width="800" height="900">
                            </figure>
                        </div>
                        @empty
                        <div class="swiper-slide">
                            <figure class="product-image">
                                <img src="{{ $product->card_image_url }}" data-zoom-image="{{ $product->primary_image_url }}" alt="{{ $product->name }}" width="800" height="900">
                            </figure>
                        </div>
                        @endforelse
                    </div>
                    <button class="swiper-button-next"></button>
                    <button class="swiper-button-prev"></button>
                </div>
                @if($product->images->count() > 1)
                <div class="product-thumbs-wrap swiper-container" data-swiper-options="{
                    'navigation': {
                        'nextEl': '.swiper-button-next',
                        'prevEl': '.swiper-button-prev'
                    }
                }">
                    <div class="product-thumbs swiper-wrapper row cols-4 gutter-sm">
                        @foreach($product->images as $image)
                        <div class="product-thumb swiper-slide">
                            <img src="{{ product_image_url($image->path, $product->id) }}" alt="{{ $product->name }}" width="103" height="116">
                        </div>
                        @endforeach
                    </div>
                    <button class="swiper-button-next"></button>
                    <button class="swiper-button-prev"></button>
                </div>
                @endif
            </div>
        </div>
        <div class="col-md-6 overflow-hidden p-relative">
            <div class="product-details scrollable pl-0">
                <h2 class="product-title">{{ $product->name }}</h2>
                <div class="product-bm-wrapper">
                    @if($product->brand)
                    <figure class="brand">
                        <img src="{{ $product->brand->image ? asset('storage/' . $product->brand->image) : asset('frontend/assets/images/products/brand/brand-1.jpg') }}" alt="{{ $product->brand->name }}" width="102" height="48" />
                    </figure>
                    @endif
                    <div class="product-meta">
                        @if($product->category)
                        <div class="product-categories">
                            Category:
                            <span class="product-category d-inline-block"><a href="{{ route('frontend.category.show', $product->category->slug) }}">{{ $product->category->name }}</a></span>
                        </div>
                        @endif
                        @if($product->sku)
                        <div class="product-sku">
                            SKU: <span class="d-inline-block">{{ $product->sku }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <hr class="product-divider">

                <div class="product-price">
                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <ins class="new-price">{{ format_money($product->price) }}</ins>
                        <del class="old-price">{{ format_money($product->compare_at_price) }}</del>
                    @else
                        <ins class="new-price">{{ format_money($product->price) }}</ins>
                    @endif
                </div>

                <div class="ratings-container">
                    <div class="ratings-full">
                        <span class="ratings" style="width: {{ $product->reviews_avg_rating ? ($product->reviews_avg_rating / 5 * 100) : 0 }}%;"></span>
                        <span class="tooltiptext tooltip-top"></span>
                    </div>
                    <a href="{{ route('frontend.product.show', $product->slug) }}" class="rating-reviews">({{ $product->reviews->count() }} مراجعة)</a>
                </div>

                @if($product->short_description)
                <div class="product-short-desc">
                    {!! $product->short_description !!}
                </div>
                @endif

                <hr class="product-divider">

                <div class="product-form">
                    <div class="product-qty-form">
                        <div class="input-group">
                            <input class="quantity form-control" type="number" min="1" max="99" value="1">
                            <button class="quantity-plus w-icon-plus"></button>
                            <button class="quantity-minus w-icon-minus"></button>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-cart" data-product-id="{{ $product->id }}">
                        <i class="w-icon-cart"></i>
                        <span>أضف للسلة</span>
                    </button>
                </div>

                <div class="social-links-wrapper">
                    <div class="social-links">
                        <div class="social-icons social-no-color border-thin">
                            <a href="#" class="social-icon social-facebook w-icon-facebook"></a>
                            <a href="#" class="social-icon social-twitter w-icon-twitter"></a>
                            <a href="#" class="social-icon social-pinterest fab fa-pinterest-p"></a>
                            <a href="#" class="social-icon social-whatsapp fab fa-whatsapp"></a>
                        </div>
                    </div>
                    <span class="divider d-xs-show"></span>
                    <div class="product-link-wrapper d-flex">
                        <a href="#" class="btn-product-icon btn-wishlist w-icon-heart"><span></span></a>
                        <a href="#" class="btn-product-icon btn-compare btn-icon-left w-icon-compare"><span></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
