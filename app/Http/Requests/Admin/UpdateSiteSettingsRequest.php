<?php

namespace App\Http\Requests\Admin;

use App\Services\SiteSettingsService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = SiteSettingsService::validationRules();

        foreach (SiteSettingsService::heroKeys() as $heroKey) {
            unset($rules[$heroKey], $rules['hero_image_file'], $rules['hero_bg_image_file']);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'site_contact_email.email' => 'البريد الإلكتروني للتواصل غير صالح.',
            'site_facebook_url.url' => 'رابط فيسبوك غير صالح.',
            'site_twitter_url.url' => 'رابط تويتر غير صالح.',
            'site_instagram_url.url' => 'رابط انستغرام غير صالح.',
            'site_logo_file.image' => 'يجب أن يكون الشعار صورة.',
            'site_logo_file.max' => 'حجم صورة الشعار يجب ألا يتجاوز 2 ميجابايت.',
            'site_favicon_file.image' => 'يجب أن تكون أيقونة الموقع صورة.',
            'site_favicon_file.max' => 'حجم أيقونة الموقع يجب ألا يتجاوز 512 كيلوبايت.',
            'site_accent_color.regex' => 'لون التمييز يجب أن يكون بصيغة hex مثل #387e99.',
        ];
    }
}
