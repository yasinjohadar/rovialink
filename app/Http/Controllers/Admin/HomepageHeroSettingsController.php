<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomepageHeroRequest;
use App\Services\ActivityLogger;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomepageHeroSettingsController extends Controller
{
    public function __construct(
        protected SiteSettingsService $siteSettings
    ) {
        $this->middleware('auth');
    }

    public function edit(): View
    {
        $settings = $this->siteSettings->all();
        $schema = SiteSettingsService::schemaForSection('homepage');

        return view('admin.pages.homepage.hero', compact('settings', 'schema'));
    }

    public function update(UpdateHomepageHeroRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->siteSettings->saveHeroSettings(
            $validated,
            $request->file('hero_image_file'),
            $request->file('hero_bg_image_file'),
        );

        app(ActivityLogger::class)->siteSettingsUpdated(SiteSettingsService::heroKeys());

        return redirect()
            ->route('admin.homepage.hero.edit')
            ->with('success', 'تم حفظ إعدادات هيرو الصفحة الرئيسية بنجاح.');
    }
}
