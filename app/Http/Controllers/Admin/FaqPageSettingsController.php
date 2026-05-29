<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateFaqPageRequest;
use App\Services\ActivityLogger;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FaqPageSettingsController extends Controller
{
    public function __construct(
        protected SiteSettingsService $siteSettings
    ) {
        $this->middleware('auth');
    }

    public function edit(): View
    {
        $settings = $this->siteSettings->all();
        $schema = SiteSettingsService::schemaForSection('faq');

        return view('admin.pages.homepage.faq', compact('settings', 'schema'));
    }

    public function update(UpdateFaqPageRequest $request): RedirectResponse
    {
        $this->siteSettings->saveFaqSettings($request->validated());

        app(ActivityLogger::class)->siteSettingsUpdated(SiteSettingsService::faqKeys());

        return redirect()
            ->route('admin.homepage.faq.edit')
            ->with('success', 'تم حفظ إعدادات الأسئلة الشائعة بنجاح.');
    }
}
