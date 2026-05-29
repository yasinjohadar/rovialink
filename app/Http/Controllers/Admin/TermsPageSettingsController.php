<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTermsPageRequest;
use App\Services\ActivityLogger;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TermsPageSettingsController extends Controller
{
    public function __construct(
        protected SiteSettingsService $siteSettings
    ) {
        $this->middleware('auth');
    }

    public function edit(): View
    {
        $settings = $this->siteSettings->all();
        $schema = SiteSettingsService::schemaForSection('terms');

        return view('admin.pages.homepage.terms', compact('settings', 'schema'));
    }

    public function update(UpdateTermsPageRequest $request): RedirectResponse
    {
        $this->siteSettings->saveTermsSettings($request->validated());

        app(ActivityLogger::class)->siteSettingsUpdated(SiteSettingsService::termsKeys());

        return redirect()
            ->route('admin.homepage.terms.edit')
            ->with('success', 'تم حفظ إعدادات الشروط والأحكام بنجاح.');
    }
}
