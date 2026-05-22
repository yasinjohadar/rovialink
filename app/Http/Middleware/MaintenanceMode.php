<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->isMaintenanceEnabled()) {
            return $next($request);
        }

        if ($this->shouldBypass($request)) {
            return $next($request);
        }

        return response()->view('maintenance', [
            'message' => site_setting(\App\Services\SiteSettingsService::KEY_SITE_MAINTENANCE_MESSAGE, 'الموقع قيد الصيانة. نعود قريباً.'),
        ], 503);
    }

    private function isMaintenanceEnabled(): bool
    {
        try {
            return (bool) site_setting(\App\Services\SiteSettingsService::KEY_SITE_MAINTENANCE_MODE, false);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function shouldBypass(Request $request): bool
    {
        $path = $request->path();

        if ($request->is('admin*') || $request->is('login') || $request->is('logout') || $request->is('register')) {
            return true;
        }

        if ($request->is('_ignition*') || $request->is('sanctum/*') || $request->is('storage/*') || $request->is('assets/*')) {
            return true;
        }

        if ($request->user()?->hasRole('admin')) {
            return true;
        }

        return false;
    }
}
