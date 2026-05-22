@php
    $discount = $discount ?? session('discount', 0);
    $total = max(0, $cartTotal - $discount);
@endphp
<div id="cart-summary-root" class="glass-card position-sticky" style="top: 100px; z-index: 10;">
    <div class="p-4">
        <h4 class="fw-bold text-white mb-4">ملخص الطلب</h4>

        <div class="d-flex justify-content-between mb-3 text-secondary">
            <span>المجموع الفرعي:</span>
            <span class="en-text">{{ number_format($cartTotal, 2) }} ر.س</span>
        </div>
        @if($discount > 0)
        <div class="d-flex justify-content-between mb-3 text-success">
            <span>الخصم:</span>
            <span class="en-text">-{{ number_format($discount, 2) }} ر.س</span>
        </div>
        @endif

        <hr class="border-secondary border-opacity-25 my-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-white m-0">الإجمالي:</h5>
            <h3 class="fw-bold text-accent m-0 en-text">{{ number_format($total, 2) }} ر.س</h3>
        </div>

        <form method="POST" action="{{ route('frontend.cart.apply-coupon') }}" class="input-group mb-2" id="cart-coupon-form" data-cart-coupon-form>
            @csrf
            <input type="text" name="coupon" class="form-control bg-glass text-white border-secondary" placeholder="أدخل كود الخصم" value="{{ old('coupon', $couponCode ?? '') }}">
            <button class="btn btn-outline-light border-secondary hover-accent" type="submit">تطبيق</button>
        </form>
        <p id="cart-coupon-error" class="text-danger small mb-3 d-none"><i class="fas fa-exclamation-circle me-1"></i><span></span></p>
        @error('coupon')
        <p class="text-danger small mb-3 cart-coupon-error-server"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</p>
        @enderror
        <div id="cart-coupon-applied">
        @if(session('coupon_code') && !($errors->has('coupon')))
        <p class="text-success small mb-2"><i class="fas fa-check-circle me-1"></i> تم تطبيق كود الخصم: {{ session('coupon_code') }}</p>
        <button type="button" class="btn btn-sm btn-outline-danger border-secondary w-100 mb-4" data-cart-remove-coupon>إزالة الكوبون</button>
        @endif
        </div>

        @if(count($cartItems) > 0)
        <a href="{{ route('frontend.checkout.index') }}" class="btn btn-accent w-100 py-3 fw-bold fs-5 shadow rounded-3 mb-3" id="checkout-btn">
            إتمام الطلب <i class="fas fa-lock ms-2 small"></i>
        </a>
        @else
        <button type="button" class="btn btn-accent w-100 py-3 fw-bold fs-5 shadow rounded-3 mb-3 disabled" disabled>إتمام الطلب</button>
        @endif
        <p class="text-center text-secondary small m-0"><i class="fas fa-shield-alt me-1 text-accent"></i> الدفع آمن ومشفّر 100%</p>
    </div>
</div>
