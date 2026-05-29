<?php

use App\Models\User;
use App\Services\SiteSettingsService;

test('footer and header display contact info from site settings', function () {
    $user = User::factory()->create(['is_active' => true]);

    $customEmail = 'contact@rovialink.test';
    $customPhone = '+966501112233';
    $customAddress = 'الرياض، المملكة العربية السعودية';
    $customFooterText = 'متجر رقمي موثوق — تسليم فوري لجميع المنتجات.';

    $this->actingAs($user)
        ->put(route('admin.site-settings.update'), [
            SiteSettingsService::KEY_SITE_NAME => site_brand_name(),
            SiteSettingsService::KEY_SITE_CONTACT_EMAIL => $customEmail,
            SiteSettingsService::KEY_SITE_CONTACT_PHONE => $customPhone,
            SiteSettingsService::KEY_SITE_ADDRESS => $customAddress,
            SiteSettingsService::KEY_SITE_FOOTER_TEXT => $customFooterText,
        ])
        ->assertRedirect(route('admin.site-settings.index'));

    app(SiteSettingsService::class)->clearCache();

    $this->get(route('frontend.home'))
        ->assertOk()
        ->assertSee($customEmail, false)
        ->assertSee($customPhone, false)
        ->assertSee($customAddress, false)
        ->assertSee($customFooterText, false);
});
