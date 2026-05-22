<?php

namespace App\Http\Controllers\Frontend\Concerns;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait FiltersProducts
{
    protected function productListingQuery(Request $request): Builder
    {
        $query = Product::query()
            ->with(['category', 'brand', 'images', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->where('status', 'active')
            ->where('is_visible', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('category')) {
            $category = Category::active()->where('slug', $request->category)->first();
            if ($category) {
                $categoryIds = $category->selfAndDescendantIds();
                $query->whereIn('category_id', $categoryIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->brand);
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        switch ($request->input('sort', 'popular')) {
            case 'rating':
                $query->orderByDesc('reviews_avg_rating');
                break;
            case 'newest':
            case 'date':
                $query->orderByDesc('created_at');
                break;
            case 'price-asc':
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-desc':
            case 'price-high':
                $query->orderByDesc('price');
                break;
            case 'popularity':
            case 'popular':
            default:
                $query->orderByDesc('is_featured')->orderByDesc('created_at');
                break;
        }

        return $query;
    }
}
