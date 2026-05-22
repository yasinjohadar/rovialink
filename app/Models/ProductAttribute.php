<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'type', 'order', 'is_visible'];

    protected $casts = [
        'order' => 'integer',
        'is_visible' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attr) {
            if (empty($attr->slug)) {
                $attr->slug = Str::slug($attr->name);
            }
        });
    }

    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_attribute_id')->orderBy('order');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}
