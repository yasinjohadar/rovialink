(function () {
    const cfg = window.ACCOUNT_ADDRESS_PHONE || {};
    const initialized = new WeakSet();

    function getElements(wrap) {
        const formKey = wrap.dataset.formKey || 'new';
        const form = wrap.closest('form');
        return {
            formKey,
            form,
            input: wrap.querySelector('.account-address-phone-input'),
            hiddenPhone: document.getElementById('addressPhoneHidden' + formKey),
            hiddenCountry: document.getElementById('addressCountryHidden' + formKey),
        };
    }

    function showPhoneError(wrap, message) {
        wrap.classList.add('is-invalid');
        let feedback = wrap.parentElement?.querySelector('.account-address-phone-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback d-block account-address-phone-feedback account-profile-form__error';
            wrap.parentElement?.appendChild(feedback);
        }
        feedback.textContent = message;
    }

    function clearPhoneError(wrap) {
        wrap.classList.remove('is-invalid');
        wrap.parentElement?.querySelector('.account-address-phone-feedback')?.remove();
    }

    function initPhoneWrap(wrap) {
        if (initialized.has(wrap) || typeof intlTelInput === 'undefined') {
            return;
        }

        const { formKey, form, input, hiddenPhone, hiddenCountry } = getElements(wrap);
        if (!input) {
            return;
        }

        const iti = intlTelInput(input, {
            onlyCountries: (cfg.onlyCountries || []).map(function (code) {
                return String(code).toLowerCase();
            }),
            initialCountry: (wrap.dataset.initialCountry || cfg.initialCountry || 'sa').toLowerCase(),
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
                hiddenPhone.value = iti.getNumber() || '';
            }
        }

        const initialNumber = wrap.dataset.initialNumber || '';
        if (initialNumber) {
            try {
                iti.setNumber(initialNumber);
            } catch (e) {
                input.value = initialNumber;
            }
        }
        syncHidden();

        input.addEventListener('countrychange', function () {
            clearPhoneError(wrap);
            syncHidden();
        });
        input.addEventListener('blur', syncHidden);
        input.addEventListener('input', function () {
            clearPhoneError(wrap);
        });

        if (form) {
            form.addEventListener('submit', function (e) {
                syncHidden();

                const phoneValue = hiddenPhone?.value || '';
                if (phoneValue !== '' && typeof intlTelInputUtils !== 'undefined' && !iti.isValidNumber()) {
                    e.preventDefault();
                    showPhoneError(wrap, 'رقم الهاتف غير صالح. اختر الدولة وأدخل الرقم بشكل صحيح.');
                    input.focus();
                    return;
                }

                if (phoneValue !== '' && !phoneValue.startsWith('+')) {
                    e.preventDefault();
                    showPhoneError(wrap, 'يرجى إدخال رقم هاتف صالح مع رمز الدولة.');
                    input.focus();
                    return;
                }

                clearPhoneError(wrap);
            }, true);
        }

        wrap._accountAddressIti = iti;
        initialized.add(wrap);
    }

    function initAllInModal(modal) {
        modal.querySelectorAll('[data-account-address-phone]').forEach(initPhoneWrap);
    }

    document.querySelectorAll('[data-account-address-phone]').forEach(function (wrap) {
        const modal = wrap.closest('.modal');
        if (!modal || modal.classList.contains('show')) {
            initPhoneWrap(wrap);
        }
    });

    document.querySelectorAll('.account-address-modal').forEach(function (modal) {
        modal.addEventListener('shown.bs.modal', function () {
            initAllInModal(modal);
            modal.querySelectorAll('[data-account-address-phone]').forEach(function (wrap) {
                if (wrap._accountAddressIti) {
                    wrap._accountAddressIti.setCountry(
                        (wrap.dataset.initialCountry || cfg.initialCountry || 'sa').toLowerCase()
                    );
                }
            });
        });
    });
})();
