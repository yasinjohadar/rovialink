<?php

namespace App\Services\Auth;

use App\Models\SocialAccount;
use App\Models\User;
use App\Services\SiteSettingsService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SocialAuthService
{
    public function __construct(
        protected SiteSettingsService $siteSettings,
        protected SocialiteFactory $socialite,
    ) {}

    public function assertProviderAllowed(string $provider): void
    {
        if (! in_array($provider, SocialAccount::PROVIDERS, true)) {
            throw new NotFoundHttpException();
        }

        if (! $this->isProviderEnabled($provider)) {
            abort(403, 'تسجيل الدخول عبر هذا المزود غير متاح حالياً.');
        }
    }

    public function isProviderEnabled(string $provider): bool
    {
        if (! in_array($provider, SocialAccount::PROVIDERS, true)) {
            return false;
        }

        $enabled = match ($provider) {
            SocialAccount::PROVIDER_GOOGLE => (bool) $this->siteSettings->get(
                SiteSettingsService::KEY_AUTH_GOOGLE_ENABLED,
                false
            ),
            SocialAccount::PROVIDER_FACEBOOK => (bool) $this->siteSettings->get(
                SiteSettingsService::KEY_AUTH_FACEBOOK_ENABLED,
                false
            ),
            default => false,
        };

        if (! $enabled) {
            return false;
        }

        return $this->providerCredentialsComplete($provider);
    }

    /** @return list<string> */
    public function enabledProviders(): array
    {
        return array_values(array_filter(
            SocialAccount::PROVIDERS,
            fn (string $provider) => $this->isProviderEnabled($provider)
        ));
    }

    public function hasAnyProviderEnabled(): bool
    {
        return $this->enabledProviders() !== [];
    }

    public function redirectUrl(string $provider): string
    {
        return route('auth.social.callback', ['provider' => $provider]);
    }

    public function configureSocialite(string $provider): void
    {
        $this->assertProviderAllowed($provider);

        $clientId = $this->clientId($provider);
        $clientSecret = $this->clientSecret($provider);
        $redirect = $this->redirectUrl($provider);

        Config::set("services.{$provider}", [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect' => $redirect,
        ]);
    }

    public function socialiteDriver(string $provider)
    {
        $this->configureSocialite($provider);

        return $this->socialite->driver($provider);
    }

    public function handleCallback(string $provider, SocialiteUser $socialUser): User
    {
        $this->assertProviderAllowed($provider);

        $providerId = (string) $socialUser->getId();
        $email = strtolower(trim((string) $socialUser->getEmail()));
        $name = trim((string) ($socialUser->getName() ?: $socialUser->getNickname() ?: 'مستخدم'));
        $avatar = $socialUser->getAvatar();

        if ($email === '') {
            throw new \RuntimeException('لم يتم استلام البريد الإلكتروني من المزود. تأكد من طلب صلاحية البريد في إعدادات التطبيق.');
        }

        return DB::transaction(function () use ($provider, $providerId, $email, $name, $avatar) {
            $account = SocialAccount::query()
                ->where('provider', $provider)
                ->where('provider_id', $providerId)
                ->first();

            if ($account) {
                $user = $account->user;
                $this->updateSocialAccount($account, $avatar);
                $this->assertUserCanLogin($user);

                return $user;
            }

            $user = User::query()->where('email', $email)->first();

            if ($user) {
                if ($user->hasRole('admin') && ! $user->socialAccounts()->where('provider', $provider)->exists()) {
                    throw new \RuntimeException('حساب الإدارة مرتبط بالبريد وكلمة المرور. استخدم تسجيل الدخول بالبريد الإلكتروني.');
                }

                $this->createSocialAccount($user, $provider, $providerId, $avatar);
                $this->assertUserCanLogin($user);

                return $user;
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'photo' => $avatar,
                'email_verified_at' => now(),
                'is_active' => true,
                'status' => 'active',
            ]);

            Role::firstOrCreate(['name' => 'user']);
            $user->assignRole('user');

            $this->createSocialAccount($user, $provider, $providerId, $avatar);
            $this->assertUserCanLogin($user);

            return $user;
        });
    }

    public function assertUserCanLogin(User $user): void
    {
        if (! $user->is_active) {
            throw new \RuntimeException('تم إلغاء تفعيل حسابك. يرجى التواصل مع الإدارة.');
        }
    }

    protected function providerCredentialsComplete(string $provider): bool
    {
        return $this->clientId($provider) !== '' && $this->clientSecret($provider) !== '';
    }

    protected function clientId(string $provider): string
    {
        return match ($provider) {
            SocialAccount::PROVIDER_GOOGLE => (string) $this->siteSettings->get(
                SiteSettingsService::KEY_AUTH_GOOGLE_CLIENT_ID,
                ''
            ),
            SocialAccount::PROVIDER_FACEBOOK => (string) $this->siteSettings->get(
                SiteSettingsService::KEY_AUTH_FACEBOOK_CLIENT_ID,
                ''
            ),
            default => '',
        };
    }

    protected function clientSecret(string $provider): string
    {
        return match ($provider) {
            SocialAccount::PROVIDER_GOOGLE => $this->siteSettings->getDecrypted(
                SiteSettingsService::KEY_AUTH_GOOGLE_CLIENT_SECRET
            ),
            SocialAccount::PROVIDER_FACEBOOK => $this->siteSettings->getDecrypted(
                SiteSettingsService::KEY_AUTH_FACEBOOK_CLIENT_SECRET
            ),
            default => '',
        };
    }

    protected function createSocialAccount(User $user, string $provider, string $providerId, ?string $avatar): SocialAccount
    {
        return SocialAccount::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $providerId,
            'avatar' => $avatar,
        ]);
    }

    protected function updateSocialAccount(SocialAccount $account, ?string $avatar): void
    {
        if ($avatar) {
            $account->update(['avatar' => $avatar]);
        }
    }
}
