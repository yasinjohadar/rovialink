<?php

use App\Models\User;
use App\Services\SiteSettingsService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admin can upload site logo and favicon', function () {
    Storage::fake('public');

    $user = User::factory()->create(['is_active' => true]);

    $logo = UploadedFile::fake()->image('logo.png', 120, 120);
    $favicon = UploadedFile::fake()->image('favicon.png', 32, 32);

    $this->actingAs($user)
        ->put(route('admin.site-settings.update'), [
            SiteSettingsService::KEY_SITE_NAME => 'RoviaLink',
            'site_logo_file' => $logo,
            'site_favicon_file' => $favicon,
        ])
        ->assertRedirect(route('admin.site-settings.index'))
        ->assertSessionHas('success');

    app(SiteSettingsService::class)->clearCache();

    expect(site_setting(SiteSettingsService::KEY_SITE_LOGO))->toContain('site-settings/')
        ->and(site_setting(SiteSettingsService::KEY_SITE_FAVICON))->toContain('site-settings/');
});

test('site settings page shows logo preview url after upload', function () {
    Storage::fake('public');

    $user = User::factory()->create(['is_active' => true]);
    $logo = UploadedFile::fake()->image('logo.png');

    $this->actingAs($user)
        ->put(route('admin.site-settings.update'), [
            SiteSettingsService::KEY_SITE_NAME => 'RoviaLink',
            'site_logo_file' => $logo,
        ])
        ->assertRedirect(route('admin.site-settings.index'));

    app(SiteSettingsService::class)->clearCache();

    $logoUrl = site_setting_url(SiteSettingsService::KEY_SITE_LOGO);

    $this->actingAs($user)
        ->get(route('admin.site-settings.index'))
        ->assertOk()
        ->assertSee($logoUrl, false);
});
