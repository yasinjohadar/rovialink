<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePrivacyPageRequest;
use App\Services\ActivityLogger;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PrivacyPageSettingsController extends Controller
{
    public function __construct(
        protected SiteSettingsService $siteSettings
    ) {
        $this->middleware('auth');
    }

    public function edit(): View
    {
        $settings = $this->siteSettings->all();
        $schema = SiteSettingsService::schemaForSection('privacy');

        return view('admin.pages.homepage.privacy', compact('settings', 'schema'));
    }

    public function update(UpdatePrivacyPageRequest $request): RedirectResponse
    {
        $this->siteSettings->savePrivacySettings($request->validated());

        app(ActivityLogger::class)->siteSettingsUpdated(SiteSettingsService::privacyKeys());

        return redirect()
            ->route('admin.homepage.privacy.edit')
            ->with('success', 'تم حفظ إعدادات سياسة الخصوصية بنجاح.');
    }
}
