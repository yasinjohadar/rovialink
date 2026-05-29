<div class="table-responsive">
    <table class="table table-striped table-hover align-middle table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th style="width: 40px;">
                    <input type="checkbox" id="select-all-products">
                </th>
                <th style="width: 50px;">#</th>
                <th style="min-width: 70px;">الصورة</th>
                <th style="min-width: 200px;">الاسم</th>
                <th>SKU</th>
                <th>التصنيف</th>
                <th>السعر</th>
                <th>الحالة</th>
                <th style="min-width: 160px;">العمليات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>
                        <input type="checkbox" name="ids[]" form="bulk-products-form" value="{{ $product->id }}">
                    </td>
                    <td>{{ $products->firstItem() + $loop->index }}</td>
                    <td>
                        <img src="{{ $product->card_image_url }}" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                    </td>
                    <td>
                        <a href="{{ route('admin.products.show', $product) }}" class="text-decoration-none">{{ $product->name }}</a>
                        @if($product->is_featured)
                            <span class="badge bg-warning text-dark ms-1">مميز</span>
                        @endif
                    </td>
                    <td><code>{{ $product->sku ?? '-' }}</code></td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>{{ $currencyService->format((float) $product->effective_price) }}</td>
                    <td>
                        @if($product->status == 'active')
                            <span class="badge bg-success">نشط</span>
                        @elseif($product->status == 'draft')
                            <span class="badge bg-secondary">مسودة</span>
                        @else
                            <span class="badge bg-danger">أرشيف</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-info" title="عرض"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-primary" title="تعديل"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.products.duplicate', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('استنساخ هذا المنتج؟');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="استنساخ"><i class="bi bi-files"></i></button>
                        </form>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف المنتج؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="حذف"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">لا توجد منتجات</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($products->hasPages())
    <div class="mt-3" id="products-pagination">{{ $products->withQueryString()->links() }}</div>
@endif
