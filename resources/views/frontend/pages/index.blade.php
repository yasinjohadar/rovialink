@extends('frontend.layouts.master')

@section('content')
    @include('frontend.partials.hero')
    @include('frontend.partials.categories')
    @include('frontend.partials.products-swiper')
    @include('frontend.partials.best-sellers')
    @include('frontend.partials.features')
    @include('frontend.partials.brands')
    @include('frontend.partials.testimonials')
    @include('frontend.partials.newsletter')
    @include('frontend.partials.blog-swiper')
@endsection

@push('scripts')
<script>
    const productsSwiper = new Swiper('.products-swiper', {
        slidesPerView: 1.2,
        spaceBetween: 20,
        loop: true,
        autoplay: { delay: 4000, disableOnInteraction: false },
        pagination: { el: '.products-pagination', clickable: true },
        navigation: { nextEl: '.products-next', prevEl: '.products-prev' },
        breakpoints: {
            576:  { slidesPerView: 2 },
            992:  { slidesPerView: 3 },
            1200: { slidesPerView: 4 }
        }
    });

    const blogSwiper = new Swiper('.blog-swiper', {
        slidesPerView: 1,
        spaceBetween: 24,
        loop: true,
        autoplay: { delay: 5000, disableOnInteraction: false },
        pagination: { el: '.blog-pagination', clickable: true },
        navigation: { nextEl: '.blog-next', prevEl: '.blog-prev' },
        breakpoints: {
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 }
        }
    });
</script>
@endpush
