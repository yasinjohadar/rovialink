<form method="POST" action="{{ route('frontend.cart.store') }}" class="{{ $class ?? 'd-inline' }}">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="quantity" value="{{ $quantity ?? 1 }}">
    <button type="submit" class="{{ $buttonClass ?? 'product-action-btn' }}" style="{{ isset($buttonClass) && str_contains($buttonClass ?? '', 'rounded-circle') ? 'width:35px; height:35px; padding:0' : '' }}" title="أضف للسلة">
        {!! $icon ?? '<i class="fas fa-cart-plus"></i>' !!}
    </button>
</form>
