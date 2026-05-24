@extends('store.layouts.master')

@section('title', 'السلة')

@section('content')
    <h4>السلة</h4>
    @if($cart->items->isEmpty())
        <p class="text-muted">السلة فارغة.</p>
        <a href="{{ route('store.products.index') }}" class="btn btn-primary">تصفح المنتجات</a>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>السعر</th>
                    <th>الكمية</th>
                    <th>المجموع</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product->name }}</strong>
                            @if($item->variant)
                                <br><small class="text-muted">{{ $item->variant->display_name }}</small>
                            @endif
                        </td>
                        <td>{{ format_money($item->unit_price) }}</td>
                        <td>
                            <form action="{{ route('store.cart.update', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" style="width: 70px;" class="form-control form-control-sm d-inline-block">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">تحديث</button>
                            </form>
                        </td>
                        <td>{{ format_money($item->line_total) }}</td>
                        <td>
                            <form action="{{ route('store.cart.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('إزالة من السلة؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p><strong>المجموع:</strong> {{ format_money($cart->subtotal) }}</p>
        @if($cart->coupon_code)
            <p class="text-success">كوبون: {{ $cart->coupon_code }} — خصم: {{ format_money($cart->discount_amount) }}</p>
            <form action="{{ route('store.cart.remove-coupon') }}" method="POST" class="mb-3">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">إزالة الكوبون</button>
            </form>
        @else
            <form action="{{ route('store.cart.apply-coupon') }}" method="POST" class="row g-2 align-items-end mb-3" style="max-width: 400px;">
                @csrf
                <div class="col">
                    <label class="form-label small">كود الخصم</label>
                    <input type="text" name="code" class="form-control" placeholder="أدخل الكود" value="{{ old('code') }}" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-primary">تطبيق</button>
                </div>
            </form>
            @error('code')
                <p class="text-danger small">{{ $message }}</p>
            @enderror
        @endif
        @if($cart->discount_amount > 0)
            <p><strong>الإجمالي بعد الخصم:</strong> {{ number_format(max(0, $cart->subtotal - $cart->discount_amount), 2) }}</p>
        @endif
        <div class="mt-3">
            <a href="{{ route('store.products.index') }}" class="btn btn-outline-secondary">متابعة التسوق</a>
            <a href="{{ route('store.checkout.index') }}" class="btn btn-primary">إتمام الطلب</a>
        </div>
    @endif
@endsection
