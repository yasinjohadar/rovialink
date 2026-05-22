<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StoreProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category', 'images'])->orderBy('is_featured', 'desc')->orderBy('order')->orderByDesc('created_at');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%")
                    ->orWhere('sku', 'like', "%$q%");
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        $products = $query->paginate(12);
        $categories = Category::active()->ordered()->get();

        return view('store.products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if (!$product->is_visible || $product->status !== 'active') {
            abort(404);
        }
        $product->load([
            'category',
            'images',
            'attributes' => fn ($q) => $q->where('is_visible', true)->with('values'),
            'variants.attributeValues.attribute',
            'reviews' => fn ($q) => $q->approved(),
        ]);
        return view('store.products.show', compact('product'));
    }
}
