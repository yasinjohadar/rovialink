<form method="POST" action="{{ route('frontend.checkout.store') }}" id="checkout-form" enctype="multipart/form-data">
    @csrf

    @if($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="glass-panel p-4 mb-4 section-fade-up">
        <h5 class="account-panel__title mb-4"><i class="fas fa-user-circle me-2"></i> معلومات المشتري</h5>
        <div class="row g-3">
            <div class="col-sm-6">
                <label class="form-label text-secondary small">الاسم الأول</label>
                <input type="text" name="first_name" class="form-control bg-glass border-secondary @error('first_name') is-invalid @enderror"
                       value="{{ old('first_name', auth()->user()->name ? explode(' ', auth()->user()->name)[0] : '') }}" required>
            </div>
            <div class="col-sm-6">
                <label class="form-label text-secondary small">الاسم الأخير</label>
                <input type="text" name="last_name" class="form-control bg-glass border-secondary @error('last_name') is-invalid @enderror"
                       value="{{ old('last_name', '') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label text-secondary small">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control bg-glass border-secondary @error('email') is-invalid @enderror"
                       value="{{ old('email', auth()->user()->email ?? '') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label text-secondary small">رقم الهاتف</label>
                <input type="tel" name="phone" class="form-control bg-glass border-secondary @error('phone') is-invalid @enderror"
                       value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label text-secondary small">المدينة</label>
                <input type="text" name="city" class="form-control bg-glass border-secondary" value="{{ old('city', 'الرياض') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label text-secondary small">الرمز البريدي (اختياري)</label>
                <input type="text" name="zip_code" class="form-control bg-glass border-secondary" value="{{ old('zip_code') }}">
            </div>
            <input type="hidden" name="address" value="{{ old('address', 'تسليم رقمي') }}">
            <input type="hidden" name="country" value="SA">
            <div class="col-12">
                <label class="form-label text-secondary small">ملاحظات الطلب (اختياري)</label>
                <textarea name="notes" class="form-control bg-glass border-secondary" rows="2">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="glass-panel p-4 mb-4 section-fade-up">
        <h5 class="account-panel__title mb-4"><i class="fas fa-wallet me-2"></i> وسيلة الدفع</h5>
        <div class="d-flex flex-column gap-2">
            @foreach($paymentMethods as $method)
            <label class="account-sidebar__link {{ old('payment_method_id', $paymentMethods->first()?->id) == $method->id ? 'active' : '' }}" style="cursor:pointer;">
                <input type="radio" name="payment_method_id" value="{{ $method->id }}" class="d-none payment-method-radio"
                       data-driver="{{ $method->driver }}"
                       @checked(old('payment_method_id', $paymentMethods->first()?->id) == $method->id)>
                <span class="account-sidebar__indicator"></span>
                <i class="fas fa-{{ match($method->driver) { 'paypal' => 'paypal', 'bank_transfer' => 'university', 'cod' => 'money-bill-wave', default => 'credit-card' } }} account-sidebar__icon"></i>
                <span class="shop-filters__option-text">{{ $method->name }}</span>
            </label>
            @endforeach
        </div>

        @foreach($paymentMethods->where('driver', 'bank_transfer') as $bankMethod)
            @php $cfg = $bankMethod->config ?? []; @endphp
            <div id="bank-transfer-panel-{{ $bankMethod->id }}" class="checkout-bank-panel mt-3 d-none" data-method-id="{{ $bankMethod->id }}">
                @if(!empty($cfg['customer_notice']))
                <div class="checkout-bank-notice mb-3" role="alert">
                    <div class="checkout-bank-notice__icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="checkout-bank-notice__body">
                        <strong class="checkout-bank-notice__title">تنبيه مهم</strong>
                        <p class="mb-0">{!! nl2br(e($cfg['customer_notice'])) !!}</p>
                    </div>
                </div>
                @endif

                <div class="checkout-bank-details mb-3">
                    <h6 class="checkout-bank-details__title"><i class="fas fa-university me-2 text-accent"></i> بيانات الحساب البنكي</h6>
                    @if(!empty($cfg['bank_name']) || !empty($cfg['iban']) || !empty($cfg['account_name']) || !empty($cfg['instructions']))
                    <ul class="checkout-bank-details__list list-unstyled mb-0">
                        @if(!empty($cfg['bank_name']))
                        <li><span class="label">البنك</span><span class="value">{{ $cfg['bank_name'] }}</span></li>
                        @endif
                        @if(!empty($cfg['iban']))
                        <li><span class="label">IBAN</span><span class="value en-text" dir="ltr">{{ $cfg['iban'] }}</span></li>
                        @endif
                        @if(!empty($cfg['account_name']))
                        <li><span class="label">اسم الحساب</span><span class="value">{{ $cfg['account_name'] }}</span></li>
                        @endif
                        @if(!empty($cfg['instructions']))
                        <li class="checkout-bank-details__instructions"><span class="label">تعليمات</span><span class="value">{!! nl2br(e($cfg['instructions'])) !!}</span></li>
                        @endif
                    </ul>
                    @else
                    <p class="text-secondary small mb-0">لم تُضبط بيانات التحويل بعد. تواصل مع الدعم أو اختر وسيلة دفع أخرى.</p>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">مرجع التحويل (اختياري)</label>
                    <input type="text" name="bank_reference" class="form-control bg-glass border-secondary @error('bank_reference') is-invalid @enderror"
                           value="{{ old('bank_reference') }}" placeholder="رقم العملية البنكية أو المرجع">
                    @error('bank_reference')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="form-label text-secondary small">إيصال التحويل <span class="text-accent">*</span></label>
                    <input type="file" name="payment_receipt" id="payment_receipt_{{ $bankMethod->id }}"
                           class="form-control bg-glass border-secondary @error('payment_receipt') is-invalid @enderror"
                           accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf">
                    <small class="text-secondary d-block mt-1">صورة (JPG, PNG, WEBP) أو ملف PDF — حد أقصى 5 م.ب</small>
                    @error('payment_receipt')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-accent w-100 py-3 fw-bold fs-5 rounded-3 mb-2 section-fade-up">
        <i class="fas fa-lock ms-2"></i> تأكيد الطلب والمتابعة للدفع
    </button>
    <p class="text-center text-secondary small"><i class="fas fa-shield-alt me-1 text-accent"></i> جميع بياناتك محمية ومشفرة</p>
</form>

@push('scripts')
<script>
(function () {
    const radios = document.querySelectorAll('.payment-method-radio');
    const bankPanels = document.querySelectorAll('.checkout-bank-panel');

    function syncPaymentPanels() {
        const selected = document.querySelector('.payment-method-radio:checked');
        const driver = selected?.dataset.driver || '';
        const methodId = selected?.value || '';

        bankPanels.forEach(panel => {
            const show = driver === 'bank_transfer' && panel.dataset.methodId === methodId;
            panel.classList.toggle('d-none', !show);
            const fileInput = panel.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.required = show;
                if (!show) fileInput.value = '';
            }
        });
    }

    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('label.account-sidebar__link').forEach(l => l.classList.remove('active'));
            radio.closest('label')?.classList.add('active');
            syncPaymentPanels();
        });
    });

    syncPaymentPanels();
})();
</script>
@endpush
