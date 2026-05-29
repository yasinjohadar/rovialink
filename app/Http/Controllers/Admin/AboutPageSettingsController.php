<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAboutPageRequest;
use App\Services\ActivityLogger;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AboutPageSettingsController extends Controller
{
    public function __construct(
        protected SiteSettingsService $siteSettings
    ) {
        $this->middleware('auth');
    }

    public function edit(): View
    {
        $settings = $this->siteSettings->all();
        $schema = SiteSettingsService::schemaForSection('about');

        return view('admin.pages.homepage.about', compact('settings', 'schema'));
    }

    public function update(UpdateAboutPageRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->siteSettings->saveAboutSettings(
            $validated,
            $request->file('about_story_image_file'),
        );

        app(ActivityLogger::class)->siteSettingsUpdated(SiteSettingsService::aboutKeys());

        return redirect()
            ->route('admin.homepage.about.edit')
            ->with('success', 'تم حفظ إعدادات صفحة من نحن بنجاح.');
    }
}
