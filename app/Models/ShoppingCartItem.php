<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCartItem extends Model
{
    use HasFactory;

    protected $fillable = ['shopping_cart_id', 'product_id', 'product_variant_id', 'quantity'];

    protected $casts = ['quantity' => 'integer'];

    public function cart()
    {
        return $this->belongsTo(ShoppingCart::class, 'shopping_cart_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getUnitPriceAttribute()
    {
        if ($this->variant) {
            return $this->variant->effective_price;
        }
        return $this->product->effective_price;
    }

    public function getLineTotalAttribute()
    {
        return $this->unit_price * $this->quantity;
    }
}
