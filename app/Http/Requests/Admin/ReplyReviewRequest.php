<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReplyReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reply_text' => 'required|string|max:1000',
            'status' => 'nullable|in:approved,pending',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reply_text.required' => 'نص الرد مطلوب',
            'reply_text.max' => 'نص الرد يجب أن يكون أقل من 1000 حرف',
            'status.in' => 'حالة الرد غير صحيحة',
        ];
    }
}
