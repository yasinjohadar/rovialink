<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\Concerns\FiltersProducts;
use App\Http\Controllers\Frontend\Concerns\RendersShopCatalog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\Seo\SeoBuilder;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use FiltersProducts;
    use RendersShopCatalog;

    public function index()
    {
        $categories = Category::active()->root()->ordered()->withCount([
            'products' => fn ($q) => $q->where('status', 'active')->where('is_visible', true),
        ])->get();

        $seo = SeoBuilder::forPage(
            'متجر إديو ستور - التصنيفات',
            'اختر القسم الذي يناسب احتياجاتك واكتشف أفضل المنتجات في متجر إديو ستور.',
            route('frontend.categories.index')
        );

        return view('frontend.pages.categories.index', compact('categories', 'seo'));
    }

    public function show(Request $request, string $slug)
    {
        $category = Category::where('slug', $slug)->where('status', 'active')->firstOrFail();

        if ($request->has('category')) {
            $requestedCategory = $request->input('category');

            if ($requestedCategory === '' || $requestedCategory === null) {
                $targetUrl = route('frontend.shop.index', $request->except('category'));

                if ($this->wantsCatalogJson($request)) {
                    return $this->catalogRedirectResponse($targetUrl);
                }

                return redirect()->to($targetUrl);
            }

            if ($requestedCategory !== $category->slug) {
                $targetUrl = route('frontend.category.show', [
                    'slug' => $requestedCategory,
                ] + $request->except('category'));

                if ($this->wantsCatalogJson($request)) {
                    return $this->catalogRedirectResponse($targetUrl);
                }

                return redirect()->to($targetUrl);
            }
        }

        $products = $this->productListingQuery($request)
            ->whereIn('category_id', $category->selfAndDescendantIds())
            ->paginate($request->integer('per_page', 12))
            ->withQueryString();

        if ($this->wantsCatalogJson($request)) {
            return $this->catalogJsonResponse($products);
        }

        $categories = Category::active()->ordered()->get();
        $brands = Brand::orderBy('name')->get();
        $maxProductPrice = (int) ceil((float) Product::active()->max('price') ?: 2000);

        $seo = SeoBuilder::forPage(
            $category->name.' - متجر إديو ستور',
            $category->description ?: 'تصفح منتجات قسم '.$category->name,
            route('frontend.category.show', $category->slug)
        );

        return view('frontend.pages.category.show', compact(
            'category',
            'products',
            'categories',
            'brands',
            'maxProductPrice',
            'seo'
        ));
    }
}
