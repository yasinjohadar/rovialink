@extends('frontend.layouts.master')

@section('title', ($product->meta_title ?: $product->name).' - متجر إديو ستور')

@section('content')
    @include('frontend.partials.product-detail')
    @include('frontend.partials.product-tabs')
    @include('frontend.partials.related-products')
</main>
@endsection

@push('scripts')
<script>
    function changeQty(delta) {
        const input = document.getElementById('qty-input');
        const cartQty = document.getElementById('cart-qty-input');
        if (!input) return;
        let val = parseInt(input.value, 10) + delta;
        const max = parseInt(input.max, 10) || 10;
        if (val < 1) val = 1;
        if (val > max) val = max;
        input.value = val;
        if (cartQty) cartQty.value = val;
    }

    document.getElementById('qty-input')?.addEventListener('change', function () {
        const cartQty = document.getElementById('cart-qty-input');
        if (cartQty) cartQty.value = this.value;
    });

    document.querySelectorAll('.color-option').forEach(opt => {
        opt.addEventListener('click', () => {
            document.querySelectorAll('.color-option').forEach(o => o.classList.remove('active'));
            opt.classList.add('active');
        });
    });

    document.getElementById('wishlist-btn')?.addEventListener('click', function () {
        const icon = this.querySelector('i');
        icon.classList.toggle('far');
        icon.classList.toggle('fas');
        if (icon.classList.contains('fas')) {
            icon.style.color = '#ef4444';
            showToast('تمت إضافة المنتج إلى قائمة الرغبات', 'success');
        } else {
            icon.style.color = '';
            showToast('تمت إزالة المنتج من قائمة الرغبات', 'info');
        }
    });

    const thumbsEl = document.querySelector('.product-thumbs-swiper');
    let thumbsSwiper = null;
    if (thumbsEl) {
        thumbsSwiper = new Swiper('.product-thumbs-swiper', {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
        });
    }

    new Swiper('.product-image-swiper', {
        spaceBetween: 10,
        loop: {{ $product->images->count() > 1 ? 'true' : 'false' }},
        navigation: {
            nextEl: '.product-img-next',
            prevEl: '.product-img-prev',
        },
        thumbs: thumbsSwiper ? { swiper: thumbsSwiper } : undefined,
    });
</script>
@endpush
