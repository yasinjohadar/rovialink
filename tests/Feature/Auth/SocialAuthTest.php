<?php

use App\Models\SocialAccount;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\SiteSettingsService;
use Illuminate\Support\Facades\Crypt;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'user']);
    Role::firstOrCreate(['name' => 'admin']);
});

function enableGoogleAuth(): void
{
    SystemSetting::set(SiteSettingsService::KEY_AUTH_GOOGLE_ENABLED, '1', 'boolean', SiteSettingsService::GROUP);
    SystemSetting::set(SiteSettingsService::KEY_AUTH_GOOGLE_CLIENT_ID, 'google-client-id', 'string', SiteSettingsService::GROUP);
    SystemSetting::set(
        SiteSettingsService::KEY_AUTH_GOOGLE_CLIENT_SECRET,
        Crypt::encryptString('google-client-secret'),
        'string',
        SiteSettingsService::GROUP
    );
    app(SiteSettingsService::class)->clearCache();
}

function mockSocialiteUser(string $id, string $email, string $name = 'Test User'): SocialiteUser
{
    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn($id);
    $abstractUser->shouldReceive('getEmail')->andReturn($email);
    $abstractUser->shouldReceive('getName')->andReturn($name);
    $abstractUser->shouldReceive('getNickname')->andReturn(null);
    $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

    return $abstractUser;
}

function mockSocialiteDriver(string $provider, object $providerMock): void
{
    $factory = Mockery::mock(SocialiteFactory::class);
    $factory->shouldReceive('driver')->with($provider)->andReturn($providerMock);
    app()->instance(SocialiteFactory::class, $factory);
}

it('forbids redirect when google provider is disabled', function () {
    $response = $this->get(route('auth.social.redirect', 'google'));

    $response->assertForbidden();
});

it('redirects to google when provider is enabled', function () {
    enableGoogleAuth();

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));
    mockSocialiteDriver('google', $provider);

    $response = $this->get(route('auth.social.redirect', 'google'));

    $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
});

it('creates a new user and social account on google callback', function () {
    enableGoogleAuth();

    $socialUser = mockSocialiteUser('google-123', 'social-new@example.com', 'Social User');

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('user')->andReturn($socialUser);
    mockSocialiteDriver('google', $provider);

    $response = $this->get(route('auth.social.callback', 'google'));

    $response->assertRedirect(route('frontend.account'));

    $user = User::where('email', 'social-new@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('user'))->toBeTrue();

    $this->assertAuthenticatedAs($user);

    expect(SocialAccount::where('provider', 'google')->where('provider_id', 'google-123')->exists())->toBeTrue();
});

it('links google account to existing customer email', function () {
    enableGoogleAuth();

    $user = User::factory()->create(['email' => 'existing@example.com']);
    $user->assignRole('user');

    $socialUser = mockSocialiteUser('google-456', 'existing@example.com');

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('user')->andReturn($socialUser);
    mockSocialiteDriver('google', $provider);

    $response = $this->get(route('auth.social.callback', 'google'));

    $response->assertRedirect(route('frontend.account'));
    $this->assertAuthenticatedAs($user);
    expect(SocialAccount::where('user_id', $user->id)->where('provider', 'google')->count())->toBe(1);
    expect(User::where('email', 'existing@example.com')->count())->toBe(1);
});

it('rejects linking google to existing admin account without prior social link', function () {
    enableGoogleAuth();

    $admin = User::factory()->create(['email' => 'admin-social@example.com']);
    $admin->assignRole('admin');

    $socialUser = mockSocialiteUser('google-admin', 'admin-social@example.com');

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('user')->andReturn($socialUser);
    mockSocialiteDriver('google', $provider);

    $response = $this->get(route('auth.social.callback', 'google'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
    expect(SocialAccount::count())->toBe(0);
});

it('hides social buttons on login when no provider is enabled', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertDontSee('المتابعة مع Google', false);
    $response->assertDontSee('المتابعة مع Facebook', false);
});

it('shows google button on login when google is enabled', function () {
    enableGoogleAuth();

    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('المتابعة مع Google', false);
});
