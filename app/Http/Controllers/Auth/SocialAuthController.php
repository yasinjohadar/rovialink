<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\SocialAuthService;
use App\Support\AuthRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Two\InvalidStateException;

class SocialAuthController extends Controller
{
    public function __construct(
        protected SocialAuthService $socialAuth
    ) {}

    public function redirect(string $provider): RedirectResponse
    {
        $this->socialAuth->assertProviderAllowed($provider);

        return $this->socialAuth->socialiteDriver($provider)->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        try {
            $this->socialAuth->assertProviderAllowed($provider);

            $socialUser = $this->socialAuth->socialiteDriver($provider)->user();
            $user = $this->socialAuth->handleCallback($provider, $socialUser);

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(AuthRedirect::home($user));
        } catch (InvalidStateException) {
            return redirect()->route('login')->withErrors([
                'email' => 'انتهت صلاحية جلسة تسجيل الدخول الاجتماعي. حاول مرة أخرى.',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Social auth callback failed', [
                'provider' => $provider,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('login')->withErrors([
                'email' => $e->getMessage() ?: 'تعذّر إكمال تسجيل الدخول. حاول لاحقاً.',
            ]);
        }
    }
}
