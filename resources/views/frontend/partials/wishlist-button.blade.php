@php
    static $cachedWishlistProductIds = null;
    if ($cachedWishlistProductIds === null) {
        $cachedWishlistProductIds = auth()->check()
            ? auth()->user()->wishlists()->pluck('product_id')->map(fn ($id) => (int) $id)->all()
            : [];
    }
    $wishlistPayload = [
        'id' => $product->id,
        'title' => $product->name,
        'slug' => $product->slug,
        'newPrice' => (float) $product->effective_price,
        'img' => $product->card_image_url,
    ];
    $isWishlisted = in_array((int) $product->id, $cachedWishlistProductIds, true);
@endphp
<button type="button"
        class="product-action-btn {{ $isWishlisted ? 'wishlisted' : '' }}"
        data-wishlist-id="{{ $product->id }}"
        data-wishlist-product="{{ htmlspecialchars(json_encode($wishlistPayload, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') }}"
        title="أضف للمفضلة">
    <i class="{{ $isWishlisted ? 'fas' : 'far' }} fa-heart"></i>
</button>
