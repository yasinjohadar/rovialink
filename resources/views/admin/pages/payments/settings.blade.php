@extends('admin.layouts.master')

@section('page-title')
    إعدادات الدفع
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">إعدادات الدفع</h5>
                    <p class="text-muted mb-0 small">ضبط مفاتيح Stripe وPayPal والعملة الافتراضية من لوحة التحكم — بدون ملف .env.</p>
                </div>
            </div>

            <form action="{{ route('admin.payments.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card mb-3">
                    <div class="card-header">
                        <div class="card-title mb-0">عام</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">العملة الافتراضية</label>
                                <input type="text" name="payment_default_currency" maxlength="3"
                                    class="form-control text-uppercase @error('payment_default_currency') is-invalid @enderror"
                                    value="{{ old('payment_default_currency', $settings['payment_default_currency'] ?? 'SAR') }}">
                                <small class="text-muted">مثال: SAR, USD, EUR</small>
                                @error('payment_default_currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">Stripe</div>
                        <span class="badge bg-light text-dark">Hosted Checkout</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Publishable Key</label>
                                <input type="text" name="stripe_publishable_key"
                                    class="form-control @error('stripe_publishable_key') is-invalid @enderror"
                                    value="{{ old('stripe_publishable_key', $settings['stripe_publishable_key'] ?? '') }}"
                                    placeholder="pk_test_...">
                                @error('stripe_publishable_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Secret Key</label>
                                <input type="password" name="stripe_secret_key" class="form-control" autocomplete="new-password"
                                    placeholder="{{ ($settings['stripe_secret_configured'] ?? false) ? '•••••••• (محفوظ — اتركه فارغاً للإبقاء)' : 'sk_test_...' }}">
                                @if($settings['stripe_secret_configured'] ?? false)
                                    <small class="text-success">مفتاح سري محفوظ</small>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Webhook Signing Secret</label>
                                <input type="password" name="stripe_webhook_secret" class="form-control" autocomplete="new-password"
                                    placeholder="{{ ($settings['stripe_webhook_configured'] ?? false) ? '•••••••• (محفوظ)' : 'whsec_...' }}">
                                @if($settings['stripe_webhook_configured'] ?? false)
                                    <small class="text-success">سر Webhook محفوظ</small>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Webhook URL (انسخه إلى Stripe Dashboard)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" readonly value="{{ $webhookUrls['stripe'] ?? '' }}" id="stripe-webhook-url">
                                    <button type="button" class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(document.getElementById('stripe-webhook-url').value)">نسخ</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <div class="card-title mb-0">PayPal</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">الوضع</label>
                                <select name="paypal_mode" class="form-select @error('paypal_mode') is-invalid @enderror">
                                    <option value="sandbox" {{ old('paypal_mode', $settings['paypal_mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox (اختبار)</option>
                                    <option value="live" {{ old('paypal_mode', $settings['paypal_mode'] ?? '') === 'live' ? 'selected' : '' }}>Live (إنتاج)</option>
                                </select>
                                @error('paypal_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Client ID</label>
                                <input type="text" name="paypal_client_id" class="form-control"
                                    value="{{ old('paypal_client_id', $settings['paypal_client_id'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Client Secret</label>
                                <input type="password" name="paypal_client_secret" class="form-control" autocomplete="new-password"
                                    placeholder="{{ ($settings['paypal_secret_configured'] ?? false) ? '•••••••• (محفوظ)' : '' }}">
                                @if($settings['paypal_secret_configured'] ?? false)
                                    <small class="text-success">سر العميل محفوظ</small>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Webhook ID</label>
                                <input type="text" name="paypal_webhook_id" class="form-control"
                                    value="{{ old('paypal_webhook_id', $settings['paypal_webhook_id'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Webhook URL (انسخه إلى PayPal Developer)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" readonly value="{{ $webhookUrls['paypal'] ?? '' }}" id="paypal-webhook-url">
                                    <button type="button" class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(document.getElementById('paypal-webhook-url').value)">نسخ</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <strong>ملاحظة:</strong> يمكنك أيضاً تخصيص مفاتيح لكل وسيلة دفع من
                    <a href="{{ route('admin.payment-methods.index') }}">وسائل الدفع</a>
                    — الإعدادات هنا هي الافتراضية العامة.
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">المدفوعات</a>
                </div>
            </form>
        </div>
    </div>
@stop
