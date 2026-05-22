@extends('frontend.layouts.auth')

@section('title', 'متجر إديو ستور - إنشاء حساب جديد')

@section('content')
    @include('frontend.partials.auth.auth-background')

    <div class="auth-container">
        <div class="auth-wrapper">
            @include('frontend.partials.auth.branding-register')

            <div class="auth-form-section">
                <div class="auth-form-wrapper">
                    @include('frontend.partials.auth.auth-form-shell', [
                        'heading' => 'إنشاء حساب جديد',
                        'subheading' => 'أدخل بياناتك لإنشاء حسابك الشخصي',
                    ])

                    @include('frontend.partials.auth.social-login', [
                        'googleLabel' => 'التسجيل مع Google',
                        'facebookLabel' => 'التسجيل مع Facebook',
                    ])

                    <div class="auth-divider">
                        <span>أو سجل باستخدام البريد الإلكتروني</span>
                    </div>

                    @include('frontend.partials.auth.register-form')

                    <div class="auth-footer">
                        <p>لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('frontend.partials.auth.scripts-common')
    @include('frontend.partials.auth.scripts-register')
@endpush
