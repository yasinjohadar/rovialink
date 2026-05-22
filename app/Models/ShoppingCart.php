<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'session_id', 'coupon_code', 'discount_amount'];

    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ShoppingCartItem::class, 'shopping_cart_id');
    }

    public function getSubtotalAttribute()
    {
        return $this->items->sum(fn ($item) => $item->line_total);
    }

    public function getTotalAttribute()
    {
        return $this->subtotal - $this->discount_amount;
    }

    public function getItemsCountAttribute()
    {
        return $this->items->sum('quantity');
    }
}
