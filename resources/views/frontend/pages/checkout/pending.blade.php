@extends('frontend.layouts.master')

@section('content')
    <main class="container py-5 section-fade-up">
        <div class="text-center mb-5">
            <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width:88px;height:88px;">
                <i class="fas fa-clock fa-3x text-warning"></i>
            </div>
            <h1 class="fw-bold mb-2">بانتظار تأكيد الدفع</h1>
            <p class="text-secondary">طلبك <span class="en-text fw-bold">#{{ $order->order_number }}</span> مسجّل وينتظر إتمام الدفع.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="glass-card p-4 mb-4">
                    @if($method ?? $payment?->paymentMethod)
                    @php $method = $method ?? $payment->paymentMethod; @endphp
                    <h5 class="account-panel__title mb-3">{{ $method->name }}</h5>

                    @if($method->driver === 'bank_transfer')
                        @php $cfg = $method->config ?? []; @endphp

                        @if(!empty($cfg['customer_notice']))
                        <div class="checkout-bank-notice mb-3" role="alert">
                            <div class="checkout-bank-notice__icon"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="checkout-bank-notice__body">
                                <strong class="checkout-bank-notice__title">تنبيه مهم</strong>
                                <p class="mb-0">{!! nl2br(e($cfg['customer_notice'])) !!}</p>
                            </div>
                        </div>
                        @endif

                        <ul class="list-unstyled mb-0">
                            @if(!empty($cfg['bank_name']))<li class="mb-2"><strong>البنك:</strong> {{ $cfg['bank_name'] }}</li>@endif
                            @if(!empty($cfg['iban']))<li class="mb-2"><strong>IBAN:</strong> <span class="en-text">{{ $cfg['iban'] }}</span></li>@endif
                            @if(!empty($cfg['account_name']))<li class="mb-2"><strong>اسم الحساب:</strong> {{ $cfg['account_name'] }}</li>@endif
                            @if(!empty($cfg['instructions']))<li class="mb-2 text-secondary">{!! nl2br(e($cfg['instructions'])) !!}</li>@endif
                        </ul>
                        @if(!empty($payment?->metadata['bank_reference']))
                        <p class="mt-2 mb-0 small"><strong>مرجع التحويل:</strong> <span class="en-text">{{ $payment->metadata['bank_reference'] }}</span></p>
                        @endif
                        @if(!empty($payment?->metadata['payment_receipt_path']))
                        <p class="mt-2 mb-0 small text-success"><i class="fas fa-check-circle me-1"></i> تم استلام إيصال التحويل وسيتم مراجعته.</p>
                        @endif
                        <p class="mt-3 mb-0 text-secondary small">المبلغ المطلوب: <strong class="text-accent en-text">{{ number_format($order->total, 2) }} {{ $order->currency ?? 'SAR' }}</strong></p>
                    @elseif($method->driver === 'cod')
                        <p class="text-secondary mb-0">{{ $method->config['instructions'] ?? 'سيتواصل معك فريقنا لإتمام الدفع.' }}</p>
                    @endif
                    @endif
                </div>

                <div class="d-flex gap-2 flex-wrap justify-content-center">
                    <a href="{{ route('frontend.account.orders.show', $order) }}" class="btn btn-accent px-4">عرض الطلب</a>
                    <a href="{{ route('frontend.shop.index') }}" class="btn btn-glass px-4">متابعة التسوق</a>
                </div>
            </div>
        </div>
    </main>
@endsection
