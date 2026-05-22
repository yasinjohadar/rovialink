<?php

namespace App\Http\Requests\Admin;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:payment_methods,slug'],
            'driver' => ['required', 'string', Rule::in(PaymentMethod::getDriverSlugs())],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];

        $driver = $this->input('driver');
        foreach ($this->configRulesForDriver($driver) as $key => $driverRules) {
            $rules["config.{$key}"] = $driverRules;
        }

        return $rules;
    }

    protected function configRulesForDriver(?string $driver): array
    {
        if (!$driver) {
            return [];
        }
        $rules = [
            'cod' => [
                'instructions' => ['nullable', 'string', 'max:1000'],
            ],
            'bank_transfer' => [
                'bank_name' => ['nullable', 'string', 'max:255'],
                'iban' => ['nullable', 'string', 'max:50'],
                'account_name' => ['nullable', 'string', 'max:255'],
                'instructions' => ['nullable', 'string', 'max:2000'],
            ],
            'paypal' => [
                'client_id' => ['nullable', 'string', 'max:255'],
                'client_secret' => ['nullable', 'string', 'max:255'],
                'sandbox' => ['sometimes', 'boolean'],
            ],
            'card' => [
                'gateway' => ['nullable', 'string', 'max:100'],
                'public_key' => ['nullable', 'string', 'max:500'],
                'secret_key' => ['nullable', 'string', 'max:500'],
                'sandbox' => ['sometimes', 'boolean'],
            ],
        ];
        return $rules[$driver] ?? [];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم وسيلة الدفع مطلوب.',
            'driver.required' => 'نوع وسيلة الدفع مطلوب.',
            'driver.in' => 'نوع وسيلة الدفع غير صالح.',
        ];
    }
}
