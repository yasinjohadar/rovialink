@if (session('status'))
    <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
@endif

<form class="auth-form" method="POST" action="{{ route('login') }}" id="loginForm">
    @csrf

    <div class="form-group">
        <label for="email" class="form-label">
            <i class="fas fa-envelope"></i>
            البريد الإلكتروني
        </label>
        <div class="input-wrapper @error('email') is-invalid @enderror">
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email') }}" placeholder="example@email.com" required autofocus autocomplete="username">
            <span class="input-focus-border"></span>
        </div>
        @error('email')
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
                placeholder="••••••••" required autocomplete="current-password">
            <button type="button" class="password-toggle" data-target="password" aria-label="إظهار كلمة المرور">
                <i class="fas fa-eye"></i>
            </button>
            <span class="input-focus-border"></span>
        </div>
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-options">
        <label class="checkbox-wrapper">
            <input type="checkbox" id="remember_me" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <span class="checkmark"></span>
            <span class="checkbox-label">تذكرني</span>
        </label>
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="forgot-link">نسيت كلمة المرور؟</a>
        @endif
    </div>

    @if (config('app.debug'))
        <div class="form-group mb-3">
            <button type="button" class="btn btn-outline-secondary btn-sm w-100" id="btnAdminFill" title="تعبئة بيانات الأدمن للتطوير">
                <i class="fas fa-user-shield me-1"></i> استخدام بيانات الأدمن
            </button>
        </div>
    @endif

    <button type="submit" class="btn btn-auth">
        <span class="btn-text">تسجيل الدخول</span>
        <span class="btn-icon"><i class="fas fa-arrow-left"></i></span>
        <span class="btn-loader"><i class="fas fa-spinner fa-spin"></i></span>
    </button>
</form>
