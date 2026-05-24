<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'brand_id',
        'tax_class_id',
        'is_digital',
        'digital_download_limit',
        'digital_download_expiry_days',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'compare_at_price',
        'cost',
        'weight',
        'dimensions',
        'barcode',
        'status',
        'is_featured',
        'is_visible',
        'allow_reviews',
        'reviews_require_approval',
        'order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_digital' => 'boolean',
        'digital_download_limit' => 'integer',
        'digital_download_expiry_days' => 'integer',
        'is_featured' => 'boolean',
        'is_visible' => 'boolean',
        'allow_reviews' => 'boolean',
        'reviews_require_approval' => 'boolean',
        'order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function taxClass()
    {
        return $this->belongsTo(TaxClass::class, 'tax_class_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('is_default', 'desc');
    }

    public function attributes()
    {
        return $this->belongsToMany(ProductAttribute::class, 'product_product_attribute')->withPivot([])->orderBy('product_attributes.order');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order')->orderBy('id');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)
            ->orderByDesc('is_primary')
            ->orderBy('order')
            ->orderBy('id');
    }

    public function files()
    {
        return $this->hasMany(ProductFile::class)->orderBy('order')->orderBy('id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_visible', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Whether new reviews for this product require admin approval before being visible.
     * Uses product override if set, otherwise global system setting.
     */
    public function reviewsRequireApproval(): bool
    {
        if ($this->reviews_require_approval !== null) {
            return (bool) $this->reviews_require_approval;
        }
        return SystemSetting::getValue('reviews_require_approval', '1') == '1';
    }

    public function getDefaultVariantAttribute()
    {
        return $this->variants()->where('is_default', true)->first() ?? $this->variants()->first();
    }

    public function getEffectivePriceAttribute()
    {
        $variant = $this->default_variant;
        if ($variant && $variant->price !== null) {
            return $variant->price;
        }
        return $this->price;
    }

    public function getInStockAttribute()
    {
        return $this->status === 'active' && $this->is_visible;
    }

    public function getIsPurchasableAttribute()
    {
        return $this->in_stock;
    }

    public function getPrimaryImageAttribute()
    {
        if ($this->relationLoaded('primaryImage') && $this->getRelation('primaryImage')) {
            return $this->getRelation('primaryImage');
        }

        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
        }

        return $this->images()->where('is_primary', true)->first()
            ?? $this->images()->first();
    }

    public function getPrimaryImageUrlAttribute()
    {
        $img = $this->primary_image;
        if ($img) {
            return product_image_url($img->path, $this->id);
        }
        $seed = $this->id ?? rand(1, 100);
        return "https://picsum.photos/seed/product{$seed}/400/450";
    }

    public function getDefaultImageUrlAttribute()
    {
        $seed = $this->id ?? rand(1, 100);
        return "https://picsum.photos/seed/product{$seed}/400/450";
    }

    public function getHasDiscountAttribute()
    {
        return $this->compare_at_price && $this->compare_at_price > $this->effective_price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->has_discount) {
            return 0;
        }
        return (int) round((1 - ($this->effective_price / $this->compare_at_price)) * 100);
    }

    public function getReviewsAvgRatingAttribute()
    {
        if (!$this->relationLoaded('reviews') || $this->reviews->isEmpty()) {
            return 0;
        }
        return $this->reviews->avg('rating');
    }
}
