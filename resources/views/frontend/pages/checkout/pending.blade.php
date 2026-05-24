@extends('frontend.layouts.master')

@section('content')
@php
    $method = $payment?->paymentMethod;
    $cfg = $method?->config ?? [];
    $hasReceipt = ! empty($payment?->metadata['payment_receipt_path']);
@endphp

<div class="page-hero page-hero--pending">
    <div class="page-hero-content container">
        <div class="page-hero-icon page-hero-icon--pending"><i class="fas fa-hourglass-half"></i></div>
        <h1 class="page-hero-title">بانتظار تأكيد الدفع</h1>
        <p class="page-hero-subtitle">طلبك مسجّل بنجاح — نراجع تفاصيل الدفع ونُعلمك فور التأكيد.</p>
        <nav class="page-hero-breadcrumb" aria-label="breadcrumb">
            <a href="{{ route('frontend.home') }}">الرئيسية</a>
            <i class="fas fa-chevron-left sep"></i>
            <a href="{{ route('frontend.account.orders.show', $order) }}">طلب #{{ $order->order_number }}</a>
            <i class="fas fa-chevron-left sep"></i>
            <span class="current">تأكيد الدفع</span>
        </nav>
    </div>
    <div class="page-hero-wave">
        <svg viewBox="0 0 1440 65" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,30 C360,65 1080,0 1440,30 L1440,65 L0,65 Z" style="fill:var(--page-bg)"/>
        </svg>
    </div>
</div>

