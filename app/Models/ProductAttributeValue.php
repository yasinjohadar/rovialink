<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_attribute_id',
        'value',
        'slug',
        'color_hex',
        'image',
        'order',
    ];

    protected $casts = ['order' => 'integer'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($val) {
            if (empty($val->slug)) {
                $val->slug = Str::slug($val->value);
            }
        });
    }

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    public function productVariants()
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'product_variant_attribute_values',
            'product_attribute_value_id',
            'product_variant_id'
        );
    }
}
