@extends('frontend.layouts.auth')

@section('title', 'متجر إديو ستور - تسجيل الدخول')

@section('content')
    @include('frontend.partials.auth.auth-background')

    <div class="auth-container">
        <div class="auth-wrapper">
            @include('frontend.partials.auth.branding-login')

            <div class="auth-form-section">
                <div class="auth-form-wrapper">
                    @include('frontend.partials.auth.auth-form-shell', [
                        'heading' => 'تسجيل الدخول',
                        'subheading' => 'أدخل بياناتك للوصول إلى حسابك',
                    ])

                    @include('frontend.partials.auth.social-login')

                    <div class="auth-divider">
                        <span>أو سجل دخول باستخدام البريد الإلكتروني</span>
                    </div>

                    @include('frontend.partials.auth.login-form')

                    <div class="auth-footer">
                        <p>ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب جديد</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('frontend.partials.auth.scripts-common')
@endpush
