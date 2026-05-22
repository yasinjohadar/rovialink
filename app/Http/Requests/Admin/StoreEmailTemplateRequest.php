<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_settings') ?? true;
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:191', 'unique:email_templates,key'],
            'event' => ['required', 'string', 'in:' . implode(',', array_keys(\App\Models\EmailTemplate::events()))],
            'locale' => ['required', 'string', 'max:5'],
            'name' => ['required', 'string', 'max:191'],
            'subject' => ['required', 'string', 'max:191'],
            'body_html' => ['required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string', 'max:191'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}

