@php
    $headerCartCount = collect(session('cart', []))->sum('quantity');
    $headerWishlistCount = auth()->check() ? auth()->user()->wishlists()->count() : 0;
    $wishlistProductIds = auth()->check()
        ? auth()->user()->wishlists()->pluck('product_id')->map(fn ($id) => (int) $id)->all()
        : [];
@endphp
<script>
    window.FRONTEND_ROUTES = {
        cartStore: @json(route('frontend.cart.store')),
    };
    window.FRONTEND_CONFIG = {
        isAuthenticated: @json(auth()->check()),
        wishlistToggleUrl: @json(route('frontend.wishlist.toggle', ['product' => '__ID__'])),
        initialCartCount: @json($headerCartCount),
        initialWishlistCount: @json($headerWishlistCount),
        wishlistProductIds: @json($wishlistProductIds),
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
@php
    $mainJsPath = public_path('frontend/assets/js/main.js');
    $jsVersion = file_exists($mainJsPath) ? filemtime($mainJsPath) : time();
@endphp
<script src="{{ asset('frontend/assets/js/main.js') }}?v={{ $jsVersion }}"></script>
@stack('scripts')
