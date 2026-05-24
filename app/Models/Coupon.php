<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_order_amount',
        'usage_limit',
        'status',
        'starts_at',
        'expires_at',
        'applicable_to',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $dates = ['starts_at', 'expires_at'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->where(function ($query) {
            $query->where('is_active', true)
                  ->where(function ($subQuery) {
                      $subQuery->whereNull('expires_at')
                              ->orWhere('expires_at', '>', now());
                  });
        });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('is_active', false)
                  ->orWhere('expires_at', '<=', now());
    }

    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }

    public function scopeValidForUser(Builder $query, $userId): Builder
    {
        return $query->where(function ($subQuery) {
            $subQuery->where('usage_count', '<', 10)
                      ->orWhere(function ($innerQuery) {
                          $innerQuery->where('user_id', '!=', $userId)
                          ->orWhereNull('user_id');
                      });
        });
    }

    public function isValidForUser($userId): bool
    {
        return $this->usage_count < 10 || $this->user_id === null;
    }

    public function markAsUsed($userId): void
    {
        $this->increment('usage_count');
        $this->user_id = $userId ?: null;
    }

    public function apply($userId): bool
    {
        if ($this->isValidForUser($userId)) {
            $this->update(['status' => 'used']);
            return true;
        }
        return false;
    }

    public function getFormattedDiscountAttribute(): string
    {
        if ($this->type === 'percentage') {
            return "{$this->value}%";
        } elseif ($this->type === 'fixed_amount') {
            return "{$this->value} $";
        }
        return $this->value;
    }

    public function getDiscountDescriptionAttribute(): string
    {
        $formattedValue = $this->getFormattedDiscountAttribute();

        switch ($this->type) {
            case 'percentage':
                return "خصم {$formattedValue}";
            case 'fixed_amount':
                return "خصم {$formattedValue}";
            case 'buy_x_get_y':
                return "اشتر {$this->value} واحصل على " . sprintf('%s', $this->value / 100) . " مجاني";
        }

        $valueDescription = '';
        if ($this->minimum_order_amount > 0) {
            $valueDescription .= sprintf(" | الحد الأدنى: %s ريال", number_format($this->minimum_order_amount, 2));
        }
        if ($this->usage_limit > 0) {
            $valueDescription .= sprintf(" | الاستخدام الأقصى: %s مرة", $this->usage_limit);
        }

        return trim($valueDescription . ' |');
    }

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_product');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'coupon_category');
    }

    /**
     * Get the cart subtotal that this coupon applies to (based on applicable_to).
     */
    public function getApplicableSubtotal(ShoppingCart $cart): float
    {
        $cart->loadMissing('items.product.category');

        $lines = $cart->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'product' => $item->product,
            'line_total' => (float) $item->line_total,
        ]);

        return $this->getApplicableSubtotalFromLines($lines);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array{product_id: int, product: ?Product, line_total: float}>  $lines
     */
    public function getApplicableSubtotalFromLines($lines): float
    {
        $this->loadMissing('products', 'categories');

        if ($this->applicable_to === 'entire_store') {
            return (float) $lines->sum('line_total');
        }

        if ($this->applicable_to === 'specific_products') {
            $productIds = $this->products->pluck('id')->toArray();
            if (empty($productIds)) {
                return 0.0;
            }

            return (float) $lines
                ->filter(fn ($line) => in_array($line['product_id'], $productIds))
                ->sum('line_total');
        }

        if ($this->applicable_to === 'specific_categories') {
            $categoryIds = $this->categories->pluck('id')->toArray();
            if (empty($categoryIds)) {
                return 0.0;
            }

            return (float) $lines
                ->filter(function ($line) use ($categoryIds) {
                    $product = $line['product'] ?? null;

                    return $product && $product->category_id && in_array($product->category_id, $categoryIds);
                })
                ->sum('line_total');
        }

        return (float) $lines->sum('line_total');
    }

    /**
     * Check if this coupon applies to a given product.
     */
    public function appliesToProduct(Product $product): bool
    {
        if ($this->applicable_to === 'entire_store') {
            return true;
        }
        if ($this->applicable_to === 'specific_products') {
            return $this->products()->where('products.id', $product->id)->exists();
        }
        if ($this->applicable_to === 'specific_categories' && $product->category_id) {
            return $this->categories()->where('categories.id', $product->category_id)->exists();
        }
        return false;
    }
}
