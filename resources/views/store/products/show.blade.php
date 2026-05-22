@extends('store.layouts.master')

@section('title', $product->name)

@section('content')
    <div class="row">
        <div class="col-md-5">
            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-height: 400px; object-fit: contain;">
        </div>
        <div class="col-md-7">
            <h1>{{ $product->name }}</h1>
            @if($product->is_digital)
                <div class="mb-2">
                    <span class="badge bg-info">منتج رقمي قابل للتحميل</span>
                </div>
            @endif
            @if($product->short_description)
                <p class="text-muted">{{ $product->short_description }}</p>
            @endif

            @php
                $hasVariants = $product->variants->isNotEmpty();
                $productAttributes = $product->attributes->filter(fn($a) => $a->values->isNotEmpty());
            @endphp

            @if($hasVariants && $productAttributes->isNotEmpty())
                {{-- منتج بمتغيرات: نعرض منتقي السمات --}}
                <div id="variant-selectors" class="mb-3">
                    @foreach($productAttributes as $attr)
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $attr->name }}</label>
                            <div class="d-flex flex-wrap gap-2 align-items-center" data-attribute-id="{{ $attr->id }}">
                                @if($attr->type === 'color')
                                    @foreach($attr->values as $val)
                                        <button type="button" class="btn btn-outline-secondary variant-option border rounded-circle p-0 align-middle"
                                                data-attribute-id="{{ $attr->id }}" data-value-id="{{ $val->id }}"
                                                style="width: 32px; height: 32px; background-color: {{ $val->color_hex ? '#' . ltrim($val->color_hex, '#') : '#eee' }}; border: 2px solid #dee2e6 !important;"
                                                title="{{ $val->value }}"></button>
                                    @endforeach
                                @else
                                    @foreach($attr->values as $val)
                                        <button type="button" class="btn btn-outline-secondary variant-option"
                                                data-attribute-id="{{ $attr->id }}" data-value-id="{{ $val->id }}">{{ $val->value }}</button>
                                    @endforeach
                                @endif
                            </div>
                            <small class="text-muted variant-required-msg" data-attribute-id="{{ $attr->id }}" style="display: none;">اختر {{ $attr->name }}</small>
                        </div>
                    @endforeach
                </div>

                <p class="mb-2"><strong>السعر:</strong> <span id="variant-price">{{ $product->effective_price ? number_format($product->effective_price, 2) . ' ر.س' : '—' }}</span></p>

                <form action="{{ route('store.cart.store') }}" method="POST" class="row g-2 align-items-end" id="add-to-cart-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="product_variant_id" id="product_variant_id" value="">
                    <div class="col-auto">
                        <label class="form-label mb-0">الكمية</label>
                        <input type="number" name="quantity" value="1" min="1" class="form-control" style="width: 90px;">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary" id="add-to-cart-btn" disabled>اختر الخيارات أعلاه</button>
                    </div>
                </form>
            @else
                {{-- منتج بسيط بدون متغيرات --}}
                <p class="mb-2"><strong>السعر:</strong> {{ number_format($product->effective_price, 2) }} ر.س</p>
                <form action="{{ route('store.cart.store') }}" method="POST" class="row g-2 align-items-end">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="col-auto">
                        <label class="form-label mb-0">الكمية</label>
                        <input type="number" name="quantity" value="1" min="1" class="form-control" style="width: 90px;">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary" @disabled(! $product->in_stock)>إضافة للسلة</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    @if($product->description)
        <div class="mt-4">
            <h5>الوصف</h5>
            <div>{!! nl2br(e($product->description)) !!}</div>
        </div>
    @endif

    @if($hasVariants && $productAttributes->isNotEmpty())
        @push('scripts')
        <script>
        (function() {
            const variants = @json($product->variants->map(fn($v) => [
                'id' => $v->id,
                'price' => $v->effective_price,
                'attribute_value_ids' => $v->attributeValues->pluck('id')->sort()->values()->toArray()
            ])->values());
            const attributeIds = @json($productAttributes->pluck('id')->values());
            const priceEl = document.getElementById('variant-price');
            const variantIdInput = document.getElementById('product_variant_id');
            const addBtn = document.getElementById('add-to-cart-btn');
            const productInStock = @json($product->in_stock);

            function getSelectedValueIds() {
                const selected = {};
                document.querySelectorAll('.variant-option.active').forEach(function(btn) {
                    selected[parseInt(btn.dataset.attributeId, 10)] = parseInt(btn.dataset.valueId, 10);
                });
                return selected;
            }

            function getSortedValueIds() {
                const selected = getSelectedValueIds();
                const sorted = attributeIds.map(aid => selected[aid]).filter(Boolean);
                return sorted.length === attributeIds.length ? sorted.sort((a,b)=>a-b) : null;
            }

            function findVariant() {
                const ids = getSortedValueIds();
                if (!ids || ids.length === 0) return null;
                const key = JSON.stringify(ids);
                return variants.find(v => JSON.stringify(v.attribute_value_ids) === key) || null;
            }

            function updateUI() {
                const variant = findVariant();
                document.querySelectorAll('.variant-required-msg').forEach(el => el.style.display = 'none');
                if (variant) {
                    variantIdInput.value = variant.id;
                    priceEl.textContent = (variant.price != null ? Number(variant.price).toFixed(2) : '—') + ' ر.س';
                    addBtn.disabled = !productInStock;
                    addBtn.textContent = productInStock ? 'إضافة للسلة' : 'غير متوفر';
                } else {
                    variantIdInput.value = '';
                    const missing = attributeIds.filter(aid => !getSelectedValueIds()[aid]);
                    if (missing.length) {
                        const firstAttr = document.querySelector('.variant-required-msg[data-attribute-id="' + missing[0] + '"]');
                        if (firstAttr) firstAttr.style.display = 'inline';
                    }
                    priceEl.textContent = '—';
                    addBtn.disabled = true;
                    addBtn.textContent = 'اختر الخيارات أعلاه';
                }
            }

            document.querySelectorAll('.variant-option').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const aid = parseInt(this.dataset.attributeId, 10);
                    document.querySelectorAll('.variant-option[data-attribute-id="' + aid + '"]').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    updateUI();
                });
            });
            updateUI();
        })();
        </script>
        @endpush
    @endif
@endsection
