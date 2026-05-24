<?php

namespace App\Services\Store;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductCatalogContextBuilder
{
    /**
     * @return array{context_text: string, products: array<int, array<string, mixed>>}
     */
    public function build(string $userMessage, ?string $currentProductSlug = null, int $limit = 8): array
    {
        $products = collect();

        if ($currentProductSlug) {
            $current = Product::query()->active()->where('slug', $currentProductSlug)->first();
            if ($current) {
                $products->push($this->formatProduct($current));
            }
        }

        $searchResults = $this->searchProducts($userMessage, $limit);
        foreach ($searchResults as $product) {
            if ($products->contains(fn ($p) => $p['id'] === $product['id'])) {
                continue;
            }
            $products->push($product);
            if ($products->count() >= $limit) {
                break;
            }
        }

        if ($products->isEmpty()) {
            $fallback = Product::query()
                ->active()
                ->orderByDesc('is_featured')
                ->orderBy('order')
                ->limit($limit)
                ->get();

            foreach ($fallback as $product) {
                $products->push($this->formatProduct($product));
            }
        }

        $lines = $products->map(function (array $p) {
            return "- {$p['name']} | السعر: {$p['price_formatted']} | الرابط: {$p['url']}"
                .($p['excerpt'] ? " | ملخص: {$p['excerpt']}" : '');
        });

        return [
            'context_text' => "كتالوج المنتجات (استخدمه فقط كمصدر للإجابة):\n".$lines->implode("\n"),
            'products' => $products->values()->all(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function searchProducts(string $query, int $limit = 8): array
    {
        $terms = $this->extractTerms($query);

        if ($terms === []) {
            return Product::query()
                ->active()
                ->orderByDesc('is_featured')
                ->limit($limit)
                ->get()
                ->map(fn (Product $p) => $this->formatProduct($p))
                ->all();
        }

        $builder = Product::query()->active();

        $builder->where(function ($q) use ($terms) {
            foreach ($terms as $term) {
                $like = '%'.$term.'%';
                $q->orWhere('name', 'like', $like)
                    ->orWhere('short_description', 'like', $like)
                    ->orWhere('sku', 'like', $like)
                    ->orWhere('meta_keywords', 'like', $like)
                    ->orWhere('meta_title', 'like', $like);
            }
        });

        return $builder
            ->orderByDesc('is_featured')
            ->limit($limit)
            ->get()
            ->map(fn (Product $p) => $this->formatProduct($p))
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatProduct(Product $product): array
    {
        $price = $product->effective_price ?? $product->price;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'url' => route('frontend.product.show', $product->slug),
            'price' => (float) $price,
            'price_formatted' => number_format((float) $price, 2).' $',
            'excerpt' => Str::limit(strip_tags($product->short_description ?: ''), 160),
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function extractTerms(string $query): array
    {
        $query = trim(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $query) ?? '');
        $parts = preg_split('/\s+/u', $query) ?: [];

        return array_values(array_filter($parts, fn (string $w) => mb_strlen($w) >= 2));
    }
}
