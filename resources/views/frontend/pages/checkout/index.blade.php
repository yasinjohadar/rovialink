@extends('frontend.layouts.master')

@section('content')
    @include('frontend.partials.checkout-hero')

    <main class="container py-4">
        <div id="toast-container"></div>

        <div class="row g-5">
            <div class="col-lg-7">
                @include('frontend.partials.checkout-form')
            </div>
            <div class="col-lg-5">
                @include('frontend.partials.checkout-summary')
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    function updateCardPreview() {
        const numEl = document.getElementById('card-number-display');
        const holderEl = document.getElementById('card-holder-display');
        const expEl = document.getElementById('card-exp-display');
        const cvvEl = document.getElementById('card-cvv-display');
        const typeEl = document.getElementById('card-type-icon');
        const num = document.getElementById('card-number')?.value || '';
        const holder = document.getElementById('card-name')?.value || '';
        const exp = document.getElementById('card-expiry')?.value || '';
        const cvv = document.getElementById('card-cvv')?.value || '';
        if (numEl) numEl.textContent = num.padEnd(19, '\u2022').replace(/(.{4})/g, '$1 ').trim() || '\u2022\u2022\u2022\u2022 \u2022\u2022\u2022\u2022 \u2022\u2022\u2022\u2022 \u2022\u2022\u2022\u2022';
        if (holderEl) holderEl.textContent = holder.toUpperCase() || 'FULL NAME';
        if (expEl) expEl.textContent = exp || 'MM/YY';
        if (cvvEl) cvvEl.textContent = cvv ? '\u2022'.repeat(cvv.length) : '\u2022\u2022\u2022';
        if (typeEl) {
            const first = num.replace(/\s/g, '')[0];
            if (first === '4') typeEl.innerHTML = '<i class="fab fa-cc-visa fa-2x text-white opacity-75"></i>';
            else if (first === '5') typeEl.innerHTML = '<i class="fab fa-cc-mastercard fa-2x text-white opacity-75"></i>';
            else if (first === '3') typeEl.innerHTML = '<i class="fab fa-cc-amex fa-2x text-white opacity-75"></i>';
            else typeEl.innerHTML = '<i class="fab fa-cc-visa fa-2x text-white opacity-75"></i>';
        }
    }
    function formatCardNumber(input) {
        let v = input.value.replace(/\D/g, '').substring(0, 16);
        input.value = v.match(/.{1,4}/g)?.join(' ') || v;
        updateCardPreview();
    }
    function formatExpiry(input) {
        let v = input.value.replace(/\D/g, '').substring(0, 4);
        if (v.length > 2) v = v.substring(0, 2) + '/' + v.substring(2);
        input.value = v;
        updateCardPreview();
    }
    function flipCard(show) {
        const preview = document.getElementById('card-preview');
        if (preview) preview.classList.toggle('flipped', show);
    }
    document.getElementById('card-name')?.addEventListener('keyup', updateCardPreview);
    document.getElementById('card-number')?.addEventListener('input', function() { formatCardNumber(this); });
    document.getElementById('card-expiry')?.addEventListener('input', function() { formatExpiry(this); });
    document.getElementById('card-cvv')?.addEventListener('input', updateCardPreview);
    document.getElementById('card-cvv')?.addEventListener('focus', () => flipCard(true));
    document.getElementById('card-cvv')?.addEventListener('blur', () => flipCard(false));
</script>
@endpush
