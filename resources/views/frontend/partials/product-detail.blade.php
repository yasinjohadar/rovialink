@php
    $reviewCount = $product->reviews->count();
    $avgRating = $product->reviews_avg_rating ?? 0;
@endphp
<div class="product-page">
    <div class="product-page__breadcrumb-wrap">
        <div class="container">
            <nav class="product-page__breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('frontend.shop.index') }}">المنتجات</a></li>
                    @if($product->category)
                    <li class="breadcrumb-item"><a href="{{ route('frontend.category.show', $product->category->slug) }}">{{ $product->category->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($product->name, 48) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <main class="container product-page__main py-3 py-md-4">
        <div class="row g-3 g-lg-4 align-items-start">
            <div class="col-lg-6">
                <div class="product-page__gallery section-fade-up">
                    <div class="product-page__media">
                        <div class="swiper product-image-swiper">
                            <div class="swiper-wrapper" id="product-swiper-wrapper">
                                @forelse($product->images as $image)
                                <div class="swiper-slide">
                                    <div class="product-page__image-frame">
                                        <img src="{{ product_image_url($image->path, $product->id) }}"
                                             alt="{{ $product->name }}"
                                             class="product-page__image"
                                             loading="lazy">
                                    </div>
                                </div>
                                @empty
                                <div class="swiper-slide">
                                    <div class="product-page__image-frame">
                                        <img src="{{ $product->primary_image_url }}"
                                             alt="{{ $product->name }}"
                                             class="product-page__image"
                                             loading="eager">
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            @if($product->images->count() > 1)
                            <div class="swiper-button-next product-img-next"></div>
                            <div class="swiper-button-prev product-img-prev"></div>
                            @endif
                        </div>
                    </div>

                    @if($product->images->count() > 1)
                    <div class="product-page__thumbs">
                        <div class="swiper product-thumbs-swiper">
                            <div class="swiper-wrapper" id="product-thumbs-wrapper">
                                @foreach($product->images as $image)
                                <div class="swiper-slide">
                                    <div class="product-page__thumb-frame">
                                        <img src="{{ product_image_url($image->path, $product->id) }}"
                                             alt="{{ $product->name }}"
                                             class="product-page__thumb-image"
                                             loading="lazy">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-6">
                <div class="product-page__info section-fade-up">
                    <div class="product-page__badges">
                        @if($product->category)
                        <span class="product-page__badge product-page__badge--category">{{ $product->category->name }}</span>
                        @endif
                        @if($product->is_bestseller)
                        <span class="product-page__badge product-page__badge--hot">الأكثر مبيعاً</span>
                        @elseif($product->is_new)
                        <span class="product-page__badge product-page__badge--new">جديد</span>
                        @endif
                    </div>

                    <h1 class="product-page__title">{{ $product->name }}</h1>

                    <div class="product-page__meta">
                        @if($product->brand)
                        <span class="product-page__meta-item">
                            <i class="fas fa-tag" aria-hidden="true"></i>
                            <span class="en-text">{{ $product->brand->name }}</span>
                        </span>
                        @endif
                        <span class="product-page__meta-item">
                            <span class="stars en-text text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($avgRating))
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                    @elseif($i - 0.5 <= $avgRating)
                                        <i class="fas fa-star-half-alt" aria-hidden="true"></i>
                                    @else
                                        <i class="far fa-star" aria-hidden="true"></i>
                                    @endif
                                @endfor
                            </span>
                            <span class="en-text">({{ $reviewCount }} تقييم)</span>
                        </span>
                        <span class="product-page__meta-item product-page__meta-item--stock {{ $product->in_stock ? 'is-in-stock' : 'is-out-stock' }}">
                            <i class="fas {{ $product->in_stock ? 'fa-check-circle' : 'fa-times-circle' }}" aria-hidden="true"></i>
                            {{ $product->in_stock ? 'متاح للشراء' : 'غير متاح' }}
                        </span>
                    </div>

                    <div class="product-page__pricing">
                        <span class="product-page__price en-text">{{ format_money($product->price) }}</span>
                        @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <span class="product-page__price-old en-text">{{ format_money($product->compare_at_price) }}</span>
                        <span class="product-page__discount">خصم {{ $product->discount_percentage }}%</span>
                        @endif
                    </div>

                    @if($product->short_description || $product->description)
                    <p class="product-page__excerpt">{{ $product->short_description ?? Str::limit(strip_tags($product->description), 200) }}</p>
                    @endif

                    @if($product->colors)
                    <div class="product-page__field">
                        <h2 class="product-page__field-label">اللون:</h2>
                        <div class="color-options product-page__colors" id="color-options">
                            @foreach(explode(',', $product->colors) as $index => $color)
                            <div class="color-option {{ $index === 0 ? 'active' : '' }}"
                                 style="background: {{ trim($color) }};"
                                 data-color="{{ trim($color) }}"
                                 role="button"
                                 tabindex="0"
                                 aria-label="لون"></div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="product-page__field">
                        <h2 class="product-page__field-label">الكمية:</h2>
                        <div class="qty-stepper quantity-selector" role="group" aria-label="اختيار الكمية">
                            <button type="button" id="product-qty-minus" onclick="changeQty(-1)" aria-label="تقليل الكمية">
                                <i class="fas fa-minus" aria-hidden="true"></i>
                            </button>
                            <input type="number" id="qty-input" value="1" min="1" max="99" inputmode="numeric" aria-label="الكمية">
                            <button type="button" id="product-qty-plus" onclick="changeQty(1)" aria-label="زيادة الكمية">
                                <i class="fas fa-plus" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div class="product-page__actions">
                        <form method="POST"
                              action="{{ route('frontend.cart.store') }}"
                              class="js-add-to-cart-form product-page__cart-form"
                              id="product-add-to-cart-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" id="cart-qty-input" value="1">
                            <button type="submit"
                                    class="btn btn-accent product-page__add-cart"
                                    @disabled(! $product->in_stock)>
                                <i class="fas fa-cart-plus ms-2" aria-hidden="true"></i>
                                أضف إلى السلة
                            </button>
                        </form>
                        <div class="product-page__wishlist-wrap">
                            @include('frontend.partials.wishlist-button', ['product' => $product])
                        </div>
                    </div>

                    <div class="product-page__highlights">
                        <div class="product-page__highlight">
                            <i class="fas fa-download" aria-hidden="true"></i>
                            <span>تسليم رقمي</span>
                        </div>
                        <div class="product-page__highlight">
                            <i class="fas fa-shield-halved" aria-hidden="true"></i>
                            <span>ضمان سنتين</span>
                        </div>
                        <div class="product-page__highlight">
                            <i class="fas fa-undo" aria-hidden="true"></i>
                            <span>إرجاع 30 يوم</span>
                        </div>
                    </div>

                    @if($product->features)
                    <div class="product-page__features">
                        <h2 class="product-page__field-label">مميزات المنتج</h2>
                        <ul class="product-page__features-list">
                            @foreach(explode("\n", $product->features) as $feature)
                                @if(trim($feature) !== '')
                                <li>
                                    <i class="fas fa-check" aria-hidden="true"></i>
                                    {{ trim($feature) }}
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="product-page__footer-meta">
                        <span><i class="fas fa-share-alt" aria-hidden="true"></i> مشاركة المنتج</span>
                        @if($product->is_digital ?? true)
                        <span><i class="fas fa-bolt" aria-hidden="true"></i> منتج رقمي — تسليم فوري</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
