<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'string', 'max:50'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in('draft', 'active', 'archived')],
            'is_featured' => ['boolean'],
            'is_visible' => ['boolean'],
            'allow_reviews' => ['boolean'],
            'reviews_require_approval' => ['nullable', 'boolean'],
            'is_digital' => ['boolean'],
            'digital_download_limit' => ['nullable', 'integer', 'min:1'],
            'digital_download_expiry_days' => ['nullable', 'integer', 'min:1'],
            'digital_files' => ['nullable', 'array'],
            'digital_files.*.id' => ['nullable', 'exists:product_files,id'],
            'digital_files.*.title' => ['nullable', 'string', 'max:255'],
            'digital_files.*.file' => ['nullable', 'file', 'max:51200'],
            'digital_files.*.order' => ['nullable', 'integer', 'min:0'],
            'digital_files.*.delete' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'primary_image' => ['nullable', 'image', 'max:5120'],
            'card_image' => ['nullable', 'image', 'max:5120'],
            'images.*' => ['nullable', 'image', 'max:5120'],
            'attribute_ids' => ['nullable', 'array'],
            'attribute_ids.*' => ['exists:product_attributes,id'],
        ];
    }

    protected function prepareForValidation()
    {
        $v = $this->input('reviews_require_approval');
        $reviewsRequireApproval = null;
        if ($v === '1') {
            $reviewsRequireApproval = true;
        } elseif ($v === '0') {
            $reviewsRequireApproval = false;
        }
        // 'default' or any other value => null (use global setting)

        $this->merge([
            'is_digital' => true,
            'is_featured' => $this->boolean('is_featured'),
            'is_visible' => $this->boolean('is_visible'),
            'is_digital' => $this->boolean('is_digital'),
            'allow_reviews' => $this->boolean('allow_reviews'),
            'reviews_require_approval' => $reviewsRequireApproval,
        ]);
    }
}
