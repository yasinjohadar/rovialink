@php
    $columnClass = $columnClass ?? 'col-md-6 col-lg-4 col-xl-3';
@endphp
<div class="{{ $columnClass }}">
    <div class="glass-card h-100 d-flex flex-column product-card">
        <div class="product-img text-white text-center">
            @if($product->is_featured ?? false)
                <span class="product-badge danger">الأكثر مبيعاً</span>
            @endif
            @unless($product->in_stock)
                <span class="product-stock-badge out-of-stock">غير متاح</span>
            @endunless
            <a href="{{ route('frontend.product.show', $product->slug) }}" class="d-block w-100 h-100">
                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
            </a>
            @if($product->relationLoaded('images') && $product->images->count() > 1)
                <div class="image-count-badge"><i class="fas fa-images"></i> {{ $product->images->count() }}</div>
            @endif
            <div class="product-actions-overlay">
                <button type="button" class="product-action-btn" onclick="toggleWishlist({{ $product->id }})" title="أضف للمفضلة">
                    <i class="far fa-heart"></i>
                </button>
                @include('frontend.partials.add-to-cart-form', ['product' => $product])
                <button type="button" class="product-action-btn" onclick="openQuickView('{{ $product->slug }}')" title="عرض سريع">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
        <div class="p-3 d-flex flex-column flex-grow-1">
            <div class="d-flex justify-content-between align-items-start mb-2">
                @if($product->category)
                    <span class="badge bg-white bg-opacity-10 text-accent px-2 py-1">{{ $product->category->name }}</span>
                @endif
                @if($product->brand)
                    <span class="text-secondary small en-text">{{ $product->brand->name }}</span>
                @endif
            </div>
            <h5 class="fw-bold mb-2 d-flex flex-grow-1">
                <a href="{{ route('frontend.product.show', $product->slug) }}" class="text-white text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem;">
                    {{ $product->name }}
                </a>
            </h5>
            <div class="product-rating mb-2">
                <span class="stars en-text small">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($product->reviews_avg_rating ?? 0))
                            <i class="fas fa-star"></i>
                        @elseif($i - 0.5 <= ($product->reviews_avg_rating ?? 0))
                            <i class="fas fa-star-half-alt"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                </span>
                <span class="count en-text">({{ $product->relationLoaded('reviews') ? $product->reviews->count() : 0 }})</span>
            </div>
            <hr class="border-secondary mt-auto mb-2">
            <div class="product-card-footer d-flex justify-content-between align-items-center gap-2">
                <div class="product-price">
                    <span class="current-price">{{ $product->price }} ر.س</span>
                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <span class="original-price ms-2">{{ $product->compare_at_price }} ر.س</span>
                    @endif
                </div>
                @include('frontend.partials.add-to-cart-form', [
                    'product' => $product,
                    'buttonClass' => 'btn btn-sm btn-accent rounded-circle',
                    'icon' => '<i class="fas fa-cart-plus"></i>',
                ])
            </div>
        </div>
    </div>
</div>
