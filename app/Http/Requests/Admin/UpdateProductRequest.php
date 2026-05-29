<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product');
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product->id)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product->id)],
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
            'remove_card_image' => ['nullable', 'boolean'],
            'images.*' => ['nullable', 'image', 'max:5120'],
            'attribute_ids' => ['nullable', 'array'],
            'attribute_ids.*' => ['exists:product_attributes,id'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'exists:product_variants,id'],
            'variants.*.attribute_value_ids' => ['nullable', 'array'],
            'variants.*.attribute_value_ids.*' => ['exists:product_attribute_values,id'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
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

        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_visible' => $this->boolean('is_visible'),
            'is_digital' => $this->boolean('is_digital'),
            'allow_reviews' => $this->boolean('allow_reviews'),
            'reviews_require_approval' => $reviewsRequireApproval,
            'remove_card_image' => $this->boolean('remove_card_image'),
        ]);
    }
}
