@extends('frontend.layouts.master')

@section('title', 'قائمة الرغبات - متجرنا')

@section('content')
<nav class="breadcrumb-nav">
    <div class="container">
        <ul class="breadcrumb bb-no">
            <li><a href="{{ route('frontend.home') }}">الرئيسية</a></li>
            <li>قائمة الرغبات</li>
        </ul>
    </div>
</nav>

<div class="page-content">
    <div class="container">
        <h2 class="title text-center mb-8">قائمة الرغبات</h2>

        @if($wishlistItems && count($wishlistItems) > 0)
        <div class="row cols-xl-5 cols-lg-4 cols-md-3 cols-sm-2 cols-1">
            @foreach($wishlistItems as $item)
            <div class="product-wrap">
                <div class="product text-center">
                    <figure class="product-media">
                        <a href="{{ route('frontend.product.show', $item['slug']) }}">
                            <img src="{{ $item['image'] ? product_image_url($item['image'], $item['id'] ?? null) : product_image_url(null, $item['id'] ?? null) }}" alt="{{ $item['name'] }}" width="300" height="338" />
                        </a>
                        <div class="product-action-vertical">
                            <a href="#" class="btn-product-icon btn-cart w-icon-cart" title="أضف للسلة"></a>
                            <a href="#" class="btn-product-icon btn-wishlist w-icon-heart active" title="إزالة من قائمة الرغبات"></a>
                            <a href="#" class="btn-product-icon btn-compare w-icon-compare" title="مقارنة"></a>
                        </div>
                    </figure>
                    <div class="product-details">
                        <h3 class="product-name">
                            <a href="{{ route('frontend.product.show', $item['slug']) }}">{{ $item['name'] }}</a>
                        </h3>
                        <div class="product-price">
                            @if($item['sale_price'])
                                <ins class="new-price">{{ $item['sale_price'] }} ر.س</ins>
                                <del class="old-price">{{ $item['price'] }} ر.س</del>
                            @else
                                <span class="price">{{ $item['price'] }} ر.س</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-10">
            <i class="w-icon-heart" style="font-size: 4rem; color: #ccc;"></i>
            <h3 class="mt-4">قائمة الرغبات فارغة</h3>
            <p class="mb-4">لم تقم بإضافة أي منتجات إلى قائمة الرغبات بعد</p>
            <a href="{{ route('frontend.shop.index') }}" class="btn btn-primary btn-rounded">تسوق الآن</a>
        </div>
        @endif
    </div>
</div>
@endsection
