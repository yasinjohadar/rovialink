<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
        $categoryId = $this->route('category');

        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($categoryId) {
                    if ($value == $categoryId) {
                        $fail('التصنيف لا يمكن أن يكون أباً لنفسه');
                    }
                },
            ],
            'status' => 'required|in:active,inactive',
            'order' => 'nullable|integer|min:0',
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
            'name.required' => 'اسم التصنيف مطلوب',
            'name.max' => 'اسم التصنيف يجب أن يكون أقل من 255 حرف',
            'slug.unique' => 'الرابط مستخدم بالفعل',
            'slug.max' => 'الرابط يجب أن يكون أقل من 255 حرف',
            'image.image' => 'يجب أن يكون الملف صورة',
            'image.mimes' => 'نوع الصورة غير مدعوم. يجب أن يكون: jpeg, png, jpg, gif',
            'image.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
            'cover_image.image' => 'يجب أن يكون الملف صورة',
            'cover_image.mimes' => 'نوع الصورة غير مدعوم. يجب أن يكون: jpeg, png, jpg, gif',
            'cover_image.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
            'meta_title.max' => 'عنوان SEO يجب أن يكون أقل من 255 حرف',
            'meta_description.max' => 'وصف SEO يجب أن يكون أقل من 500 حرف',
            'meta_keywords.max' => 'الكلمات المفتاحية يجب أن تكون أقل من 500 حرف',
            'parent_id.exists' => 'التصنيف الأب المحدد غير موجود',
            'status.required' => 'حالة التصنيف مطلوبة',
            'status.in' => 'حالة التصنيف غير صحيحة',
            'order.integer' => 'الترتيب يجب أن يكون رقماً',
            'order.min' => 'الترتيب يجب أن يكون أكبر من أو يساوي 0',
        ];
    }
}
