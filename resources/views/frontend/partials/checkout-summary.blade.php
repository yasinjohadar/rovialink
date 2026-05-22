@php
    $discount = $discount ?? session('discount', 0);
    $shippingCost = $shippingCost ?? 0;
    $total = max(0, $cartTotal + $shippingCost - $discount);
@endphp
<div class="glass-card position-sticky section-fade-up" style="top: 100px; z-index: 10;">
    <div class="p-4">
        <h5 class="fw-bold text-white mb-4"><i class="fas fa-receipt text-accent ms-2"></i> ملخص الطلب</h5>

        <div id="checkout-order-items" class="d-flex flex-column gap-3 mb-4" data-server-rendered="1">
            @foreach($cartItems as $item)
            <div class="d-flex justify-content-between align-items-center text-secondary small border-bottom border-secondary border-opacity-25 pb-2">
                <span class="text-white">{{ Str::limit($item['name'], 40) }} <span class="en-text text-secondary">x{{ $item['quantity'] }}</span></span>
                <span class="en-text text-accent fw-bold">{{ number_format($item['subtotal'], 2) }} ر.س</span>
            </div>
            @endforeach
        </div>

        <hr class="border-secondary border-opacity-25">

        <div class="d-flex justify-content-between text-secondary mb-2">
            <span>المجموع:</span>
            <span class="en-text">{{ number_format($cartTotal, 2) }} ر.س</span>
        </div>
        @if($shippingCost > 0)
        <div class="d-flex justify-content-between text-secondary mb-2">
            <span>الشحن:</span>
            <span class="en-text">{{ number_format($shippingCost, 2) }} ر.س</span>
        </div>
        @endif
        @if($discount > 0)
        <div class="d-flex justify-content-between text-success mb-2">
            <span>الخصم:</span>
            <span class="en-text">-{{ number_format($discount, 2) }} ر.س</span>
        </div>
        @endif
        <hr class="border-secondary border-opacity-25">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-white m-0">الإجمالي:</h5>
            <h3 class="fw-bold text-accent m-0 en-text">{{ number_format($total, 2) }} ر.س</h3>
        </div>
    </div>
</div>
