<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CouponService;
use App\Services\Seo\SeoBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(
        protected CouponService $couponService
    ) {}

    public function index()
    {
        $cartItems = $this->getCartItems();
        $cartTotal = $this->getCartTotal();

        if (empty($cartItems)) {
            return redirect()->route('frontend.cart.index')->withErrors(['cart' => 'السلة فارغة']);
        }

        $discount = session('discount', 0);
        $shippingCost = 0;
        $seo = SeoBuilder::forPage(
            'إتمام الدفع - إديو ستور',
            'أكمل طلبك بأمان واحصل على منتجاتك الرقمية فوراً.',
            route('frontend.checkout.index')
        );

        return view('frontend.pages.checkout.index', compact('cartItems', 'cartTotal', 'discount', 'shippingCost', 'seo'));
    }

    public function store(Request $request)
    {
        $cartItems = $this->getCartItems();
        $cartTotal = $this->getCartTotal();

        if (empty($cartItems)) {
            return redirect()->route('frontend.cart.index')->withErrors(['cart' => 'السلة فارغة']);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:credit_card,bank_transfer,cod',
        ]);

        $shippingCost = 0;
        $discountAmount = 0;
        $couponCode = null;
        $coupon = null;

        if ($code = session('coupon_code')) {
            $result = $this->couponService->calculateDiscountForSessionCart($code, $this->getCart());
            if (! $result['success']) {
                return back()
                    ->withErrors(['coupon' => $result['message']])
                    ->withInput();
            }
            $discountAmount = $result['discount_amount'];
            $couponCode = $result['coupon_code'];
            $coupon = Coupon::where('code', $couponCode)->first();
        }

        $total = max(0, $cartTotal + $shippingCost - $discountAmount);

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => auth()->id(),
                'subtotal' => $cartTotal,
                'shipping_amount' => $shippingCost,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'coupon_code' => $couponCode,
                'customer_note' => $validated['notes'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'currency' => 'SAR',
            ]);

            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'billing',
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'],
                'address_line_1' => $validated['address'],
                'address_line_2' => $validated['email'],
                'city' => $validated['city'],
                'postal_code' => $validated['zip_code'],
                'country' => 'SA',
            ]);

            foreach ($this->getCart() as $item) {
                $product = Product::find($item['product_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'],
                    'product_name' => $item['name'],
                    'sku' => $product?->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);
            }

            if ($coupon && $discountAmount > 0) {
                $this->couponService->recordUsage($coupon, $order, $discountAmount);
            }

            DB::commit();

            session()->forget(['cart', 'discount', 'coupon_code']);

            return redirect()->route('frontend.account')->with('success', 'تم تأكيد طلبك بنجاح! رقم الطلب: ' . $order->order_number);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'حدث خطأ أثناء معالجة الطلب. يرجى المحاولة مرة أخرى.']);
        }
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
}
