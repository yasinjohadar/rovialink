<?php

namespace App\Support;

use App\Models\User;

class AuthRedirect
{
    /**
     * Default destination after login / registration / email verification.
     */
    public static function home(?User $user = null): string
    {
        $user ??= auth()->user();

        if ($user && $user->hasRole('admin')) {
            return route('admin.dashboard');
        }

        if ($user) {
            return route('frontend.account');
        }

        return route('frontend.home');
    }
}
