<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\Concerns\FiltersProducts;
use App\Http\Controllers\Frontend\Concerns\RendersShopCatalog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    use FiltersProducts;
    use RendersShopCatalog;

    public function index(Request $request)
    {
        $products = $this->productListingQuery($request)
            ->paginate($request->integer('per_page', 12))
            ->withQueryString();

        if ($this->wantsCatalogJson($request)) {
            return $this->catalogJsonResponse($products);
        }

        $categories = Category::active()->ordered()->get();
        $brands = Brand::orderBy('name')->get();
        $maxProductPrice = (int) ceil((float) Product::active()->max('price') ?: 2000);

        return view('frontend.pages.shop.index', compact('products', 'categories', 'brands', 'maxProductPrice'));
    }
}
