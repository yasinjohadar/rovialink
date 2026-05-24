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
            <div class="account-sidebar__link payment-method-option {{ old('payment_method_id', $paymentMethods->first()?->id) == $method->id ? 'active' : '' }}"
                 role="button" tabindex="0" data-payment-option>
                <input type="radio" name="payment_method_id" value="{{ $method->id }}" class="d-none payment-method-radio"
                       data-driver="{{ $method->driver }}"
                       data-ui-driver="{{ $method->checkoutUiDriver() }}"
                       @checked(old('payment_method_id', $paymentMethods->first()?->id) == $method->id)>
                <span class="account-sidebar__indicator"></span>
                <i class="fas fa-{{ match($method->checkoutUiDriver()) { 'paypal' => 'paypal', 'bank_transfer' => 'university', 'cod' => 'money-bill-wave', default => 'credit-card' } }} account-sidebar__icon"></i>
                <span class="shop-filters__option-text">{{ $method->name }}</span>
            </div>
            @endforeach
        </div>

        @foreach($paymentMethods->filter(fn ($m) => $m->checkoutUiDriver() === 'cod') as $codMethod)
            @php $cfg = $codMethod->config ?? []; @endphp
            <div class="checkout-method-panel checkout-cod-panel mt-3 d-none"
                 data-checkout-panel="cod" data-method-id="{{ $codMethod->id }}">
                <div class="checkout-method-panel__head">
                    <i class="fas fa-truck-fast"></i>
                    <div>
                        <h6 class="checkout-method-panel__title mb-0">الدفع عند الاستلام</h6>
                        <p class="checkout-method-panel__subtitle mb-0">تعليمات إتمام الطلب</p>
                    </div>
                </div>
                @if(!empty($cfg['instructions']))
                <div class="checkout-method-notice checkout-method-notice--info">{!! nl2br(e($cfg['instructions'])) !!}</div>
                @else
                <p class="checkout-method-panel__text mb-0">سيتواصل معك فريقنا لترتيب الدفع وتسليم منتجاتك.</p>
                @endif
            </div>
        @endforeach

        @foreach($paymentMethods->filter(fn ($m) => $m->checkoutUiDriver() === 'bank_transfer') as $bankMethod)
            @php $cfg = $bankMethod->config ?? []; @endphp
            <div id="bank-transfer-panel-{{ $bankMethod->id }}" class="checkout-bank-panel checkout-method-panel mt-3 d-none"
                 data-checkout-panel="bank_transfer" data-method-id="{{ $bankMethod->id }}">
                <div class="checkout-bank-details mb-3">
                    <h6 class="checkout-bank-details__title"><i class="fas fa-university me-2 text-accent"></i> بيانات الحساب البنكي</h6>

                    @if(!empty($cfg['bank_name']) || !empty($cfg['iban']) || !empty($cfg['account_name']))
                    <div class="checkout-bank-fields mb-3">
                        @if(!empty($cfg['bank_name']))
                        <div class="mb-2">
                            <label class="form-label text-secondary small mb-1">البنك</label>
                            <div class="checkout-bank-field form-control bg-glass border-secondary">{{ $cfg['bank_name'] }}</div>
                        </div>
                        @endif
                        @if(!empty($cfg['iban']))
                        <div class="mb-2">
                            <label class="form-label text-secondary small mb-1">IBAN</label>
                            <div class="checkout-bank-field form-control bg-glass border-secondary en-text" dir="ltr">{{ $cfg['iban'] }}</div>
                        </div>
                        @endif
                        @if(!empty($cfg['account_name']))
                        <div class="mb-0">
                            <label class="form-label text-secondary small mb-1">اسم الحساب</label>
                            <div class="checkout-bank-field form-control bg-glass border-secondary">{{ $cfg['account_name'] }}</div>
                        </div>
                        @endif
                    </div>
                    @endif

                    @php
                        $noticeText = trim((string) ($cfg['customer_notice'] ?? ''));
                        if ($noticeText === '' && !empty($cfg['instructions'])) {
                            $noticeText = trim((string) $cfg['instructions']);
                        }
                    @endphp
                    <div class="checkout-bank-notice-wrap">
                        <label class="form-label text-secondary small mb-1">
                            <i class="fas fa-bell text-accent me-1"></i> ملاحظة مهمة
                        </label>
                        @if($noticeText !== '')
                        <div class="checkout-bank-notice-field form-control bg-glass border-secondary" role="note" aria-readonly="true">{!! nl2br(e($noticeText)) !!}</div>
                        @else
                        <div class="checkout-bank-notice-field checkout-bank-notice-field--empty form-control bg-glass border-secondary" role="note">
                            لم تُضبط بيانات التحويل أو الملاحظة بعد. يمكنك ضبطها من لوحة الإدارة ← وسائل الدفع ← تحويل بنكي.
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">مرجع التحويل (اختياري)</label>
                    <input type="text" name="bank_reference" class="form-control bg-glass border-secondary @error('bank_reference') is-invalid @enderror"
                           value="{{ old('bank_reference') }}" placeholder="رقم العملية البنكية أو المرجع">
                    @error('bank_reference')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="form-label text-secondary small">إيصال التحويل <span class="text-accent">*</span></label>
                    <label class="checkout-file-upload @error('payment_receipt') is-invalid @enderror" for="payment_receipt_{{ $bankMethod->id }}">
                        <input type="file" name="payment_receipt" id="payment_receipt_{{ $bankMethod->id }}"
                               class="checkout-file-upload__input payment-receipt-input"
                               accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf">
                        <span class="btn btn-accent checkout-file-upload__btn">
                            <i class="fas fa-cloud-upload-alt me-1"></i> اختيار ملف
                        </span>
                        <span class="checkout-file-upload__name" data-empty="لم يُختَر أي ملف بعد">لم يُختَر أي ملف بعد</span>
                    </label>
                    <small class="text-secondary d-block mt-2">صورة (JPG, PNG, WEBP) أو ملف PDF — حد أقصى 5 م.ب</small>
                    @error('payment_receipt')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        @endforeach

        @foreach($paymentMethods->filter(fn ($m) => $m->checkoutUiDriver() === 'card') as $cardMethod)
            @php
                $cfg = $cardMethod->config ?? [];
                $gateway = strtolower((string) ($cfg['gateway'] ?? 'stripe'));
                $noticeText = trim((string) ($cfg['customer_notice'] ?? ''));
                $instructions = trim((string) ($cfg['instructions'] ?? ''));
            @endphp
            <div class="checkout-card-panel checkout-method-panel mt-3 d-none"
                 data-checkout-panel="card" data-method-id="{{ $cardMethod->id }}">
                <div class="checkout-method-panel__head">
                    <i class="fas fa-shield-halved"></i>
                    <div>
                        <h6 class="checkout-method-panel__title mb-0">الدفع الآمن بالبطاقة</h6>
                        <p class="checkout-method-panel__subtitle mb-0">Visa · Mastercard · Mada · Amex</p>
                    </div>
                    <div class="checkout-card-brands" aria-hidden="true">
                        <span class="checkout-card-brand checkout-card-brand--visa">VISA</span>
                        <span class="checkout-card-brand checkout-card-brand--mc">MC</span>
                        <span class="checkout-card-brand checkout-card-brand--mada">مدى</span>
                    </div>
                </div>

                <div class="checkout-card-visual">
                    <div class="checkout-card-visual__chip"><i class="fas fa-microchip"></i></div>
                    <div class="checkout-card-visual__number en-text" dir="ltr">•••• &nbsp; •••• &nbsp; •••• &nbsp; ••••</div>
                    <div class="checkout-card-visual__footer">
                        <div>
                            <span class="checkout-card-visual__label">VALID THRU</span>
                            <span class="checkout-card-visual__value en-text">•• / ••</span>
                        </div>
                        <div>
                            <span class="checkout-card-visual__label">CVC</span>
                            <span class="checkout-card-visual__value en-text">•••</span>
                        </div>
                    </div>
                </div>

                <p class="checkout-card-panel__lead">
                    <i class="fas fa-arrow-left-long text-accent me-1"></i>
                    بعد تأكيد الطلب ستُوجَّه إلى صفحة {{ $gateway === 'stripe' ? 'Stripe' : ucfirst($gateway) }} المؤمّنة لإدخال بيانات بطاقتك.
                </p>

                <div class="checkout-card-fields">
                    <div class="mb-3">
                        <label class="form-label text-secondary small mb-1">رقم البطاقة</label>
                        <div class="checkout-card-field form-control bg-glass border-secondary en-text" dir="ltr" tabindex="-1" aria-hidden="true">0000 0000 0000 0000</div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-secondary small mb-1">تاريخ الانتهاء</label>
                            <div class="checkout-card-field form-control bg-glass border-secondary en-text" dir="ltr" tabindex="-1" aria-hidden="true">MM / YY</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-secondary small mb-1">رمز الأمان (CVC)</label>
                            <div class="checkout-card-field form-control bg-glass border-secondary en-text" dir="ltr" tabindex="-1" aria-hidden="true">•••</div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-secondary small mb-1">اسم حامل البطاقة</label>
                        <div class="checkout-card-field form-control bg-glass border-secondary" tabindex="-1" aria-hidden="true">كما يظهر على البطاقة</div>
                    </div>
                </div>

                @if($instructions !== '')
                <p class="checkout-card-panel__hint mb-0 mt-3"><i class="fas fa-circle-info text-accent me-1"></i> {{ $instructions }}</p>
                @endif

                <div class="checkout-card-notice-wrap mt-3">
                    <label class="form-label text-secondary small mb-1">
                        <i class="fas fa-bell text-accent me-1"></i> ملاحظة مهمة
                    </label>
                    @if($noticeText !== '')
                    <div class="checkout-card-notice-field form-control bg-glass border-secondary" role="note" aria-readonly="true">{!! nl2br(e($noticeText)) !!}</div>
                    @else
                    <div class="checkout-card-notice-field checkout-card-notice-field--empty form-control bg-glass border-secondary" role="note">
                        يمكنك كتابة ملاحظة للعميل من لوحة الإدارة ← وسائل الدفع ← {{ $cardMethod->name }} ← «ملاحظة بارزة للعميل».
                    </div>
                    @endif
                </div>

                <ul class="checkout-card-trust">
                    <li><i class="fas fa-lock"></i> تشفير SSL</li>
                    <li><i class="fas fa-user-shield"></i> PCI DSS</li>
                    <li><i class="fas fa-bolt"></i> دفع فوري</li>
                </ul>
            </div>
        @endforeach

        @foreach($paymentMethods->filter(fn ($m) => $m->checkoutUiDriver() === 'paypal') as $paypalMethod)
            @php
                $cfg = $paypalMethod->config ?? [];
                $noticeText = trim((string) ($cfg['customer_notice'] ?? $cfg['instructions'] ?? ''));
            @endphp
            <div class="checkout-method-panel checkout-paypal-panel mt-3 d-none"
                 data-checkout-panel="paypal" data-method-id="{{ $paypalMethod->id }}">
                <div class="checkout-method-panel__head">
                    <i class="fab fa-paypal"></i>
                    <div>
                        <h6 class="checkout-method-panel__title mb-0">الدفع عبر PayPal</h6>
                        <p class="checkout-method-panel__subtitle mb-0">تسجيل دخول PayPal أو بطاقة ضمن حسابك</p>
                    </div>
                </div>
                @if($noticeText !== '')
                <div class="checkout-method-notice checkout-method-notice--info">{!! nl2br(e($noticeText)) !!}</div>
                @endif
                <p class="checkout-method-panel__text mb-0"><i class="fas fa-external-link-alt text-accent me-1"></i> سيتم تحويلك إلى PayPal لإتمام الدفع بأمان.</p>
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
    const form = document.getElementById('checkout-form');
    const radios = document.querySelectorAll('.payment-method-radio');
    const panels = document.querySelectorAll('[data-checkout-panel]');
    const paymentOptions = document.querySelectorAll('[data-payment-option]');

    function selectPaymentOption(option) {
        const radio = option?.querySelector('.payment-method-radio');
        if (!radio || radio.checked) {
            return;
        }
        radio.checked = true;
        radio.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function syncPaymentPanels() {
        const selected = document.querySelector('.payment-method-radio:checked');
        const uiDriver = selected?.dataset.uiDriver || selected?.dataset.driver || '';
        const methodId = selected?.value || '';

        paymentOptions.forEach(option => {
            const radio = option.querySelector('.payment-method-radio');
            option.classList.toggle('active', radio?.checked === true);
        });

        panels.forEach(panel => {
            const show = panel.dataset.checkoutPanel === uiDriver && panel.dataset.methodId === methodId;
            panel.classList.toggle('d-none', !show);

            const fileInput = panel.querySelector('.payment-receipt-input');
            if (fileInput) {
                fileInput.required = show;
                if (!show) {
                    fileInput.value = '';
                    const nameEl = panel.querySelector('.checkout-file-upload__name');
                    if (nameEl) {
                        nameEl.textContent = nameEl.dataset.empty || 'لم يُختَر أي ملف بعد';
                        nameEl.classList.remove('has-file');
                    }
                }
            }
        });
    }

    if (form) {
        form.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter') {
                return;
            }
            const tag = e.target.tagName;
            const type = e.target.type;
            if (tag === 'TEXTAREA' || type === 'submit' || type === 'button') {
                return;
            }
            e.preventDefault();
        });

        form.addEventListener('submit', function (e) {
            if (form.dataset.submitting === '1') {
                e.preventDefault();
                return;
            }
            form.dataset.submitting = '1';
        });
    }

    paymentOptions.forEach(option => {
        option.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            selectPaymentOption(option);
        });

        option.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                selectPaymentOption(option);
            }
        });
    });

    document.querySelectorAll('.payment-receipt-input').forEach(input => {
        input.addEventListener('change', function () {
            const wrap = this.closest('.checkout-file-upload');
            const nameEl = wrap?.querySelector('.checkout-file-upload__name');
            if (!nameEl) return;
            if (this.files && this.files.length > 0) {
                nameEl.textContent = this.files[0].name;
                nameEl.classList.add('has-file');
            } else {
                nameEl.textContent = nameEl.dataset.empty || 'لم يُختَر أي ملف بعد';
                nameEl.classList.remove('has-file');
            }
        });
    });

    radios.forEach(radio => {
        radio.addEventListener('change', syncPaymentPanels);
    });

    syncPaymentPanels();
})();
</script>
@endpush
