<div id="cart-server-rendered">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-white m-0">
            العناصر في السلة (<span class="en-text text-accent" id="cart-items-count">{{ collect($cartItems)->sum('quantity') }}</span>)
        </h4>
        @if(count($cartItems) > 0)
        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" id="clear-cart-btn" data-cart-clear>
            إفراغ السلة
        </button>
        @endif
    </div>

    <div id="cart-items-list">
    @forelse($cartItems as $item)
    <div class="glass-card p-3 d-flex flex-column flex-md-row gap-3 align-items-center mb-3" data-cart-row="{{ $item['row_id'] }}">
        <div class="product-img text-white text-center flex-shrink-0 rounded-3 overflow-hidden" style="width:120px;height:90px;">
            <img src="{{ product_image_url($item['image'] ?? null, $item['product_id'] ?? null) }}"
                 alt="{{ $item['name'] }}"
                 class="w-100 h-100 object-fit-cover"
                 style="border-radius: 12px;">
        </div>
        <div class="flex-grow-1 text-center text-md-start">
            <h6 class="fw-bold text-white mb-1">
                <a href="{{ route('frontend.product.show', $item['slug']) }}" class="text-white text-decoration-none">{{ $item['name'] }}</a>
            </h6>
            <div class="mt-2">
                <div class="d-inline-flex align-items-center gap-2">
                    <label class="text-secondary small mb-0">الكمية:</label>
                    <div class="cart-item-qty d-inline-flex">
                        <button type="button" data-cart-qty data-row-id="{{ $item['row_id'] }}" data-quantity="{{ max(1, $item['quantity'] - 1) }}" aria-label="تقليل الكمية">-</button>
                        <span class="cart-qty-value">{{ $item['quantity'] }}</span>
                        <button type="button" data-cart-qty data-row-id="{{ $item['row_id'] }}" data-quantity="{{ $item['quantity'] + 1 }}" aria-label="زيادة الكمية">+</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center d-flex flex-column align-items-end justify-content-between py-1">
            <span class="text-accent fw-bold fs-5 en-text cart-line-subtotal">{{ format_money($item['subtotal']) }}</span>
            <button type="button" class="btn btn-sm text-danger bg-transparent border-0 p-0 text-decoration-underline small mt-2" data-cart-remove data-row-id="{{ $item['row_id'] }}">
                <i class="fas fa-trash-alt me-1"></i>إزالة
            </button>
        </div>
    </div>
    @empty
    <div class="text-center py-5" id="empty-cart-msg">
        <i class="fas fa-shopping-cart fa-4x text-secondary opacity-50 mb-4"></i>
        <h4 class="text-white mb-3">سلتك فارغة حالياً</h4>
        <p class="text-secondary mb-4">اكتشف منتجاتنا الرقمية وابدأ التسوق الآن.</p>
        <a href="{{ route('frontend.shop.index') }}" class="btn btn-accent px-4 py-2 rounded-pill">تصفح المنتجات</a>
    </div>
    @endforelse
    </div>
</div>
