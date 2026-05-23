@extends('frontend.layouts.master')

@section('title', ($product->meta_title ?: $product->name).' - متجر إديو ستور')

@section('content')
    @include('frontend.partials.product-detail')
    @include('frontend.partials.product-tabs')
    @include('frontend.partials.related-products')
    </main>
</div>
@endsection

@push('scripts')
<script>
    function syncProductQtyStepper() {
        const input = document.getElementById('qty-input');
        const minus = document.getElementById('product-qty-minus');
        const plus = document.getElementById('product-qty-plus');
        const cartQty = document.getElementById('cart-qty-input');
        if (!input) return;

        const min = parseInt(input.min, 10) || 1;
        const max = parseInt(input.max, 10) || 99;
        let val = parseInt(input.value, 10);
        if (Number.isNaN(val)) val = min;
        val = Math.min(max, Math.max(min, val));
        input.value = val;
        if (cartQty) cartQty.value = val;
        if (minus) minus.disabled = val <= min;
        if (plus) plus.disabled = val >= max;
    }

    function changeQty(delta) {
        const input = document.getElementById('qty-input');
        if (!input) return;
        input.value = parseInt(input.value, 10) + delta;
        syncProductQtyStepper();
    }

    window.changeQty = changeQty;

    document.getElementById('qty-input')?.addEventListener('change', syncProductQtyStepper);
    document.getElementById('qty-input')?.addEventListener('input', syncProductQtyStepper);
    syncProductQtyStepper();

    document.querySelectorAll('.product-page__colors .color-option').forEach(opt => {
        opt.addEventListener('click', () => {
            document.querySelectorAll('.product-page__colors .color-option').forEach(o => o.classList.remove('active'));
            opt.classList.add('active');
        });
    });

    const thumbsEl = document.querySelector('.product-thumbs-swiper');
    let thumbsSwiper = null;
    if (thumbsEl) {
        thumbsSwiper = new Swiper('.product-thumbs-swiper', {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
            breakpoints: {
                576: { slidesPerView: 5 },
            },
        });
    }

    new Swiper('.product-image-swiper', {
        spaceBetween: 0,
        loop: {{ $product->images->count() > 1 ? 'true' : 'false' }},
        navigation: {
            nextEl: '.product-img-next',
            prevEl: '.product-img-prev',
        },
        thumbs: thumbsSwiper ? { swiper: thumbsSwiper } : undefined,
    });
</script>
@endpush
