<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use App\Services\Seo\SeoBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        $cartItems = $this->cartService->toViewItems($cart);
        $cartTotal = (float) $cart->subtotal;
        $discount = (float) ($cart->discount_amount ?? 0);
        $couponCode = $cart->coupon_code;

        $seo = SeoBuilder::forPage(
            'سلة المشتريات - ' . site_brand_name(),
            'راجع منتجاتك الرقمية وأكمل عملية الشراء بأمان.',
            route('frontend.cart.index')
        );

        return view('frontend.pages.cart.index', compact('cartItems', 'cartTotal', 'discount', 'couponCode', 'seo'));
    }

    public function clear(Request $request)
    {
        $this->cartService->clear();

        if ($this->wantsCartJson($request)) {
            return $this->cartJsonResponse('تم إفراغ السلة بنجاح.');
        }

        return redirect()->route('frontend.cart.index')->with('success', 'تم إفراغ السلة بنجاح.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1',
            'product_variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $product = Product::active()->findOrFail($request->product_id);

        if (! $product->in_stock) {
            return $this->cartErrorResponse($request, 'هذا المنتج غير متاح للشراء حالياً', 'quantity');
        }

        $this->cartService->add(
            (int) $request->product_id,
            (int) ($request->quantity ?? 1),
            $request->product_variant_id ? (int) $request->product_variant_id : null
        );

        if ($this->wantsCartJson($request)) {
            return $this->cartJsonResponse('تمت إضافة المنتج إلى السلة');
        }

        return back()->with('success', 'تمت إضافة المنتج إلى السلة');
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);
        } catch (ValidationException $e) {
            return $this->cartValidationResponse($request, $e);
        }

        try {
            $this->cartService->update((int) $id, (int) $request->quantity);
        } catch (\Throwable) {
            return $this->cartErrorResponse($request, 'المنتج غير موجود في السلة', 'cart');
        }

        if ($this->wantsCartJson($request)) {
            return $this->cartJsonResponse('تم تحديث السلة');
        }

        return back()->with('success', 'تم تحديث السلة');
    }

    public function destroy(Request $request, $id)
    {
        $this->cartService->remove((int) $id);

        if ($this->wantsCartJson($request)) {
            return $this->cartJsonResponse('تم إزالة المنتج من السلة');
        }

        return back()->with('success', 'تم إزالة المنتج من السلة');
    }

    public function applyCoupon(Request $request)
    {
        try {
            $request->validate([
                'coupon' => 'required|string|max:50',
            ]);
        } catch (ValidationException $e) {
            return $this->cartValidationResponse($request, $e);
        }

        $cart = $this->cartService->getCart();
        if ($cart->items->isEmpty()) {
            return $this->cartErrorResponse($request, 'السلة فارغة.', 'coupon');
        }

        $result = $this->cartService->applyCoupon($request->input('coupon'));

        if (! $result['success']) {
            return $this->cartErrorResponse($request, $result['message'], 'coupon');
        }

        $message = 'تم تطبيق الكوبون بنجاح. الخصم: ' . number_format($result['discount_amount'], 2) . ' ر.س';

        if ($this->wantsCartJson($request)) {
            return $this->cartJsonResponse($message);
        }

        return back()->with('success', $message);
    }

    public function removeCoupon(Request $request)
    {
        $this->cartService->removeCoupon();

        if ($this->wantsCartJson($request)) {
            return $this->cartJsonResponse('تم إزالة كود الخصم');
        }

        return back()->with('success', 'تم إزالة كود الخصم');
    }

    protected function wantsCartJson(Request $request): bool
    {
        return $request->expectsJson() || $request->ajax();
    }

    protected function cartJsonResponse(string $message, int $status = 200): JsonResponse
    {
        $cart = $this->cartService->getCart();
        $cartItems = $this->cartService->toViewItems($cart);
        $cartTotal = (float) $cart->subtotal;
        $discount = (float) ($cart->discount_amount ?? 0);
        $couponCode = $cart->coupon_code;

        return response()->json([
            'success' => $status < 400,
            'message' => $message,
            'cart_count' => (int) collect($cartItems)->sum('quantity'),
            'cart_total' => $cartTotal,
            'discount' => $discount,
            'coupon_code' => $couponCode,
            'total' => max(0, $cartTotal - $discount),
            'html' => [
                'items' => view('frontend.partials.cart-items', compact('cartItems'))->render(),
                'summary' => view('frontend.partials.cart-summary', [
                    'cartItems' => $cartItems,
                    'cartTotal' => $cartTotal,
                    'discount' => $discount,
                    'couponCode' => $couponCode,
                ])->render(),
            ],
        ], $status);
    }

    protected function cartErrorResponse(Request $request, string $message, string $field = 'cart'): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if ($this->wantsCartJson($request)) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => [$field => [$message]],
            ], 422);
        }

        return back()->withErrors([$field => $message]);
    }

    protected function cartValidationResponse(Request $request, ValidationException $e): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if ($this->wantsCartJson($request)) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }

        throw $e;
    }
}
