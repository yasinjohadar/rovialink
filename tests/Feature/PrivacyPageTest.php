<?php

use App\Models\User;
use App\Services\SiteSettingsService;

test('privacy page loads successfully with default content', function () {
    $this->get(route('frontend.privacy'))
        ->assertOk()
        ->assertSee('سياسة الخصوصية')
        ->assertSee('البيانات التي نجمعها')
        ->assertSee('ملفات تعريف الارتباط')
        ->assertSee('حقوقك');
});

test('privacy page reflects updated settings from admin', function () {
    $user = User::factory()->create(['is_active' => true]);

    $customTitle = 'حماية بياناتك';

    $this->actingAs($user)
        ->put(route('admin.homepage.privacy.update'), [
            SiteSettingsService::KEY_PRIVACY_HERO_TITLE => $customTitle,
            SiteSettingsService::KEY_PRIVACY_HERO_SUBTITLE => 'وصف مخصص للخصوصية.',
            SiteSettingsService::KEY_PRIVACY_LAST_UPDATED => '15 مارس 2026',
            SiteSettingsService::KEY_PRIVACY_INTRO => 'مقدمة مخصصة للخصوصية.',
            SiteSettingsService::KEY_PRIVACY_SECTIONS => json_encode([
                [
                    'icon' => 'fa-lock',
                    'title' => 'قسم تجريبي',
                    'content' => 'محتوى القسم التجريبي.',
                ],
            ], JSON_UNESCAPED_UNICODE),
        ])
        ->assertRedirect(route('admin.homepage.privacy.edit'));

    app(SiteSettingsService::class)->clearCache();

    $this->get(route('frontend.privacy'))
        ->assertOk()
        ->assertSee($customTitle)
        ->assertSee('مقدمة مخصصة للخصوصية.')
        ->assertSee('قسم تجريبي')
        ->assertSee('15 مارس 2026');
});

test('privacy page is included in sitemap', function () {
    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee(route('frontend.privacy'), false);
});
