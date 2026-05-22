<?php

namespace App\Http\Requests\Admin;

use App\Models\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:20', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_final' => ['sometimes', 'boolean'],
            'system_role' => ['nullable', 'string', Rule::in([
                '',
                OrderStatus::ROLE_CHECKOUT,
                OrderStatus::ROLE_RETURN_REFUND,
            ])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الحالة مطلوب.',
            'color.regex' => 'لون الحالة يجب أن يكون بصيغة hex مثل #28a745.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('system_role') === '') {
            $this->merge(['system_role' => null]);
        }
    }
}
