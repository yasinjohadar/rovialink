(function () {
    const cfg = window.CHECKOUT_PHONE || {};
    const input = document.getElementById('checkout-phone-input');
    const hiddenPhone = document.getElementById('checkout-phone-hidden');
    const hiddenCountry = document.getElementById('checkout-country-hidden');
    const form = document.getElementById('checkout-form');
    const wrap = input?.closest('.checkout-phone-wrap');

    if (!input || typeof intlTelInput === 'undefined') {
        return;
    }

    const iti = intlTelInput(input, {
        onlyCountries: cfg.onlyCountries || [],
        initialCountry: (cfg.initialCountry || 'sa').toLowerCase(),
        separateDialCode: true,
        nationalMode: false,
        autoPlaceholder: 'aggressive',
        formatOnDisplay: true,
        utilsScript: cfg.utilsScript || undefined,
    });

    function syncHidden() {
        const data = iti.getSelectedCountryData();
        if (hiddenCountry && data?.iso2) {
            hiddenCountry.value = String(data.iso2).toUpperCase();
        }
        if (hiddenPhone) {
            const number = iti.getNumber();
            hiddenPhone.value = number || '';
        }
    }

    function showPhoneError(message) {
        if (!wrap) {
            return;
        }
        wrap.classList.add('is-invalid');
        let feedback = wrap.parentElement?.querySelector('.checkout-phone-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback d-block checkout-phone-feedback';
            wrap.parentElement?.appendChild(feedback);
        }
        feedback.textContent = message;
    }

    function clearPhoneError() {
        wrap?.classList.remove('is-invalid');
        wrap?.parentElement?.querySelector('.checkout-phone-feedback')?.remove();
    }

    if (cfg.initialNumber) {
        try {
            iti.setNumber(cfg.initialNumber);
        } catch (e) {
            input.value = cfg.initialNumber;
        }
        syncHidden();
    } else {
        syncHidden();
    }

    input.addEventListener('countrychange', function () {
        clearPhoneError();
        syncHidden();
    });

    input.addEventListener('blur', syncHidden);
    input.addEventListener('input', clearPhoneError);

    if (form) {
        form.addEventListener('submit', function (e) {
            syncHidden();

            if (typeof intlTelInputUtils !== 'undefined' && !iti.isValidNumber()) {
                e.preventDefault();
                form.dataset.submitting = '0';
                showPhoneError('رقم الهاتف غير صالح. اختر الدولة وأدخل الرقم بشكل صحيح.');
                input.focus();
                return;
            }

            if (!hiddenPhone?.value || !hiddenPhone.value.startsWith('+')) {
                e.preventDefault();
                form.dataset.submitting = '0';
                showPhoneError('يرجى إدخال رقم هاتف صالح مع رمز الدولة.');
                input.focus();
                return;
            }

            clearPhoneError();
        }, true);
    }
})();
