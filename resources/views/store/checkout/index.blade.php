@extends('store.layouts.master')

@section('title', 'إتمام الطلب')

@section('content')
    <h4>إتمام الطلب</h4>
    <p class="text-muted small mb-3">متجر منتجات رقمية — لا يتطلب عنوان شحن.</p>
    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('store.checkout.store') }}">
                @csrf
                <div class="card mb-3">
                    <div class="card-header">بيانات المشتري</div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">الاسم الأول</label>
                                <input type="text" name="first_name" class="form-control"
                                    value="{{ old('first_name', auth()->user() ? explode(' ', auth()->user()->name, 2)[0] : '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">اسم العائلة</label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الهاتف</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', auth()->user()->email ?? '') }}">
                            </div>
                            <input type="hidden" name="country" value="{{ old('country', 'SA') }}">
                            <div class="col-12">
                                <label class="form-label">ملاحظات (اختياري)</label>
                                <textarea name="customer_note" class="form-control" rows="2">{{ old('customer_note') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">إنشاء الطلب</button>
            </form>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">ملخص السلة</div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($cart->items as $item)
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span>
                                    {{ $item->product->name }}
                                    @if($item->variant)<br><small class="text-muted">{{ $item->variant->display_name }}</small>@endif
                                </span>
                                <span>{{ $item->quantity }} × {{ number_format($item->unit_price, 2) }} ر.س</span>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-2 mb-0"><strong>المجموع: {{ number_format($cart->subtotal, 2) }} ر.س</strong></p>
                    <p class="text-muted small mb-0">التسليم رقمي فوري بعد إتمام الطلب</p>
                </div>
            </div>
        </div>
    </div>
@endsection
