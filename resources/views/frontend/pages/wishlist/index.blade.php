@extends('frontend.layouts.master')

@section('content')
    @include('frontend.partials.wishlist-hero')

    <main class="container wishlist-page py-4 py-md-5 section-fade-up">
        @if($wishlistItems->isNotEmpty())
            <div class="wishlist-page__toolbar">
                <p class="wishlist-page__count mb-0">
                    <i class="fas fa-heart text-accent me-2" aria-hidden="true"></i>
                    <span class="en-text">{{ $wishlistItems->count() }}</span>
                    {{ $wishlistItems->count() === 1 ? 'منتج في المفضلة' : 'منتجات في المفضلة' }}
                </p>
                <a href="{{ route('frontend.shop.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                    متابعة التسوق
                </a>
            </div>

            <div class="row g-3 g-md-4 wishlist-page__grid">
                @foreach($wishlistItems as $product)
                    @include('frontend.partials.product-card', [
                        'product' => $product,
                        'columnClass' => 'col-sm-6 col-lg-4 col-xl-3',
                    ])
                @endforeach
            </div>
        @else
            <div class="wishlist-page__empty shop-products-empty">
                <i class="far fa-heart" aria-hidden="true"></i>
                <h5>قائمة المفضلة فارغة</h5>
                <p class="mb-4">لم تضف أي منتجات بعد. تصفّح المتجر واضغط على أيقونة القلب لحفظ المنتجات هنا.</p>
                <a href="{{ route('frontend.shop.index') }}" class="btn btn-accent px-4">تصفح المنتجات</a>
            </div>
        @endif
    </main>
@endsection
