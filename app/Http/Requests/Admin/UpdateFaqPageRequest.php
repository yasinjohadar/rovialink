<?php

namespace App\Http\Requests\Admin;

use App\Services\SiteSettingsService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFaqPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return SiteSettingsService::faqValidationRules();
    }
}
