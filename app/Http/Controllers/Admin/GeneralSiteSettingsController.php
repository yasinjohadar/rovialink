<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SiteSettingsService;
use App\Services\ActivityLogger;
use App\Http\Requests\Admin\UpdateSiteSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GeneralSiteSettingsController extends Controller
{
    public function __construct(
        protected SiteSettingsService $siteSettings
    ) {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $settings = $this->siteSettings->all();
        $schema = array_filter(
            SiteSettingsService::schema(),
            fn (array $def) => ($def['section'] ?? '') !== 'homepage'
        );
        $sectionLabels = array_filter(
            SiteSettingsService::sectionLabels(),
            fn (string $label, string $key) => $key !== 'homepage',
            ARRAY_FILTER_USE_BOTH
        );

        return view('admin.pages.site-settings.index', compact('settings', 'schema', 'sectionLabels'));
    }

    public function update(UpdateSiteSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        unset($validated['site_logo_file'], $validated['site_favicon_file']);
        unset(
            $validated[SiteSettingsService::KEY_SITE_LOGO],
            $validated[SiteSettingsService::KEY_SITE_FAVICON]
        );

        $uploadErrors = [];

        if ($request->hasFile('site_logo_file')) {
            if ($this->siteSettings->storeUpload(SiteSettingsService::KEY_SITE_LOGO, $request->file('site_logo_file')) === null) {
                $uploadErrors['site_logo_file'] = 'فشل رفع شعار الموقع. يرجى المحاولة مرة أخرى.';
            }
        }

        if ($request->hasFile('site_favicon_file')) {
            if ($this->siteSettings->storeUpload(SiteSettingsService::KEY_SITE_FAVICON, $request->file('site_favicon_file')) === null) {
                $uploadErrors['site_favicon_file'] = 'فشل رفع أيقونة الموقع. يرجى المحاولة مرة أخرى.';
            }
        }

        if ($uploadErrors !== []) {
            return redirect()
                ->route('admin.site-settings.index')
                ->withErrors($uploadErrors)
                ->withInput();
        }

        foreach (SiteSettingsService::heroKeys() as $heroKey) {
            unset($validated[$heroKey]);
        }

        $this->siteSettings->setMany($validated);

        app(ActivityLogger::class)->siteSettingsUpdated(array_keys($validated));

        return redirect()
            ->route('admin.site-settings.index')
            ->with('success', 'تم حفظ إعدادات الموقع بنجاح.');
    }
}