<main class="checkout-pending-page container pb-5 section-fade-up">
    <div class="checkout-pending-status-bar">
        <span class="checkout-pending-status-bar__pulse" aria-hidden="true"></span>
        <span class="checkout-pending-status-bar__text">حالة الطلب: <strong>بانتظار المراجعة</strong></span>
        <span class="checkout-pending-status-bar__order en-text">#{{ $order->order_number }}</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            @if($method)
            <div class="glass-card checkout-pending-card mb-4">
                <div class="checkout-pending-card__head">
                    <div class="checkout-pending-card__icon">
                        @if($method->driver === 'bank_transfer')
                            <i class="fas fa-university"></i>
                        @elseif($method->driver === 'cod')
                            <i class="fas fa-hand-holding-usd"></i>
                        @else
                            <i class="fas fa-credit-card"></i>
                        @endif
                    </div>
                    <div>
                        <h2 class="checkout-pending-card__title">{{ $method->name }}</h2>
                        <p class="checkout-pending-card__subtitle mb-0">تفاصيل الدفع المطلوبة لإتمام طلبك</p>
                    </div>
                </div>

                @if($method->driver === 'bank_transfer')
                    @if(!empty($cfg['customer_notice']))
                    <div class="checkout-pending-notice" role="alert">
                        <div class="checkout-pending-notice__icon"><i class="fas fa-gem"></i></div>
                        <div class="checkout-pending-notice__body">
                            <strong class="checkout-pending-notice__title">تنبيه مهم</strong>
                            <p class="mb-0">{!! nl2br(e($cfg['customer_notice'])) !!}</p>
                        </div>
                    </div>
                    @endif

                    <div class="table-responsive checkout-pending-table-wrap">
                        <table class="checkout-pending-table">
                            <caption class="visually-hidden">بيانات التحويل البنكي</caption>
                            <tbody>
                                @if(!empty($cfg['bank_name']))
                                <tr>
                                    <th scope="row"><span class="checkout-pending-table__label"><i class="fas fa-building-columns"></i> البنك</span></th>
                                    <td class="checkout-pending-table__value">{{ $cfg['bank_name'] }}</td>
                                </tr>
                                @endif
                                @if(!empty($cfg['iban']))
                                <tr>
                                    <th scope="row"><span class="checkout-pending-table__label"><i class="fas fa-barcode"></i> IBAN</span></th>
                                    <td class="checkout-pending-table__value checkout-pending-table__value--copy en-text">{{ $cfg['iban'] }}</td>
                                </tr>
                                @endif
                                @if(!empty($cfg['account_name']))
                                <tr>
                                    <th scope="row"><span class="checkout-pending-table__label"><i class="fas fa-user-tie"></i> اسم الحساب</span></th>
                                    <td class="checkout-pending-table__value">{{ $cfg['account_name'] }}</td>
                                </tr>
                                @endif
                                @if(!empty($cfg['instructions']))
                                <tr>
                                    <th scope="row"><span class="checkout-pending-table__label"><i class="fas fa-circle-info"></i> تعليمات</span></th>
                                    <td class="checkout-pending-table__value checkout-pending-table__value--muted">{!! nl2br(e($cfg['instructions'])) !!}</td>
                                </tr>
                                @endif
                                @if(!empty($payment?->metadata['bank_reference']))
                                <tr>
                                    <th scope="row"><span class="checkout-pending-table__label"><i class="fas fa-hashtag"></i> مرجع التحويل</span></th>
                                    <td class="checkout-pending-table__value en-text">{{ $payment->metadata['bank_reference'] }}</td>
                                </tr>
                                @endif
                                <tr class="checkout-pending-table__row--highlight">
                                    <th scope="row"><span class="checkout-pending-table__label"><i class="fas fa-coins"></i> المبلغ المطلوب</span></th>
                                    <td class="checkout-pending-table__value checkout-pending-table__value--amount en-text">{{ format_money($order->total) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row"><span class="checkout-pending-table__label"><i class="fas fa-file-invoice"></i> إيصال التحويل</span></th>
                                    <td class="checkout-pending-table__value">
                                        @if($hasReceipt)
                                            <span class="checkout-pending-badge checkout-pending-badge--success"><i class="fas fa-check"></i> تم الاستلام — قيد المراجعة</span>
                                        @else
                                            <span class="checkout-pending-badge checkout-pending-badge--muted">لم يُرفَع بعد</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @elseif($method->driver === 'cod')
                    <div class="table-responsive checkout-pending-table-wrap">
                        <table class="checkout-pending-table">
                            <tbody>
                                <tr>
                                    <th scope="row"><span class="checkout-pending-table__label"><i class="fas fa-truck"></i> طريقة الدفع</span></th>
                                    <td class="checkout-pending-table__value">الدفع عند الاستلام</td>
                                </tr>
                                <tr>
                                    <th scope="row"><span class="checkout-pending-table__label"><i class="fas fa-comment-dots"></i> التعليمات</span></th>
                                    <td class="checkout-pending-table__value">{{ $method->config['instructions'] ?? 'سيتواصل معك فريقنا لإتمام الدفع.' }}</td>
                                </tr>
                                <tr class="checkout-pending-table__row--highlight">
                                    <th scope="row"><span class="checkout-pending-table__label"><i class="fas fa-coins"></i> المبلغ المطلوب</span></th>
                                    <td class="checkout-pending-table__value checkout-pending-table__value--amount en-text">{{ format_money($order->total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @endif

            <div class="glass-card checkout-pending-card">
                <div class="checkout-pending-card__head checkout-pending-card__head--compact">
                    <div class="checkout-pending-card__icon checkout-pending-card__icon--soft"><i class="fas fa-box-open"></i></div>
                    <div>
                        <h2 class="checkout-pending-card__title">محتويات الطلب</h2>
                        <p class="checkout-pending-card__subtitle mb-0">{{ $order->items->count() }} {{ $order->items->count() === 1 ? 'منتج' : 'منتجات' }}</p>
                    </div>
                </div>
                <div class="table-responsive checkout-pending-table-wrap">
                    <table class="checkout-pending-table checkout-pending-table--items">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th class="text-center">الكمية</th>
                                <th class="text-end">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="checkout-pending-product">
                                        <img src="{{ product_image_url($item->product?->primary_image?->path ?? null, $item->product_id) }}"
                                             alt="" width="52" height="52" class="checkout-pending-product__img">
                                        <span class="checkout-pending-product__name">{{ $item->product_name }}</span>
                                    </div>
                                </td>
                                <td class="text-center en-text">{{ $item->quantity }}</td>
                                <td class="text-end en-text checkout-pending-table__value--amount-sm">{{ format_money($item->total) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glass-card checkout-pending-summary position-sticky" style="top:100px;">
                <h2 class="checkout-pending-summary__title">ملخص الطلب</h2>

                <div class="table-responsive checkout-pending-table-wrap">
                    <table class="checkout-pending-table checkout-pending-table--summary">
                        <tbody>
                            <tr>
                                <th scope="row"><span class="checkout-pending-table__label">رقم الطلب</span></th>
                                <td class="en-text">{{ $order->order_number }}</td>
                            </tr>
                            <tr>
                                <th scope="row"><span class="checkout-pending-table__label">التاريخ</span></th>
                                <td>{{ $order->created_at->translatedFormat('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th scope="row"><span class="checkout-pending-table__label">وسيلة الدفع</span></th>
                                <td>{{ $method?->name ?? '—' }}</td>
                            </tr>
                            @if($order->discount_amount > 0)
                            <tr>
                                <th scope="row"><span class="checkout-pending-table__label">الخصم</span></th>
                                <td class="text-success en-text">−{{ format_money($order->discount_amount) }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="checkout-pending-summary__total">
                    <span class="checkout-pending-summary__total-label">الإجمالي المستحق</span>
                    <span class="checkout-pending-summary__total-value en-text">{{ format_money($order->total) }}</span>
                </div>

                <ul class="checkout-pending-timeline">
                    <li class="checkout-pending-timeline__item checkout-pending-timeline__item--done">
                        <span class="checkout-pending-timeline__dot"><i class="fas fa-check"></i></span>
                        <span class="checkout-pending-timeline__text">تم تسجيل الطلب</span>
                    </li>
                    <li class="checkout-pending-timeline__item {{ $hasReceipt ? 'checkout-pending-timeline__item--done' : 'checkout-pending-timeline__item--pending' }}">
                        <span class="checkout-pending-timeline__dot">
                            @if($hasReceipt)<i class="fas fa-check"></i>@else<i class="fas fa-clock"></i>@endif
                        </span>
                        <span class="checkout-pending-timeline__text">{{ $hasReceipt ? 'تم استلام الإيصال' : 'بانتظار الإيصال' }}</span>
                    </li>
                    <li class="checkout-pending-timeline__item checkout-pending-timeline__item--current">
                        <span class="checkout-pending-timeline__dot"><i class="fas fa-shield-halved"></i></span>
                        <span class="checkout-pending-timeline__text">تأكيد الدفع من الإدارة</span>
                    </li>
                </ul>

                <div class="checkout-pending-summary__actions">
                    <a href="{{ route('frontend.account.orders.show', $order) }}" class="btn btn-accent w-100">
                        <i class="fas fa-receipt me-2"></i> عرض الطلب
                    </a>
                    <a href="{{ route('frontend.shop.index') }}" class="btn btn-glass w-100 mt-2">متابعة التسوق</a>
                </div>

                <p class="checkout-pending-summary__hint mb-0">
                    <i class="fas fa-headset me-1"></i>
                    عادةً تتم المراجعة خلال 24 ساعة. للاستفسار تواصل مع الدعم.
                </p>
            </div>
        </div>
    </div>
</main>
@endsection
