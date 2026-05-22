<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        return view('store.cart.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'product_variant_id' => 'nullable|exists:product_variants,id',
        ]);
        $this->cartService->add(
            $request->input('product_id'),
            $request->input('quantity', 1),
            $request->input('product_variant_id')
        );
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'تمت الإضافة للسلة']);
        }
        return back()->with('success', 'تمت إضافة المنتج للسلة.');
    }

    public function update(Request $request, int $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:0']);
        $this->cartService->update($itemId, $request->input('quantity'));
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'تم تحديث السلة.');
    }

    public function destroy(int $itemId)
    {
        $this->cartService->remove($itemId);
        return back()->with('success', 'تم حذف المنتج من السلة.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string|max:100']);
        $result = $this->cartService->applyCoupon($request->input('code'));
        if ($result['success']) {
            return back()->with('success', 'تم تطبيق الكوبون بنجاح. الخصم: ' . number_format($result['discount_amount'], 2) . ' ر.س');
        }
        return back()->with('error', $result['message'] ?? 'فشل تطبيق الكوبون.');
    }

    public function removeCoupon()
    {
        $this->cartService->removeCoupon();
        return back()->with('success', 'تم إزالة الكوبون من السلة.');
    }
}
