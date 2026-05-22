<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaxRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'numeric', 'min:0'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code_pattern' => ['nullable', 'string', 'max:50'],
            'is_compound' => ['sometimes', 'boolean'],
            'is_inclusive' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

