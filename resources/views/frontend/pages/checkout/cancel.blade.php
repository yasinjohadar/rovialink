@extends('frontend.layouts.master')

@section('content')
    <main class="container py-5 section-fade-up text-center">
        <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width:88px;height:88px;">
            <i class="fas fa-times-circle fa-3x text-danger"></i>
        </div>
        <h1 class="fw-bold mb-2">تم إلغاء الدفع</h1>
        <p class="text-secondary mb-4">لم تكتمل عملية الدفع للطلب <span class="en-text fw-bold">#{{ $order->order_number }}</span>.</p>

        <form method="POST" action="{{ route('frontend.checkout.retry', $order) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-accent px-4 me-2">إعادة المحاولة</button>
        </form>
        <a href="{{ route('frontend.cart.index') }}" class="btn btn-glass px-4">العودة للسلة</a>
    </main>
@endsection
