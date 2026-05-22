<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaxClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $taxClass = $this->route('tax_class');
        $taxClassId = $taxClass instanceof \App\Models\TaxClass ? $taxClass->id : $taxClass;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('tax_classes', 'slug')->ignore($taxClassId)],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }
}

