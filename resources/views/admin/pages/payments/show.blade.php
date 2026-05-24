@extends('admin.layouts.master')

@section('page-title')
    دفع #{{ $payment->id }}
@stop

@section('content')
    @include('admin.pages.payments.partials.alerts')

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="my-4">
                <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary mb-3">← العودة</a>
                <h4 class="mb-0">دفع #{{ $payment->id }}</h4>
            </div>

            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <p><strong>الحالة:</strong> {{ $payment->status }}</p>
                            <p><strong>المبلغ:</strong> {{ number_format($payment->amount, 2) }} {{ $payment->currency }}</p>
                            <p><strong>الوسيلة:</strong> {{ $payment->paymentMethod?->name }}</p>
                            <p><strong>معرف المعاملة:</strong> <code>{{ $payment->transaction_id ?? '—' }}</code></p>
                            <p><strong>paid_at:</strong> {{ $payment->paid_at?->format('Y-m-d H:i') ?? '—' }}</p>
                            @if($payment->order)
                            <p><strong>الطلب:</strong> <a href="{{ route('admin.orders.show', $payment->order) }}">#{{ $payment->order->order_number }}</a></p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body d-flex flex-column gap-2">
                            @if($payment->status === 'pending' && $payment->paymentMethod?->isManual())
                            <form method="POST" action="{{ route('admin.payments.confirm', $payment) }}">@csrf
                                <button type="submit" class="btn btn-success w-100">تأكيد الدفع يدوياً</button>
                            </form>
                            <form method="POST" action="{{ route('admin.payments.reject', $payment) }}">@csrf
                                <button type="submit" class="btn btn-outline-danger w-100">رفض</button>
                            </form>
                            @endif
                            @if($payment->status === 'completed')
                            <form method="POST" action="{{ route('admin.payments.refund', $payment) }}">@csrf
                                <input type="hidden" name="amount" value="{{ $payment->amount }}">
                                <button type="submit" class="btn btn-warning w-100" onclick="return confirm('استرداد كامل؟')">استرداد كامل</button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @if($payment->refunds->isNotEmpty())
                    <div class="card mt-3">
                        <div class="card-header">الاستردادات</div>
                        <ul class="list-group list-group-flush">
                            @foreach($payment->refunds as $refund)
                            <li class="list-group-item">{{ number_format($refund->amount,2) }} — {{ $refund->status }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>

            @if($payment->metadata)
            <div class="card mt-3">
                <div class="card-header">Metadata</div>
                <pre class="card-body small mb-0">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif
        </div>
    </div>
@endsection
