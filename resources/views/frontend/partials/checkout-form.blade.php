<form method="POST" action="{{ route('frontend.checkout.store') }}" id="checkout-form">
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
                       @checked(old('payment_method_id', $paymentMethods->first()?->id) == $method->id)>
                <span class="account-sidebar__indicator"></span>
                <i class="fas fa-{{ match($method->driver) { 'paypal' => 'paypal', 'bank_transfer' => 'university', 'cod' => 'money-bill-wave', default => 'credit-card' } }} account-sidebar__icon"></i>
                <span class="shop-filters__option-text">{{ $method->name }}</span>
            </label>
            @endforeach
        </div>

        <div id="bank-reference-wrap" class="mt-3 d-none">
            <label class="form-label text-secondary small">مرجع التحويل (اختياري)</label>
            <input type="text" name="bank_reference" class="form-control bg-glass border-secondary" value="{{ old('bank_reference') }}" placeholder="رقم العملية البنكية">
        </div>
    </div>

    <button type="submit" class="btn btn-accent w-100 py-3 fw-bold fs-5 rounded-3 mb-2 section-fade-up">
        <i class="fas fa-lock ms-2"></i> تأكيد الطلب والمتابعة للدفع
    </button>
    <p class="text-center text-secondary small"><i class="fas fa-shield-alt me-1 text-accent"></i> جميع بياناتك محمية ومشفرة</p>
</form>

@push('scripts')
<script>
document.querySelectorAll('.payment-method-radio').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('label.account-sidebar__link').forEach(l => l.classList.remove('active'));
        radio.closest('label')?.classList.add('active');
        const slug = radio.closest('label')?.querySelector('.shop-filters__option-text')?.textContent || '';
        const bankWrap = document.getElementById('bank-reference-wrap');
        if (bankWrap) {
            bankWrap.classList.toggle('d-none', !/تحويل|bank/i.test(slug));
        }
    });
});
</script>
@endpush
