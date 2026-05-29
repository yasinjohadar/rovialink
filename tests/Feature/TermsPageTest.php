<?php

use App\Models\User;
use App\Services\SiteSettingsService;

test('terms page loads successfully with default content', function () {
    $this->get(route('frontend.terms'))
        ->assertOk()
        ->assertSee('الشروط والأحكام')
        ->assertSee('قبول الشروط')
        ->assertSee('الطلبات والمنتجات الرقمية')
        ->assertSee('الملكية الفكرية');
});

test('terms page reflects updated settings from admin', function () {
    $user = User::factory()->create(['is_active' => true]);

    $customTitle = 'شروط الاستخدام';

    $this->actingAs($user)
        ->put(route('admin.homepage.terms.update'), [
            SiteSettingsService::KEY_TERMS_HERO_TITLE => $customTitle,
            SiteSettingsService::KEY_TERMS_HERO_SUBTITLE => 'وصف مخصص للشروط.',
            SiteSettingsService::KEY_TERMS_LAST_UPDATED => '1 يناير 2026',
            SiteSettingsService::KEY_TERMS_INTRO => 'مقدمة مخصصة للشروط.',
            SiteSettingsService::KEY_TERMS_SECTIONS => json_encode([
                [
                    'icon' => 'fa-star',
                    'title' => 'بند تجريبي',
                    'content' => 'محتوى البند التجريبي.',
                ],
            ], JSON_UNESCAPED_UNICODE),
        ])
        ->assertRedirect(route('admin.homepage.terms.edit'));

    app(SiteSettingsService::class)->clearCache();

    $this->get(route('frontend.terms'))
        ->assertOk()
        ->assertSee($customTitle)
        ->assertSee('مقدمة مخصصة للشروط.')
        ->assertSee('بند تجريبي')
        ->assertSee('1 يناير 2026');
});

test('terms page is included in sitemap', function () {
    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee(route('frontend.terms'), false);
});
