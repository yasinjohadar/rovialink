@php
    $address = $address ?? null;
    $formKey = $formKey ?? ($address?->id ?? 'new');
    $oldPhoneE164 = old('phone', $address?->phone ?? '');
    $initialPhoneCountry = old(
        'country',
        \App\Support\CheckoutPhoneCountries::guessIso2FromPhone($oldPhoneE164)
            ?? \App\Support\CheckoutPhoneCountries::defaultIso2()
    );
@endphp

<input type="hidden" name="type" value="billing">

<div class="account-address-form">
    <div class="row g-3">
        <div class="col-12">
            <label class="account-field__label" for="addressName{{ $formKey }}">الاسم الكامل</label>
            <div class="account-field">
                <span class="account-field__icon" aria-hidden="true"><i class="fas fa-user"></i></span>
                <input type="text"
                       id="addressName{{ $formKey }}"
                       name="name"
                       class="account-field__input @error('name') is-invalid @enderror"
                       value="{{ old('name', $address?->name) }}"
                       placeholder="الاسم كما يظهر في الفاتورة"
                       autocomplete="name">
            </div>
            @error('name')<div class="account-profile-form__error">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <label class="account-field__label" for="addressPhoneInput{{ $formKey }}">رقم الهاتف</label>
            <input type="hidden"
                   name="phone"
                   id="addressPhoneHidden{{ $formKey }}"
                   value="{{ $oldPhoneE164 }}">
            <input type="hidden"
                   name="country"
                   id="addressCountryHidden{{ $formKey }}"
                   value="{{ $initialPhoneCountry }}">
            <div class="account-address-phone-wrap @error('phone') is-invalid @enderror @error('country') is-invalid @enderror"
                 data-account-address-phone
                 data-form-key="{{ $formKey }}"
                 data-initial-country="{{ strtolower($initialPhoneCountry) }}"
                 data-initial-number="{{ $oldPhoneE164 }}">
                <input type="tel"
                       id="addressPhoneInput{{ $formKey }}"
                       class="account-field__input account-address-phone-input en-text"
                       value="{{ $oldPhoneE164 }}"
                       autocomplete="tel"
                       inputmode="tel"
                       dir="ltr"
                       placeholder="5xxxxxxxx">
            </div>
            @error('phone')<div class="account-profile-form__error">{{ $message }}</div>@enderror
            @error('country')<div class="account-profile-form__error">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <label class="account-field__label" for="addressLine1{{ $formKey }}">العنوان</label>
            <div class="account-field">
                <span class="account-field__icon" aria-hidden="true"><i class="fas fa-map-marker-alt"></i></span>
                <input type="text"
                       id="addressLine1{{ $formKey }}"
                       name="address_line_1"
                       class="account-field__input @error('address_line_1') is-invalid @enderror"
                       value="{{ old('address_line_1', $address?->address_line_1) }}"
                       placeholder="الشارع، الحي، رقم المبنى"
                       required
                       autocomplete="address-line1">
            </div>
            @error('address_line_1')<div class="account-profile-form__error">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <label class="account-field__label" for="addressLine2{{ $formKey }}">تفاصيل إضافية <span class="text-secondary fw-normal">(اختياري)</span></label>
            <div class="account-field">
                <span class="account-field__icon" aria-hidden="true"><i class="fas fa-info-circle"></i></span>
                <input type="text"
                       id="addressLine2{{ $formKey }}"
                       name="address_line_2"
                       class="account-field__input @error('address_line_2') is-invalid @enderror"
                       value="{{ old('address_line_2', $address?->address_line_2) }}"
                       placeholder="شقة، طابق، معلم قريب"
                       autocomplete="address-line2">
            </div>
            @error('address_line_2')<div class="account-profile-form__error">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <label class="account-field__label" for="addressCity{{ $formKey }}">المدينة</label>
            <div class="account-field">
                <span class="account-field__icon" aria-hidden="true"><i class="fas fa-city"></i></span>
                <input type="text"
                       id="addressCity{{ $formKey }}"
                       name="city"
                       class="account-field__input @error('city') is-invalid @enderror"
                       value="{{ old('city', $address?->city) }}"
                       placeholder="الرياض"
                       autocomplete="address-level2">
            </div>
            @error('city')<div class="account-profile-form__error">{{ $message }}</div>@enderror
        </div>
    </div>
</div>
