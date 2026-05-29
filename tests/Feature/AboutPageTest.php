<?php

use App\Models\User;
use App\Services\SiteSettingsService;

test('about page loads successfully with default content', function () {
    $this->get(route('frontend.about'))
        ->assertOk()
        ->assertSee('من نحن')
        ->assertSee('كيف بدأنا؟')
        ->assertSee('رؤيتنا')
        ->assertSee('رسالتنا')
        ->assertSee('قيمنا')
        ->assertSee('أرقام نفخر بها');
});

test('about page navbar link points to about route', function () {
    $this->get(route('frontend.home'))
        ->assertOk()
        ->assertSee(route('frontend.about'), false);
});

test('about page reflects updated settings from admin', function () {
    $user = User::factory()->create(['is_active' => true]);

    $customTitle = 'قصة ' . site_brand_name();

    $this->actingAs($user)
        ->put(route('admin.homepage.about.update'), [
            SiteSettingsService::KEY_ABOUT_HERO_TITLE => $customTitle,
            SiteSettingsService::KEY_ABOUT_HERO_SUBTITLE => 'وصف مخصص لصفحة من نحن.',
            SiteSettingsService::KEY_ABOUT_STORY_TITLE => 'بدايتنا',
            SiteSettingsService::KEY_ABOUT_STORY_TEXT_1 => 'نص القصة الأول.',
            SiteSettingsService::KEY_ABOUT_STORY_TEXT_2 => 'نص القصة الثاني.',
            SiteSettingsService::KEY_ABOUT_VISION_TITLE => 'رؤيتنا المستقبلية',
            SiteSettingsService::KEY_ABOUT_VISION_TEXT => 'نص الرؤية المخصص.',
            SiteSettingsService::KEY_ABOUT_MISSION_TITLE => 'رسالتنا اليوم',
            SiteSettingsService::KEY_ABOUT_MISSION_TEXT => 'نص الرسالة المخصص.',
            SiteSettingsService::KEY_ABOUT_VALUES => json_encode([
                ['icon' => 'fa-gem', 'title' => 'قيمة تجريبية', 'text' => 'وصف القيمة.'],
            ], JSON_UNESCAPED_UNICODE),
            SiteSettingsService::KEY_ABOUT_STATS => json_encode([
                ['icon' => 'fa-box', 'target' => 99, 'label' => 'منتج'],
            ], JSON_UNESCAPED_UNICODE),
            SiteSettingsService::KEY_ABOUT_CTA_TITLE => 'ابدأ الآن',
            SiteSettingsService::KEY_ABOUT_CTA_TEXT => 'نص CTA مخصص.',
            SiteSettingsService::KEY_ABOUT_CTA_BTN_LABEL => 'تسوق الآن',
            SiteSettingsService::KEY_ABOUT_CTA_BTN_URL => '/shop',
        ])
        ->assertRedirect(route('admin.homepage.about.edit'));

    app(SiteSettingsService::class)->clearCache();

    $this->get(route('frontend.about'))
        ->assertOk()
        ->assertSee($customTitle)
        ->assertSee('وصف مخصص لصفحة من نحن.')
        ->assertSee('بدايتنا')
        ->assertSee('قيمة تجريبية')
        ->assertSee('ابدأ الآن');
});

test('about page is included in sitemap', function () {
    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee(route('frontend.about'), false);
});
