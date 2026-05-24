<div class="glass-panel p-3 mb-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-3">
        <div>
            <h6 class="fw-bold mb-1">طلب <span class="en-text">#{{ $order->order_number }}</span></h6>
            <p class="text-secondary small mb-0"><i class="fas fa-calendar-alt me-1"></i> {{ $order->created_at->translatedFormat('d F Y') }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            @include('frontend.pages.account.partials.order-status-badge', ['order' => $order])
            <span class="en-text text-accent fw-bold fs-5">{{ format_money($order->total) }}</span>
        </div>
    </div>
    @if($order->items->isNotEmpty())
    <div class="d-flex gap-3 mb-3 flex-wrap">
        @foreach($order->items->take(3) as $item)
        <div class="product-img text-white text-center flex-shrink-0 rounded-3 overflow-hidden" style="width:70px;height:70px;">
            <img src="{{ product_image_url($item->product?->primary_image?->path ?? null, $item->product_id) }}"
                 alt="{{ $item->product_name }}"
                 class="w-100 h-100 object-fit-cover">
        </div>
        @endforeach
        @if($order->items->count() > 3)
        <div class="d-flex align-items-center">
            <span class="text-secondary small">+{{ $order->items->count() - 3 }} منتج آخر</span>
        </div>
        @endif
    </div>
    @endif
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('frontend.account.orders.show', $order) }}" class="btn btn-sm btn-accent rounded-pill px-3">عرض التفاصيل</a>
        <form method="POST" action="{{ route('frontend.account.orders.reorder', $order) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3"><i class="fas fa-redo me-1"></i> إعادة طلب</button>
        </form>
    </div>
</div>
