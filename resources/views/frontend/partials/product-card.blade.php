@php
    $columnClass = $columnClass ?? 'col-md-6 col-lg-4 col-xl-3';
    $inSwiper = !empty($inSwiper);
    $productUrl = route('frontend.product.show', $product->slug);
    $reviewCount = $product->relationLoaded('reviews') ? $product->reviews->count() : ($product->reviews_count ?? 0);
    $avgRating = $product->reviews_avg_rating ?? 0;
@endphp
@if(!$inSwiper)
<div class="{{ $columnClass }}">
@endif
    <article class="product-card h-100">
        <div class="product-card__media">
            @if(!empty($product->is_bestseller) || ($product->is_featured ?? false))
                <span class="product-card__badge product-card__badge--hot">الأكثر مبيعاً</span>
            @elseif(!empty($product->is_new))
                <span class="product-card__badge product-card__badge--new">جديد</span>
            @endif
            @unless($product->in_stock)
                <span class="product-card__stock product-card__stock--out">غير متاح</span>
            @endunless
            <a href="{{ $productUrl }}" class="product-card__media-link" tabindex="-1" aria-hidden="true">
                <img src="{{ $product->primary_image_url }}"
                     alt="{{ $product->name }}"
                     class="product-card__image"
                     loading="lazy">
            </a>
            @if($product->relationLoaded('images') && $product->images->count() > 1)
                <span class="product-card__gallery-count">
                    <i class="fas fa-images" aria-hidden="true"></i>
                    {{ $product->images->count() }}
                </span>
            @endif
            <div class="product-card__actions">
                @include('frontend.partials.wishlist-button', ['product' => $product])
                @include('frontend.partials.add-to-cart-form', ['product' => $product])
                <button type="button"
                        class="product-action-btn"
                        onclick="openQuickView('{{ $product->slug }}')"
                        title="عرض سريع">
                    <i class="fas fa-eye" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <div class="product-card__body">
            <div class="product-card__meta">
                @if($product->category)
                    <span class="product-card__category">{{ $product->category->name }}</span>
                @endif
                @if($product->brand)
                    <span class="product-card__brand en-text">{{ $product->brand->name }}</span>
                @endif
            </div>
            <h3 class="product-card__title">
                <a href="{{ $productUrl }}">{{ $product->name }}</a>
            </h3>
            <div class="product-card__rating product-rating">
                <span class="stars en-text">
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
                <span class="count en-text">({{ $reviewCount }})</span>
            </div>
            <footer class="product-card__footer product-card-footer">
                <div class="product-price">
                    <span class="current-price en-text">{{ format_money($product->price) }}</span>
                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <span class="original-price en-text">{{ format_money($product->compare_at_price) }}</span>
                    @endif
                </div>
                @include('frontend.partials.add-to-cart-form', [
                    'product' => $product,
                    'buttonClass' => 'product-card__cart-btn btn-add-cart',
                    'icon' => '<i class="fas fa-cart-plus" aria-hidden="true"></i>',
                ])
            </footer>
        </div>
    </article>
@if(!$inSwiper)
</div>
@endif
