<?php

use App\Models\User;
use App\Services\SiteSettingsService;

test('faq page loads successfully with default content', function () {
    $this->get(route('frontend.faq'))
        ->assertOk()
        ->assertSee('الأسئلة الشائعة')
        ->assertSee('الطلبات والدفع')
        ->assertSee('ما طرق الدفع المتاحة؟')
        ->assertSee('لم تجد إجابتك؟');
});

test('faq page reflects updated settings from admin', function () {
    $user = User::factory()->create(['is_active' => true]);

    $customTitle = 'مركز المساعدة';

    $this->actingAs($user)
        ->put(route('admin.homepage.faq.update'), [
            SiteSettingsService::KEY_FAQ_HERO_TITLE => $customTitle,
            SiteSettingsService::KEY_FAQ_HERO_SUBTITLE => 'وصف مخصص للأسئلة الشائعة.',
            SiteSettingsService::KEY_FAQ_GROUPS => json_encode([
                [
                    'title' => 'أسئلة تجريبية',
                    'icon' => 'fa-star',
                    'items' => [
                        ['question' => 'سؤال تجريبي؟', 'answer' => 'إجابة تجريبية.'],
                    ],
                ],
            ], JSON_UNESCAPED_UNICODE),
            SiteSettingsService::KEY_FAQ_CTA_TITLE => 'اسألنا',
            SiteSettingsService::KEY_FAQ_CTA_TEXT => 'نحن هنا للمساعدة.',
            SiteSettingsService::KEY_FAQ_CTA_BTN_LABEL => 'راسلنا',
            SiteSettingsService::KEY_FAQ_CTA_BTN_URL => '/contact',
        ])
        ->assertRedirect(route('admin.homepage.faq.edit'));

    app(SiteSettingsService::class)->clearCache();

    $this->get(route('frontend.faq'))
        ->assertOk()
        ->assertSee($customTitle)
        ->assertSee('سؤال تجريبي؟')
        ->assertSee('إجابة تجريبية.')
        ->assertSee('اسألنا');
});

test('faq page is included in sitemap', function () {
    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee(route('frontend.faq'), false);
});
