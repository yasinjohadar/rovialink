@extends('store.layouts.master')

@section('title', 'تم إنشاء الطلب')

@section('content')
    <h4>تم إنشاء الطلب بنجاح</h4>
    <p>رقم الطلب: <strong>{{ $order->order_number }}</strong></p>
    <table class="table">
        <thead>
            <tr>
                <th>المنتج</th>
                <th>الكمية</th>
                <th>السعر</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}
                        @if($item->variant_description)
                            <br><small class="text-muted">{{ $item->variant_description }}</small>
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }} ر.س</td>
                    <td>{{ number_format($item->total, 2) }} ر.س</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $downloads = $order->items->flatMap(fn($i) => $i->downloads ?? collect())->filter();
    @endphp
    @if($downloads->isNotEmpty())
        <hr>
        <h5 class="mt-3">الملفات القابلة للتحميل</h5>
        <div class="list-group mb-3">
            @foreach($order->items as $item)
                @foreach($item->downloads as $download)
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                       href="{{ route('store.downloads.show', $download->download_token) }}">
                        <span>
                            {{ $download->file->title ?? 'ملف' }}
                            <small class="text-muted d-block">{{ $item->product_name }}</small>
                        </span>
                        <span class="text-muted small">
                            @if(!is_null($download->remaining_downloads))
                                {{ $download->remaining_downloads }} تحميلات متبقية
                            @else
                                غير محدود
                            @endif
                        </span>
                    </a>
                @endforeach
            @endforeach
        </div>
        <small class="text-muted d-block">قد تنتهي صلاحية التحميل حسب إعدادات المنتج.</small>
    @endif

    <p><strong>الإجمالي: {{ number_format($order->total, 2) }} ر.س</strong></p>
    <a href="{{ route('store.products.index') }}" class="btn btn-primary">العودة للمتجر</a>
@endsection
