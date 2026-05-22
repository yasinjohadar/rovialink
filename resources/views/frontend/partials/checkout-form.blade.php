<form method="POST" action="{{ route('frontend.checkout.store') }}" id="checkout-form">
    @csrf
    <input type="hidden" name="payment_method" value="credit_card">

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
        <h5 class="fw-bold text-white mb-4"><i class="fas fa-user-circle text-accent ms-2"></i> معلومات المشتري</h5>
        <div class="row g-3">
            <div class="col-sm-6">
                <label class="form-label text-secondary small">الاسم الأول</label>
                <input type="text" name="first_name" class="form-control bg-glass text-white border-secondary @error('first_name') is-invalid @enderror"
                       value="{{ old('first_name', auth()->user()->name ? explode(' ', auth()->user()->name)[0] : '') }}" placeholder="أحمد" required>
                @error('first_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-6">
                <label class="form-label text-secondary small">الاسم الأخير</label>
                <input type="text" name="last_name" class="form-control bg-glass text-white border-secondary @error('last_name') is-invalid @enderror"
                       value="{{ old('last_name', '') }}" placeholder="سعيد" required>
                @error('last_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label text-secondary small">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control bg-glass text-white border-secondary @error('email') is-invalid @enderror"
                       value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="ahmed@example.com" required>
                @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label text-secondary small">رقم الهاتف</label>
                <input type="tel" name="phone" class="form-control bg-glass text-white border-secondary @error('phone') is-invalid @enderror"
                       value="{{ old('phone', '') }}" placeholder="+971 50 000 0000" required>
                @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label text-secondary small">المدينة</label>
                <input type="text" name="city" class="form-control bg-glass text-white border-secondary @error('city') is-invalid @enderror"
                       value="{{ old('city', '') }}" placeholder="دبي" required>
                @error('city')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label text-secondary small">الرمز البريدي (اختياري)</label>
                <input type="text" name="zip_code" class="form-control bg-glass text-white border-secondary"
                       value="{{ old('zip_code', '') }}" placeholder="00000">
            </div>
            <input type="hidden" name="address" value="{{ old('address', 'تسليم رقمي') }}">
            <div class="col-12">
                <label class="form-label text-secondary small">ملاحظات الطلب (اختياري)</label>
                <textarea name="notes" class="form-control bg-glass text-white border-secondary" rows="2" placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="glass-panel p-4 mb-4 section-fade-up">
        <h5 class="fw-bold text-white mb-4"><i class="fas fa-credit-card text-accent ms-2"></i> بيانات البطاقة</h5>

        <div class="credit-card-preview mb-4" id="card-preview">
            <div class="card-inner">
                <div class="card-front">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <i class="fas fa-cloud fa-2x text-white opacity-50"></i>
                        <div id="card-type-icon"><i class="fab fa-cc-visa fa-2x text-white opacity-75"></i></div>
                    </div>
                    <div class="card-chip mb-3">
                        <i class="fas fa-microchip fa-2x text-white opacity-50"></i>
                    </div>
                    <div class="card-number-display en-text mb-3" id="card-number-display">•••• •••• •••• ••••</div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div>
                            <div class="text-white-50 small mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">CARD HOLDER</div>
                            <div class="card-holder-display en-text" id="card-holder-display">FULL NAME</div>
                        </div>
                        <div class="text-end">
                            <div class="text-white-50 small mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">EXPIRES</div>
                            <div class="card-exp-display en-text" id="card-exp-display">MM/YY</div>
                        </div>
                    </div>
                </div>
                <div class="card-back">
                    <div class="card-stripe"></div>
                    <div class="card-cvv-strip">
                        <span class="small text-secondary me-2">CVV</span>
                        <div class="cvv-display en-text" id="card-cvv-display">•••</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label text-secondary small">اسم حامل البطاقة</label>
                <input type="text" class="form-control bg-glass text-white border-secondary text-uppercase" id="card-name" placeholder="AHMED SAID" maxlength="26" autocomplete="cc-name">
            </div>
            <div class="col-12">
                <label class="form-label text-secondary small">رقم البطاقة</label>
                <input type="text" class="form-control bg-glass text-white border-secondary en-text" id="card-number" placeholder="1234 5678 9012 3456" maxlength="19" autocomplete="cc-number">
            </div>
            <div class="col-6">
                <label class="form-label text-secondary small">تاريخ الانتهاء</label>
                <input type="text" class="form-control bg-glass text-white border-secondary en-text" id="card-expiry" placeholder="MM/YY" maxlength="5" autocomplete="cc-exp">
            </div>
            <div class="col-6">
                <label class="form-label text-secondary small">رمز CVV</label>
                <input type="password" class="form-control bg-glass text-white border-secondary en-text" id="card-cvv" placeholder="•••" maxlength="4" autocomplete="cc-csc">
            </div>
        </div>

        <div class="d-flex gap-2 flex-wrap mt-4 align-items-center">
            <span class="text-secondary small ms-2">نقبل:</span>
            <i class="fab fa-cc-visa fa-2x text-secondary opacity-75"></i>
            <i class="fab fa-cc-mastercard fa-2x text-secondary opacity-75"></i>
            <i class="fab fa-cc-paypal fa-2x text-secondary opacity-75"></i>
            <i class="fab fa-cc-amex fa-2x text-secondary opacity-75"></i>
        </div>
        <p class="text-secondary small mt-3 mb-0"><i class="fas fa-info-circle text-accent me-1"></i> بيانات البطاقة للعرض فقط — سيتم ربط بوابة الدفع لاحقاً.</p>
    </div>

    <button type="submit" class="btn btn-accent w-100 py-3 fw-bold fs-5 shadow rounded-3 mb-2 section-fade-up" id="submit-order">
        <i class="fas fa-lock ms-2"></i> تأكيد الدفع وإتمام الطلب
    </button>
    <p class="text-center text-secondary small"><i class="fas fa-shield-alt me-1 text-accent"></i> جميع بياناتك محمية ومشفرة 100%</p>
</form>
