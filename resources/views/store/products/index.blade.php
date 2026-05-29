@extends('store.layouts.master')

@section('title', 'المنتجات')

@section('content')
    <h4>المنتجات</h4>
    <div class="row g-3">
        @forelse($products as $product)
            <div class="col-md-4 col-lg-3">
                <div class="card h-100">
                    <img src="{{ $product->card_image_url }}" class="card-img-top" alt="{{ $product->name }}" style="height: 180px; object-fit: cover;">
                    <div class="card-body">
                        <h6 class="card-title">{{ $product->name }}</h6>
                        <p class="mb-2">{{ format_money($product->effective_price) }}</p>
                        <a href="{{ route('store.products.show', $product) }}" class="btn btn-sm btn-primary">عرض</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><p class="text-muted">لا توجد منتجات.</p></div>
        @endforelse
    </div>
    @if($products->hasPages())
        <div class="mt-3">{{ $products->links() }}</div>
    @endif
@endsection
