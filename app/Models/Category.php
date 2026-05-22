<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'cover_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'parent_id',
        'status',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get all descendants (recursive).
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to order categories.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * Scope a query to only include root categories (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the full URL for the image.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        $seed = $this->id ?? rand(1, 50);
        return "https://picsum.photos/seed/cat{$seed}/300/300";
    }

    /**
     * Get the full URL for the cover image.
     */
    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return Storage::url($this->cover_image);
        }
        $seed = $this->id ?? rand(1, 50);
        return "https://picsum.photos/seed/catcover{$seed}/800/400";
    }

    /**
     * Get the default image URL for categories.
     */
    public function getDefaultImageUrlAttribute()
    {
        $seed = $this->id ?? rand(1, 50);
        return "https://picsum.photos/seed/cat{$seed}/300/300";
    }

    /**
     * Check if category has children.
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    /**
     * Get the products in this category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * IDs of this category and all nested children (for product filters).
     *
     * @return array<int, int>
     */
    public function selfAndDescendantIds(): array
    {
        $ids = [$this->id];

        foreach ($this->children()->get() as $child) {
            $ids = array_merge($ids, $child->selfAndDescendantIds());
        }

        return array_values(array_unique($ids));
    }

    /**
     * Get the full path of the category (parent > child).
     */
    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }
}
