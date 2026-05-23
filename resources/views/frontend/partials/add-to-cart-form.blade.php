<form method="POST"
      action="{{ route('frontend.cart.store') }}"
      class="js-add-to-cart-form {{ $class ?? 'd-inline' }}">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="quantity" value="{{ $quantity ?? 1 }}">
    <button type="submit" class="{{ $buttonClass ?? 'product-action-btn' }}" title="أضف للسلة">
        {!! $icon ?? '<i class="fas fa-cart-plus"></i>' !!}
    </button>
</form>
