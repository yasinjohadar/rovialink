<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::with([
            'category',
            'brand',
            'images',
            'attributes.values',
            'reviews' => fn ($q) => $q->where('status', 'approved')->with('user')->latest(),
        ])
            ->withAvg(['reviews' => fn ($q) => $q->where('status', 'approved')], 'rating')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->where('is_visible', true)
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->with(['category', 'brand', 'images', 'reviews'])
            ->withAvg(['reviews' => fn ($q) => $q->where('status', 'approved')], 'rating')
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->where('is_visible', true)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('frontend.pages.product.show', compact('product', 'relatedProducts'));
    }

    public function quickView(string $slug)
    {
        $product = Product::with(['category', 'brand', 'images', 'reviews'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->where('is_visible', true)
            ->firstOrFail();

        return view('frontend.partials.quick-view-content', compact('product'));
    }

    public function quickViewData(string $slug)
    {
        $product = Product::with(['category', 'brand', 'images'])
            ->withAvg(['reviews' => fn ($q) => $q->where('status', 'approved')], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('status', 'approved')])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->where('is_visible', true)
            ->firstOrFail();

        $images = $product->images
            ->map(fn ($img) => product_image_url($img->path, $product->id))
            ->values()
            ->all();

        if (empty($images)) {
            $images = [$product->primary_image_url];
        }

        $newPrice = (float) $product->price;
        $comparePrice = $product->compare_at_price ? (float) $product->compare_at_price : null;
        $hasDiscount = $comparePrice && $comparePrice > $newPrice;

        $badge = null;
        $badgeType = null;
        if ($product->is_featured) {
            $badge = 'الأكثر مبيعاً';
            $badgeType = 'danger';
        } elseif (! $product->in_stock) {
            $badge = 'غير متاح';
            $badgeType = 'out-of-stock';
        }

        return response()->json([
            'id' => $product->id,
            'slug' => $product->slug,
            'title' => $product->name,
            'categoryName' => $product->category?->name ?? '',
            'brand' => $product->brand?->name ?? '',
            'rating' => round((float) ($product->reviews_avg_rating ?? 0), 1),
            'reviews' => (int) ($product->reviews_count ?? 0),
            'newPrice' => $newPrice,
            'oldPrice' => $hasDiscount ? $comparePrice : $newPrice,
            'hasDiscount' => $hasDiscount,
            'badge' => $badge,
            'badgeType' => $badgeType,
            'stockText' => $product->in_stock ? 'متاح للشراء' : 'غير متاح',
            'inStock' => $product->in_stock,
            'images' => $images,
            'description' => Str::limit(strip_tags($product->short_description ?: $product->description ?: ''), 220),
            'colors' => [],
            'productUrl' => route('frontend.product.show', $product->slug),
        ]);
    }
}
