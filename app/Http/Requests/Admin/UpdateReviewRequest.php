<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
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
            'product_id' => ['required', 'exists:products,id'],
            'user_id' => 'nullable|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected,spam',
            'is_verified_purchase' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $productId = $this->input('product_id');
            if ($productId) {
                $product = Product::find($productId);
                if ($product && !$product->allow_reviews) {
                    $validator->errors()->add('product_id', 'هذا المنتج لا يسمح بالتعليقات والتقييمات.');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'يجب اختيار المنتج.',
            'product_id.exists' => 'المنتج المحدد غير موجود.',
            'user_id.exists' => 'المستخدم المحدد غير موجود',
            'rating.required' => 'التقييم مطلوب',
            'rating.integer' => 'التقييم يجب أن يكون رقماً',
            'rating.min' => 'التقييم يجب أن يكون على الأقل 1',
            'rating.max' => 'التقييم يجب أن يكون على الأكثر 5',
            'status.required' => 'حالة الرأي مطلوبة',
            'status.in' => 'حالة الرأي غير صحيحة',
            'images.*.image' => 'يجب أن يكون الملف صورة',
            'images.*.mimes' => 'نوع الصورة غير مدعوم',
            'images.*.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
        ];
    }
}
