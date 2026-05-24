@extends('admin.layouts.master')

@section('page-title')
    مقارنة المنتجات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">مقارنة المنتجات</h5>
                    <p class="mb-0 text-muted">مقارنة تفصيلية بين {{ $products->count() }} منتجات مختارة داخل لوحة التحكم.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">العودة لقائمة المنتجات</a>
                    <form action="{{ route('admin.products.compare.clear') }}" method="POST" class="d-inline" onsubmit="return confirm('مسح قائمة المقارنة بالكامل؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">مسح المقارنة</button>
                    </form>
                </div>
            </div>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                </div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                </div>
            @endif

            @php
                $formatPrice = function ($amount) {
                    return isset($currencyService) ? $currencyService->format((float) $amount) : format_money((float) $amount);
                };
                $diffClass = function (array $values) {
                    $unique = collect($values)->unique()->count();
                    return $unique > 1 ? 'table-warning' : '';
                };
            @endphp

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0 compare-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 220px;">الخاصية</th>
                                    @foreach($products as $product)
                                        <th class="text-center" style="min-width: 260px;">
                                            <div class="d-flex flex-column align-items-center gap-2 py-2">
                                                @if($product->primary_image_url)
                                                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px;">
                                                @endif
                                                <a href="{{ route('admin.products.edit', $product) }}" class="fw-semibold text-decoration-none">{{ $product->name }}</a>
                                                <div class="small text-muted">SKU: <code>{{ $product->sku ?? '-' }}</code></div>
                                                <div class="d-flex flex-wrap justify-content-center gap-1 small">
                                                    @if($product->status === 'active')
                                                        <span class="badge bg-success">نشط</span>
                                                    @elseif($product->status === 'draft')
                                                        <span class="badge bg-secondary">مسودة</span>
                                                    @else
                                                        <span class="badge bg-danger">أرشيف</span>
                                                    @endif
                                                    @if($product->is_featured)
                                                        <span class="badge bg-warning text-dark">مميز</span>
                                                    @endif
                                                </div>
                                                <form action="{{ route('admin.products.compare.remove', $product) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">إزالة من المقارنة</button>
                                                </form>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-secondary">
                                    <th colspan="{{ 1 + $products->count() }}">المعلومات الأساسية</th>
                                </tr>
                                @php $vals = $products->map(fn($p) => optional($p->category)->name ?? '—')->all(); @endphp
                                <tr>
                                    <th>التصنيف</th>
                                    @foreach($products as $product)
                                        <td class="{{ $diffClass($vals) }}">{{ $product->category->name ?? '—' }}</td>
                                    @endforeach
                                </tr>
                                @php $vals = $products->map(fn($p) => optional($p->brand)->name ?? '—')->all(); @endphp
                                <tr>
                                    <th>الماركة</th>
                                    @foreach($products as $product)
                                        <td class="{{ $diffClass($vals) }}">{{ $product->brand->name ?? '—' }}</td>
                                    @endforeach
                                </tr>
                                @php $vals = $products->map(fn($p) => $p->slug)->all(); @endphp
                                <tr>
                                    <th>الرابط (Slug)</th>
                                    @foreach($products as $product)
                                        <td class="small {{ $diffClass($vals) }}"><code>{{ $product->slug }}</code></td>
                                    @endforeach
                                </tr>

                                <tr class="table-secondary">
                                    <th colspan="{{ 1 + $products->count() }}">الأسعار والضرائب</th>
                                </tr>
                                @php $vals = $products->map(fn($p) => (float) $p->effective_price)->all(); @endphp
                                <tr>
                                    <th>السعر الحالي</th>
                                    @foreach($products as $product)
                                        <td class="{{ $diffClass($vals) }}">{{ $formatPrice($product->effective_price) }}</td>
                                    @endforeach
                                </tr>
                                @php $vals = $products->map(fn($p) => (float) ($p->compare_at_price ?? 0))->all(); @endphp
                                <tr>
                                    <th>سعر المقارنة</th>
                                    @foreach($products as $product)
                                        <td class="{{ $diffClass($vals) }}">
                                            @if($product->compare_at_price)
                                                <del>{{ $formatPrice($product->compare_at_price) }}</del>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>

                                <tr class="table-secondary">
                                    <th colspan="{{ 1 + $products->count() }}">السمات والمتغيرات</th>
                                </tr>
                                <tr>
                                    <th>السمات / المتغيرات</th>
                                    @foreach($products as $product)
                                        @php
                                            $parts = $product->variants->flatMap(function ($v) {
                                                return $v->attributeValues->map(fn ($av) => ($av->attribute->name ?? '') . ': ' . ($av->value ?? ''));
                                            })->unique()->values()->all();
                                        @endphp
                                        <td class="small">{{ $parts ? implode(' | ', $parts) : '—' }}</td>
                                    @endforeach
                                </tr>
                                @php $vals = $products->map(fn($p) => $p->variants->count())->all(); @endphp
                                <tr>
                                    <th>عدد المتغيرات</th>
                                    @foreach($products as $product)
                                        <td class="{{ $diffClass($vals) }}">{{ $product->variants->count() }}</td>
                                    @endforeach
                                </tr>

                                <tr class="table-secondary">
                                    <th colspan="{{ 1 + $products->count() }}">التقييمات</th>
                                </tr>
                                @php $vals = $products->map(fn($p) => round((float) $p->reviews->avg('rating'), 2))->all(); @endphp
                                <tr>
                                    <th>متوسط التقييم</th>
                                    @foreach($products as $product)
                                        @php $avg = $product->reviews->avg('rating'); @endphp
                                        <td class="{{ $diffClass($vals) }}">
                                            @if($avg)
                                                {{ number_format($avg, 1) }} / 5
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                @php $vals = $products->map(fn($p) => $p->reviews->count())->all(); @endphp
                                <tr>
                                    <th>عدد المراجعات</th>
                                    @foreach($products as $product)
                                        <td class="{{ $diffClass($vals) }}">{{ $product->reviews->count() }}</td>
                                    @endforeach
                                </tr>

                                <tr class="table-secondary">
                                    <th colspan="{{ 1 + $products->count() }}">معلومات إضافية</th>
                                </tr>
                                @php $vals = $products->map(fn($p) => (bool) $p->is_digital)->all(); @endphp
                                <tr>
                                    <th>منتج رقمي</th>
                                    @foreach($products as $product)
                                        <td class="{{ $diffClass($vals) }}">{{ $product->is_digital ? 'نعم' : 'لا' }}</td>
                                    @endforeach
                                </tr>
                                @php $vals = $products->map(fn($p) => (bool) $p->is_visible)->all(); @endphp
                                <tr>
                                    <th>ظاهر في المتجر</th>
                                    @foreach($products as $product)
                                        <td class="{{ $diffClass($vals) }}">{{ $product->is_visible ? 'نعم' : 'لا' }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary btn-sm">
                    إضافة منتجات أخرى للمقارنة
                </a>
            </div>
        </div>
    </div>
@stop

@section('styles')
<style>
.compare-table th { white-space: nowrap; }
.compare-table td { vertical-align: middle !important; }
@media (max-width: 768px) {
    .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
}
</style>
@endsection
