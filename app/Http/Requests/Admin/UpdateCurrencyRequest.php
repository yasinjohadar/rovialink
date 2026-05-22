<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currency = $this->route('currency');
        return [
            'code' => ['required', 'string', 'max:10', Rule::unique('currencies', 'code')->ignore($currency->id)],
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['nullable', 'string', 'max:20'],
            'rate_to_default' => ['required', 'numeric', 'min:0'],
            'is_default' => ['sometimes', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
