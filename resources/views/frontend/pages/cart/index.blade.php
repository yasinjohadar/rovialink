@extends('frontend.layouts.master')

@section('content')
    @include('frontend.partials.cart-hero')

    <main class="container py-4">
        <div id="toast-container"></div>

        <div class="row g-5 section-fade-up">
            <div class="col-lg-8">
                @include('frontend.partials.cart-items')
            </div>
            <div class="col-lg-4">
                @include('frontend.partials.cart-summary')
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    window.CART_ROUTES = {
        clear: @json(route('frontend.cart.clear')),
        applyCoupon: @json(route('frontend.cart.apply-coupon')),
        removeCoupon: @json(route('frontend.cart.remove-coupon')),
        update: @json(route('frontend.cart.update', ['id' => '__ID__'])),
        destroy: @json(route('frontend.cart.destroy', ['id' => '__ID__'])),
    };
</script>
<script src="{{ asset('frontend/assets/js/cart-page.js') }}"></script>
@endpush
