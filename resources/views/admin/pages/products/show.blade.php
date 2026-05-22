@extends('admin.layouts.master')

@section('page-title')
    عرض المنتج: {{ $product->name }}
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">عرض المنتج</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">تعديل</a>
                    <form action="{{ route('admin.products.compare.add', $product) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-success">
                            إضافة للمقارنة
                        </button>
                    </form>
                    @php
                        $compareIds = collect(session('admin_product_compare', []))->map(fn ($id) => (int) $id);
                    @endphp
                    @if($compareIds->contains($product->id))
                        <form action="{{ route('admin.products.compare.remove', $product) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                إزالة من المقارنة
                            </button>
                        </form>
                    @endif
                    @if($compareIds->count() >= 2)
                        <a href="{{ route('admin.products.compare') }}" class="btn btn-info">
                            عرض المقارنة ({{ $compareIds->count() }})
                        </a>
                    @endif
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">العودة</a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show">{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-height: 300px; object-fit: contain;">
                            @if($product->images->count() > 1)
                                <div class="d-flex gap-2 justify-content-center mt-2 flex-wrap">
                                    @foreach($product->images as $img)
                                        <img src="{{ $img->url }}" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">{{ $product->name }}</h6>
                            <span class="badge bg-{{ $product->status == 'active' ? 'success' : ($product->status == 'draft' ? 'secondary' : 'danger') }}">{{ $product->status }}</span>
                            @if($product->is_featured)<span class="badge bg-warning text-dark">مميز</span>@endif
                        </div>
                        <div class="card-body">
                            <p><strong>SKU:</strong> {{ $product->sku ?? '-' }}</p>
                            <p><strong>التصنيف:</strong> {{ $product->category->name ?? '-' }}</p>
                            <p><strong>السعر:</strong> {{ number_format($product->effective_price, 2) }} ر.س</p>
                            @if($product->compare_at_price)
                                <p><strong>سعر المقارنة:</strong> <del>{{ number_format($product->compare_at_price, 2) }} ر.س</del></p>
                            @endif
                            <p><strong>التوفر:</strong> {{ $product->in_stock ? 'متاح للشراء' : 'غير متاح' }}</p>
                            @if($product->is_digital)
                                <p><strong>النوع:</strong> <span class="badge bg-info">منتج رقمي</span></p>
                            @endif
                            @if($product->short_description)
                                <p><strong>وصف مختصر:</strong><br>{{ $product->short_description }}</p>
                            @endif
                            @if($product->description)
                                <div class="product-description"><strong>الوصف:</strong><br>{!! $product->description !!}</div>
                            @endif
                        </div>
                    </div>
                    @if($product->variants->count() > 0)
                        <div class="card mt-3">
                            <div class="card-header"><h6 class="mb-0">المتغيرات</h6></div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <thead><tr><th>SKU</th><th>السعر</th><th>الخصائص</th></tr></thead>
                                    <tbody>
                                        @foreach($product->variants as $v)
                                            <tr>
                                                <td>{{ $v->sku ?? '-' }}</td>
                                                <td>{{ $v->effective_price ? number_format($v->effective_price, 2) . ' ر.س' : '-' }}</td>
                                                <td>{{ $v->attributeValues->pluck('value')->join(' / ') ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="card mt-3">
                        <div class="card-header"><h6 class="mb-0">المراجعات ({{ $product->reviews->count() }})</h6></div>
                        <div class="card-body">
                            <a href="{{ route('admin.reviews.index', ['product_id' => $product->id]) }}" class="btn btn-sm btn-outline-primary">عرض مراجعات هذا المنتج</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
