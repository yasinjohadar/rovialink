@extends('admin.layouts.master')

@section('page-title')
    تفاصيل التصنيف
@stop

@section('css')
<style>
    .category-show-compact .table-compact th {
        width: 11rem;
        padding: 0.3rem 0.65rem;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--bs-secondary-color);
        white-space: nowrap;
        vertical-align: middle;
        background: rgba(var(--bs-body-color-rgb), 0.04);
    }
    .category-show-compact .table-compact td {
        padding: 0.3rem 0.65rem;
        font-size: 0.82rem;
        vertical-align: middle;
    }
    .category-show-compact .table-compact tr:not(:last-child) th,
    .category-show-compact .table-compact tr:not(:last-child) td {
        border-bottom: 1px solid rgba(var(--bs-body-color-rgb), 0.08);
    }
    .category-show-compact .mini-thumb {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid rgba(var(--bs-body-color-rgb), 0.12);
    }
    .category-show-compact .card-header {
        padding: 0.5rem 0.85rem;
        font-size: 0.88rem;
    }
    .category-show-compact .card-body {
        padding: 0;
    }
    .category-show-compact .section-divider td {
        padding: 0.4rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--bs-primary);
        background: rgba(var(--bs-primary-rgb), 0.08);
        border-bottom: none !important;
    }
</style>
@stop

@section('content')
    <div class="main-content app-content category-show-compact">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between my-3 gap-2 flex-wrap">
                <h5 class="page-title fs-6 mb-0">تفاصيل التصنيف: {{ $category->name }}</h5>
                <div class="d-flex gap-2">
                    @can('category-edit')
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary btn-sm py-0 px-2">
                            <i class="bi bi-pencil"></i> تعديل
                        </a>
                    @endcan
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm py-0 px-2">رجوع</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-compact mb-0">
                            <tbody>
                                <tr class="section-divider"><td colspan="2">المعلومات الأساسية</td></tr>
                                <tr>
                                    <th>الاسم</th>
                                    <td>{{ $category->name }}</td>
                                </tr>
                                <tr>
                                    <th>الرابط</th>
                                    <td><code class="small">{{ $category->slug ?? '—' }}</code></td>
                                </tr>
                                <tr>
                                    <th>التصنيف الأب</th>
                                    <td>
                                        @if($category->parent)
                                            <a href="{{ route('admin.categories.show', $category->parent->id) }}" class="small">{{ $category->parent->name }}</a>
                                        @else
                                            <span class="text-muted">رئيسي</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>الوصف</th>
                                    <td>{{ $category->description ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>الترتيب</th>
                                    <td>{{ $category->order }}</td>
                                </tr>
                                <tr>
                                    <th>الحالة</th>
                                    <td>
                                        @if($category->status === 'active')
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>المنتجات</th>
                                    <td>{{ $category->products->count() }}</td>
                                </tr>
                                @if($category->children->count() > 0)
                                    <tr>
                                        <th>تصنيفات فرعية</th>
                                        <td>{{ $category->children->count() }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>تاريخ الإنشاء</th>
                                    <td>{{ $category->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>آخر تحديث</th>
                                    <td>{{ $category->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                @if($category->image || $category->cover_image)
                                    <tr>
                                        <th>الصور</th>
                                        <td>
                                            <div class="d-flex gap-2 flex-wrap">
                                                @if($category->image)
                                                    <img src="{{ category_image_url($category->image, $category->id) }}" alt="" class="mini-thumb" title="صورة التصنيف">
                                                @endif
                                                @if($category->cover_image)
                                                    <img src="{{ category_image_url($category->cover_image, $category->id) }}" alt="" class="mini-thumb" title="الغلاف">
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                                <tr class="section-divider"><td colspan="2">SEO</td></tr>
                                <tr>
                                    <th>عنوان SEO</th>
                                    <td>{{ $category->meta_title ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>وصف SEO</th>
                                    <td>{{ $category->meta_description ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>كلمات مفتاحية</th>
                                    <td>{{ $category->meta_keywords ?: '—' }}</td>
                                </tr>

                                @if($category->children->count() > 0)
                                    <tr class="section-divider"><td colspan="2">التصنيفات الفرعية ({{ $category->children->count() }})</td></tr>
                                    @foreach($category->children as $child)
                                        <tr>
                                            <th class="fw-normal">{{ $child->name }}</th>
                                            <td>
                                                <code class="small">{{ $child->slug ?? '—' }}</code>
                                                @if($child->status === 'active')
                                                    <span class="badge bg-success ms-1" style="font-size:0.65rem;">نشط</span>
                                                @else
                                                    <span class="badge bg-danger ms-1" style="font-size:0.65rem;">غير نشط</span>
                                                @endif
                                                <a href="{{ route('admin.categories.show', $child->id) }}" class="btn btn-info btn-sm py-0 px-1 ms-1"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <h6 class="text-primary mb-0">منتجات هذا التصنيف ({{ $category->products->count() }})</h6>
                    <a href="{{ route('admin.products.create', ['category_id' => $category->id]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> إضافة منتج
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($category->products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;">الصورة</th>
                                        <th>الاسم</th>
                                        <th>SKU</th>
                                        <th>العلامة</th>
                                        <th>السعر</th>
                                        <th>الحالة</th>
                                        <th style="min-width: 100px;">العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($category->products as $product)
                                        <tr>
                                            <td>
                                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                                     style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px;">
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.products.show', $product) }}" class="text-decoration-none fw-semibold">
                                                    {{ $product->name }}
                                                </a>
                                                @if($product->is_featured)
                                                    <span class="badge bg-warning text-dark ms-1">مميز</span>
                                                @endif
                                            </td>
                                            <td><code>{{ $product->sku ?? '—' }}</code></td>
                                            <td>{{ $product->brand->name ?? '—' }}</td>
                                            <td class="text-nowrap">{{ number_format((float) $product->effective_price, 2) }} ر.س</td>
                                            <td>
                                                @if($product->status === 'active')
                                                    <span class="badge bg-success">نشط</span>
                                                @elseif($product->status === 'draft')
                                                    <span class="badge bg-secondary">مسودة</span>
                                                @else
                                                    <span class="badge bg-danger">أرشيف</span>
                                                @endif
                                                @unless($product->is_visible)
                                                    <span class="badge bg-warning text-dark ms-1">مخفي</span>
                                                @endunless
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-info me-1" title="عرض">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-primary" title="تعديل">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-4 mb-0">لا توجد منتجات مرتبطة بهذا التصنيف.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
