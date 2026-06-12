@php
    use App\Models\SocialAccount;
    use App\Services\Auth\SocialAuthService;
    use App\Services\SiteSettingsService;

    $siteSettings = app(SiteSettingsService::class);
    $socialAuth = app(SocialAuthService::class);

    $googleRedirect = route('auth.social.callback', 'google');
    $facebookRedirect = route('auth.social.callback', 'facebook');

    $googleConfigured = $siteSettings->get(SiteSettingsService::KEY_AUTH_GOOGLE_CLIENT_ID, '') !== ''
        && $siteSettings->isEncryptedConfigured(SiteSettingsService::KEY_AUTH_GOOGLE_CLIENT_SECRET);

    $facebookConfigured = $siteSettings->get(SiteSettingsService::KEY_AUTH_FACEBOOK_CLIENT_ID, '') !== ''
        && $siteSettings->isEncryptedConfigured(SiteSettingsService::KEY_AUTH_FACEBOOK_CLIENT_SECRET);
@endphp

<div class="social-auth-guide mb-4">
    <div class="alert alert-warning border-0 social-auth-guide__alert mb-3">
        <i class="bi bi-shield-exclamation me-2"></i>
        لا تفعّل أي مزود قبل حفظ المفاتيح وإضافة <strong>رابط إعادة التوجيه</strong> في لوحة Google أو Meta ثم اختبار الدخول من صفحة <code>/login</code>.
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="social-auth-provider-card">
                <div class="social-auth-provider-card__head">
                    <span class="social-auth-provider-card__brand social-auth-provider-card__brand--google">
                        <i class="bi bi-google"></i> Google
                    </span>
                    @if($socialAuth->isProviderEnabled(\App\Models\SocialAccount::PROVIDER_GOOGLE))
                        <span class="sys-status-badge sys-status-badge--success">يعمل</span>
                    @elseif($googleConfigured)
                        <span class="sys-status-badge sys-status-badge--warning">جاهز — غير مفعّل</span>
                    @else
                        <span class="sys-status-badge sys-status-badge--muted">ناقص إعدادات</span>
                    @endif
                </div>
                <label class="form-label small fw-semibold">Redirect URI (انسخه إلى Google Cloud)</label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" readonly value="{{ $googleRedirect }}" id="social-auth-google-redirect">
                    <button type="button" class="btn btn-outline-secondary" data-copy-target="social-auth-google-redirect">نسخ</button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="social-auth-provider-card">
                <div class="social-auth-provider-card__head">
                    <span class="social-auth-provider-card__brand social-auth-provider-card__brand--facebook">
                        <i class="bi bi-facebook"></i> Facebook
                    </span>
                    @if($socialAuth->isProviderEnabled(\App\Models\SocialAccount::PROVIDER_FACEBOOK))
                        <span class="sys-status-badge sys-status-badge--success">يعمل</span>
                    @elseif($facebookConfigured)
                        <span class="sys-status-badge sys-status-badge--warning">جاهز — غير مفعّل</span>
                    @else
                        <span class="sys-status-badge sys-status-badge--muted">ناقص إعدادات</span>
                    @endif
                </div>
                <label class="form-label small fw-semibold">Redirect URI (انسخه إلى Meta)</label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" readonly value="{{ $facebookRedirect }}" id="social-auth-facebook-redirect">
                    <button type="button" class="btn btn-outline-secondary" data-copy-target="social-auth-facebook-redirect">نسخ</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="social-auth-steps">
                <h3 class="social-auth-steps__title"><i class="bi bi-google me-1"></i> ربط Google</h3>
                <ol class="social-auth-steps__list mb-0">
                    <li>افتح <a href="https://console.cloud.google.com/apis/credentials" target="_blank" rel="noopener">Google Cloud Console → Credentials</a>.</li>
                    <li>أنشئ <strong>OAuth client ID</strong> من نوع Web application.</li>
                    <li>أضف Redirect URI أعلاه في <strong>Authorized redirect URIs</strong>.</li>
                    <li>في OAuth consent screen فعّل scopes: <code>email</code> و <code>profile</code>.</li>
                    <li>الصق Client ID و Client Secret في الحقول أدناه ثم احفظ وفعّل Google.</li>
                </ol>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="social-auth-steps">
                <h3 class="social-auth-steps__title"><i class="bi bi-facebook me-1"></i> ربط Facebook</h3>
                <ol class="social-auth-steps__list mb-0">
                    <li>افتح <a href="https://developers.facebook.com/apps/" target="_blank" rel="noopener">Meta for Developers</a> وأنشئ تطبيقاً.</li>
                    <li>أضف منتج <strong>Facebook Login</strong> → Settings.</li>
                    <li>أضف Redirect URI أعلاه في <strong>Valid OAuth Redirect URIs</strong>.</li>
                    <li>من App → Settings → Basic انسخ App ID و App Secret.</li>
                    <li>الصق المفاتيح أدناه، احفظ، ثم فعّل Facebook.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.querySelectorAll('[data-copy-target]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const input = document.getElementById(btn.dataset.copyTarget);
                    if (!input) return;
                    navigator.clipboard.writeText(input.value).then(() => {
                        const original = btn.textContent;
                        btn.textContent = 'تم النسخ';
                        setTimeout(() => { btn.textContent = original; }, 1500);
                    });
                });
            });
        </script>
    @endpush
@endonce
