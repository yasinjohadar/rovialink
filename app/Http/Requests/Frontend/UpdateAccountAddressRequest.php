<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'string', 'in:billing,shipping'],
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^\+[1-9]\d{6,14}$/'],
            'country' => ['nullable', 'string', 'max:2'],
            'city' => ['required', 'string', 'max:255'],
        ];
    }
}
