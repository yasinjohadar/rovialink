<?php

namespace App\Http\Requests\Admin;

use App\Services\SiteSettingsService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTermsPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return SiteSettingsService::termsValidationRules();
    }
}
