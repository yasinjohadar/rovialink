<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductCompareController extends Controller
{
    private const SESSION_KEY = 'admin_product_compare';
    private const MAX_ITEMS = 5;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * عرض صفحة مقارنة المنتجات بناءً على IDs قادمة من الـ query string أو من الـ session.
     */
    public function compare(Request $request): View|RedirectResponse
    {
        $idsFromRequest = (array) $request->input('ids', []);
        $idsFromSession = (array) $request->session()->get(self::SESSION_KEY, []);

        $ids = collect($idsFromRequest)
            ->merge($idsFromSession)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($ids->count() < 2) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'يجب اختيار منتجين على الأقل للمقارنة.');
        }

        if ($ids->count() > self::MAX_ITEMS) {
            $ids = $ids->take(self::MAX_ITEMS);
            $request->session()->flash('warning', 'تم تحديد الحد الأقصى لعدد المنتجات في المقارنة (' . self::MAX_ITEMS . ').');
        }

        $request->session()->put(self::SESSION_KEY, $ids->all());

        $products = Product::with([
            'category',
            'brand',
            'variants.attributeValues.attribute',
            'reviews',
        ])->whereIn('id', $ids)->get();

        if ($products->count() < 2) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'تعذّر تحميل المنتجات المحددة للمقارنة.');
        }

        return view('admin.pages.products.compare', [
            'products' => $products,
            'maxItems' => self::MAX_ITEMS,
        ]);
    }

    /**
     * إضافة منتج إلى قائمة المقارنة في الـ session.
     */
    public function add(Request $request, Product $product): RedirectResponse
    {
        $ids = collect((array) $request->session()->get(self::SESSION_KEY, []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique();

        if ($ids->contains($product->id)) {
            return back()->with('info', 'هذا المنتج موجود بالفعل في قائمة المقارنة.');
        }

        if ($ids->count() >= self::MAX_ITEMS) {
            return back()->with('error', 'لا يمكن إضافة المزيد من المنتجات إلى المقارنة. الحد الأقصى هو ' . self::MAX_ITEMS . ' منتجات.');
        }

        $ids->push($product->id);
        $request->session()->put(self::SESSION_KEY, $ids->all());

        return back()->with('success', 'تمت إضافة المنتج إلى قائمة المقارنة.');
    }

    /**
     * إزالة منتج من قائمة المقارنة في الـ session.
     */
    public function remove(Request $request, Product $product): RedirectResponse
    {
        $ids = collect((array) $request->session()->get(self::SESSION_KEY, []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->reject(fn ($id) => $id === $product->id)
            ->values()
            ->all();

        $request->session()->put(self::SESSION_KEY, $ids);

        return back()->with('success', 'تمت إزالة المنتج من قائمة المقارنة.');
    }

    /**
     * مسح قائمة المقارنة بالكامل.
     */
    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget(self::SESSION_KEY);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'تم مسح قائمة المقارنة.');
    }
}

