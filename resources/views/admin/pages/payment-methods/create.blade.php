@extends('admin.layouts.master')

@section('page-title')
    إضافة وسيلة دفع
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h4 class="mb-0">إضافة وسيلة دفع</h4>
                    <p class="mb-0 text-muted">وسيلة دفع جديدة تظهر للعميل عند إتمام الطلب</p>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-right me-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.payment-methods.store') }}">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card custom-card mb-4">
                            <div class="card-header">
                                <div class="card-title">المعلومات الأساسية</div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required placeholder="مثال: الدفع عند الاستلام">
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الرابط (Slug)</label>
                                        <input type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" value="{{ old('slug') }}" placeholder="يُولد تلقائياً من الاسم إن تُرك فارغاً">
                                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">نوع وسيلة الدفع <span class="text-danger">*</span></label>
                                        <select name="driver" id="driver" class="form-select @error('driver') is-invalid @enderror" required>
                                            @foreach($drivers as $key => $info)
                                                <option value="{{ $key }}" {{ old('driver') === $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('driver')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ترتيب العرض</label>
                                        <input type="number" min="0" class="form-control" name="order" value="{{ old('order', 0) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- إعدادات حسب النوع --}}
                        <div id="config-cod" class="config-section card custom-card mb-4" style="display: {{ old('driver') === 'cod' ? 'block' : 'none' }};">
                            <div class="card-header"><div class="card-title">إعدادات الدفع عند الاستلام</div></div>
                            <div class="card-body">
                                <label class="form-label">تعليمات للعميل (اختياري)</label>
                                <textarea name="config[instructions]" class="form-control" rows="3" placeholder="نص يظهر للعميل عند اختيار هذه الوسيلة">{{ old('config.instructions') }}</textarea>
                            </div>
                        </div>
                        <div id="config-bank_transfer" class="config-section card custom-card mb-4" style="display: {{ old('driver') === 'bank_transfer' ? 'block' : 'none' }};">
                            <div class="card-header"><div class="card-title">إعدادات التحويل البنكي / الآيبان</div></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">اسم البنك</label>
                                        <input type="text" class="form-control" name="config[bank_name]" value="{{ old('config.bank_name') }}" placeholder="مثال: البنك الأهلي">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">رقم الآيبان</label>
                                        <input type="text" class="form-control" name="config[iban]" value="{{ old('config.iban') }}" placeholder="SA00 0000 0000 0000 0000 0000" dir="ltr">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">اسم الحساب</label>
                                        <input type="text" class="form-control" name="config[account_name]" value="{{ old('config.account_name') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">تعليمات التحويل للعميل</label>
                                        <textarea name="config[instructions]" class="form-control" rows="3">{{ old('config.instructions') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="config-paypal" class="config-section card custom-card mb-4" style="display: {{ old('driver') === 'paypal' ? 'block' : 'none' }};">
                            <div class="card-header"><div class="card-title">إعدادات باي بال</div></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Client ID</label>
                                        <input type="text" class="form-control" name="config[client_id]" value="{{ old('config.client_id') }}" placeholder="معرف التطبيق من لوحة باي بال">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Client Secret</label>
                                        <input type="password" class="form-control" name="config[client_secret]" value="{{ old('config.client_secret') }}" placeholder="السرّ السري">
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input type="hidden" name="config[sandbox]" value="0">
                                            <input class="form-check-input" type="checkbox" name="config[sandbox]" value="1" id="paypal_sandbox" {{ old('config.sandbox') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="paypal_sandbox">وضع التجربة (Sandbox)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="config-card" class="config-section card custom-card mb-4" style="display: {{ old('driver') === 'card' ? 'block' : 'none' }};">
                            <div class="card-header"><div class="card-title">إعدادات فيزا / ماستركارد (بوابة الدفع)</div></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">البوابة (مثل: stripe, tap)</label>
                                        <input type="text" class="form-control" name="config[gateway]" value="{{ old('config.gateway') }}" placeholder="اسم البوابة">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">المفتاح العام (Public Key)</label>
                                        <input type="text" class="form-control" name="config[public_key]" value="{{ old('config.public_key') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">المفتاح السري (Secret Key)</label>
                                        <input type="password" class="form-control" name="config[secret_key]" value="{{ old('config.secret_key') }}">
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input type="hidden" name="config[sandbox]" value="0">
                                            <input class="form-check-input" type="checkbox" name="config[sandbox]" value="1" id="card_sandbox" {{ old('config.sandbox') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="card_sandbox">وضع التجربة (Sandbox)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card custom-card mb-4">
                            <div class="card-header"><div class="card-title">الحالة</div></div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">وسيلة الدفع نشطة وتظهر للعميل</label>
                                </div>
                            </div>
                        </div>
                        <div class="card custom-card">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-save me-2"></i>
                                    حفظ وسيلة الدفع
                                </button>
                                <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-secondary w-100">إلغاء</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var driverSelect = document.getElementById('driver');
    var sections = document.querySelectorAll('.config-section');
    function toggleConfig() {
        var v = driverSelect ? driverSelect.value : '';
        sections.forEach(function(el) {
            el.style.display = (el.id === 'config-' + v) ? 'block' : 'none';
        });
    }
    if (driverSelect) driverSelect.addEventListener('change', toggleConfig);
    toggleConfig();
});
</script>
@endsection
