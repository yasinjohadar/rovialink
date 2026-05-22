<form class="auth-form" method="POST" action="{{ route('register') }}" id="registerForm">
    @csrf

    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-group">
                <label for="first_name" class="form-label">
                    <i class="fas fa-user"></i>
                    الاسم الأول
                </label>
                <div class="input-wrapper @error('first_name') is-invalid @enderror">
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name"
                        name="first_name" value="{{ old('first_name') }}" placeholder="أحمد" required autocomplete="given-name">
                    <span class="input-focus-border"></span>
                </div>
                @error('first_name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="last_name" class="form-label">
                    <i class="fas fa-user"></i>
                    الاسم الأخير
                </label>
                <div class="input-wrapper @error('last_name') is-invalid @enderror">
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                        name="last_name" value="{{ old('last_name') }}" placeholder="محمد" required autocomplete="family-name">
                    <span class="input-focus-border"></span>
                </div>
                @error('last_name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="email" class="form-label">
            <i class="fas fa-envelope"></i>
            البريد الإلكتروني
        </label>
        <div class="input-wrapper @error('email') is-invalid @enderror">
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email') }}" placeholder="example@email.com" required autocomplete="email">
            <span class="input-focus-border"></span>
        </div>
        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="phone" class="form-label">
            <i class="fas fa-phone"></i>
            رقم الهاتف <span class="text-secondary small">(اختياري)</span>
        </label>
        <div class="input-wrapper phone-input @error('phone') is-invalid @enderror">
            <select class="form-select phone-code" id="phone_code" name="phone_code" aria-label="رمز الدولة">
                @foreach (['+966', '+971', '+965', '+968', '+974', '+973', '+20', '+962'] as $code)
                    <option value="{{ $code }}" @selected(old('phone_code', '+966') === $code)>{{ $code }}</option>
                @endforeach
            </select>
            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                value="{{ old('phone') }}" placeholder="5XXXXXXXX" autocomplete="tel">
            <span class="input-focus-border"></span>
        </div>
        @error('phone')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password" class="form-label">
            <i class="fas fa-lock"></i>
            كلمة المرور
        </label>
        <div class="input-wrapper @error('password') is-invalid @enderror">
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                placeholder="••••••••" required autocomplete="new-password">
            <button type="button" class="password-toggle" data-target="password" aria-label="إظهار كلمة المرور">
                <i class="fas fa-eye"></i>
            </button>
            <span class="input-focus-border"></span>
        </div>
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <div class="password-strength">
            <div class="strength-bar">
                <div class="strength-fill" id="strengthFill"></div>
            </div>
            <span class="strength-text" id="strengthText">قوة كلمة المرور</span>
        </div>
    </div>

    <div class="form-group">
        <label for="password_confirmation" class="form-label">
            <i class="fas fa-lock"></i>
            تأكيد كلمة المرور
        </label>
        <div class="input-wrapper">
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                placeholder="••••••••" required autocomplete="new-password">
            <button type="button" class="password-toggle" data-target="password_confirmation" aria-label="إظهار تأكيد كلمة المرور">
                <i class="fas fa-eye"></i>
            </button>
            <span class="input-focus-border"></span>
        </div>
        <div class="password-match" id="passwordMatch"></div>
    </div>

    <div class="form-group">
        <label class="checkbox-wrapper terms-checkbox">
            <input type="checkbox" id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
            <span class="checkmark"></span>
            <span class="checkbox-label">أوافق على
                <a href="{{ route('frontend.terms') }}" target="_blank" rel="noopener">الشروط والأحكام</a>
                و<a href="{{ route('frontend.privacy') }}" target="_blank" rel="noopener">سياسة الخصوصية</a>
            </span>
        </label>
        @error('terms')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-auth">
        <span class="btn-text">إنشاء الحساب</span>
        <span class="btn-icon"><i class="fas fa-user-plus"></i></span>
        <span class="btn-loader"><i class="fas fa-spinner fa-spin"></i></span>
    </button>
</form>
