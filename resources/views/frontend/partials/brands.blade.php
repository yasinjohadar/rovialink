@php
    $homepageBrands = $homepageBrands ?? collect();
@endphp

@if($homepageBrands->isNotEmpty())
@push('styles')
<style>
    /* Full-bleed marquee — inlined so it applies even if style.css on the server is stale/cached */
    .brands-marquee-section {
        overflow: hidden;
    }

    .brands-marquee {
        position: relative;
        width: 100vw !important;
        max-width: 100vw !important;
        margin-inline: calc(50% - 50vw) !important;
        overflow: hidden;
        mask-image: none !important;
        -webkit-mask-image: none !important;
    }
</style>
@endpush
<section class="py-5 section-fade-up brands-marquee-section">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h6 class="text-accent fw-bold text-uppercase tracking-wide mb-2">علامات مميزة</h6>
            <h2 class="fw-bold m-0">أشهر العلامات التجارية</h2>
            <p class="text-secondary mt-2 max-w-lg mx-auto">نتعاون مع أفضل العلامات التجارية العالمية لنقدم لك منتجات عالية الجودة.</p>
        </div>
    </div>

    <div class="brands-marquee" dir="ltr">
        <div class="brands-marquee__track">
            @foreach([1, 2] as $loopPass)
                @foreach($homepageBrands as $brand)
                    <a
                        href="{{ route('frontend.shop.index', ['brand' => $brand->slug]) }}"
                        class="brands-marquee__item text-decoration-none"
                        @if($loopPass > 1) aria-hidden="true" tabindex="-1" @endif
                    >
                        <div class="brands-marquee__card">
                            <div class="brands-marquee__logo-wrap">
                                <img
                                    src="{{ $brand->image_url }}"
                                    alt="{{ $brand->name }}"
                                    class="brands-marquee__logo"
                                    loading="lazy"
                                >
                            </div>
                            <span class="brands-marquee__name">{{ $brand->name }}</span>
                        </div>
                    </a>
                @endforeach
            @endforeach
        </div>
    </div>
</section>
@endif
