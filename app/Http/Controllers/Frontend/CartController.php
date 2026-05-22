<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CouponService;
use App\Services\Seo\SeoBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function __construct(
        protected CouponService $couponService
    ) {}

    public function index()
    {
        $cartItems = $this->getCartItems();
        $cartTotal = $this->getCartTotal();
        $discount = session('discount', 0);
        $couponCode = session('coupon_code');

        $seo = SeoBuilder::forPage(
            'سلة المشتريات - إديو ستور',
            'راجع منتجاتك الرقمية وأكمل عملية الشراء بأمان.',
            route('frontend.cart.index')
        );

        return view('frontend.pages.cart.index', compact('cartItems', 'cartTotal', 'discount', 'couponCode', 'seo'));
    }

    public function clear(Request $request)
    {
        session()->forget(['cart', 'discount', 'coupon_code']);

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
        ]);

        $product = Product::active()->findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        if (! $product->in_stock) {
            return $this->cartErrorResponse($request, 'هذا المنتج غير متاح للشراء حالياً', 'quantity');
        }

        $cart = $this->getCart();
        $rowId = $this->generateRowId($product->id, $request->product_variant_id ?? null);

        if (isset($cart[$rowId])) {
            $cart[$rowId]['quantity'] += $quantity;
        } else {
            $cart[$rowId] = [
                'product_id' => $product->id,
                'variant_id' => $request->product_variant_id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->effective_price,
                'quantity' => $quantity,
                'image' => $product->primary_image?->path,
            ];
        }

        session(['cart' => $cart]);
        $this->syncSessionCoupon();

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

        $cart = $this->getCart();

        if (! isset($cart[$id])) {
            return $this->cartErrorResponse($request, 'المنتج غير موجود في السلة', 'cart');
        }

        $product = Product::find($cart[$id]['product_id']);

        if ($product && ! $product->in_stock) {
            return $this->cartErrorResponse($request, 'هذا المنتج غير متاح للشراء حالياً', 'quantity');
        }

        $cart[$id]['quantity'] = $request->quantity;

        if ($cart[$id]['quantity'] <= 0) {
            unset($cart[$id]);
        }

        session(['cart' => $cart]);
        $this->syncSessionCoupon();

        if ($this->wantsCartJson($request)) {
            return $this->cartJsonResponse('تم تحديث السلة');
        }

        return back()->with('success', 'تم تحديث السلة');
    }

    public function destroy(Request $request, $id)
    {
        $cart = $this->getCart();

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session(['cart' => $cart]);
            $this->syncSessionCoupon();
        }

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

        $cart = $this->getCart();
        if (empty($cart)) {
            return $this->cartErrorResponse($request, 'السلة فارغة.', 'coupon');
        }

        $result = $this->couponService->calculateDiscountForSessionCart(
            $request->input('coupon'),
            $cart
        );

        if (! $result['success']) {
            return $this->cartErrorResponse($request, $result['message'], 'coupon');
        }

        session([
            'discount' => $result['discount_amount'],
            'coupon_code' => $result['coupon_code'],
        ]);

        $message = 'تم تطبيق الكوبون بنجاح. الخصم: ' . number_format($result['discount_amount'], 2) . ' ر.س';

        if ($this->wantsCartJson($request)) {
            return $this->cartJsonResponse($message);
        }

        return back()->with('success', $message);
    }

    public function removeCoupon(Request $request)
    {
        session()->forget(['discount', 'coupon_code']);

        if ($this->wantsCartJson($request)) {
            return $this->cartJsonResponse('تم إزالة كود الخصم');
        }

        return back()->with('success', 'تم إزالة كود الخصم');
    }

    protected function syncSessionCoupon(): void
    {
        $code = session('coupon_code');
        if (! $code) {
            return;
        }

        $cart = $this->getCart();
        if (empty($cart)) {
            session()->forget(['discount', 'coupon_code']);

            return;
        }

        $result = $this->couponService->calculateDiscountForSessionCart($code, $cart);
        if ($result['success']) {
            session([
                'discount' => $result['discount_amount'],
                'coupon_code' => $result['coupon_code'],
            ]);

            return;
        }

        session()->forget(['discount', 'coupon_code']);
    }

    protected function getCart()
    {
        return session('cart', []);
    }

    protected function getCartItems()
    {
        $cart = $this->getCart();
        $items = [];

        foreach ($cart as $rowId => $item) {
            $items[] = array_merge($item, [
                'row_id' => $rowId,
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        return $items;
    }

    protected function getCartTotal()
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    protected function generateRowId($productId, $variantId = null)
    {
        return md5($productId . '_' . ($variantId ?? 'null'));
    }

    protected function wantsCartJson(Request $request): bool
    {
        return $request->expectsJson() || $request->ajax();
    }

    protected function cartJsonResponse(string $message, int $status = 200): JsonResponse
    {
        $cartItems = $this->getCartItems();
        $cartTotal = $this->getCartTotal();
        $discount = (float) session('discount', 0);
        $couponCode = session('coupon_code');

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
