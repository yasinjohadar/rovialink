<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderDownload;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\TaxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CouponService $couponService,
        protected TaxService $taxService
    ) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        if ($cart->items->isEmpty()) {
            return redirect()->route('store.cart.index')->with('error', 'السلة فارغة.');
        }

        return view('store.checkout.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $cart = $this->cartService->getCart();
        if ($cart->items->isEmpty()) {
            return redirect()->route('store.cart.index')->with('error', 'السلة فارغة.');
        }
        $cart->load('items.product.files', 'items.variant');

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'country' => 'nullable|string|size:2',
            'customer_note' => 'nullable|string|max:2000',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = $cart->subtotal;

            $taxAddress = [
                'country' => $request->input('country', 'SA'),
                'state' => null,
                'city' => null,
                'postal_code' => null,
            ];

            $taxResult = $this->taxService->calculateForCart($cart, $taxAddress);
            $tax = $taxResult['tax_amount'] ?? 0;
            $discount = 0;
            $couponCode = null;
            $coupon = null;
            if ($cart->coupon_code) {
                $result = $this->couponService->calculateDiscount($cart->coupon_code, $cart);
                if ($result['success']) {
                    $discount = $result['discount_amount'];
                    $couponCode = $result['coupon_code'];
                    $coupon = Coupon::where('code', $couponCode)->first();
                }
            }
            $total = $subtotal + $tax - $discount;

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_status_id' => OrderStatus::idForRole(OrderStatus::ROLE_CHECKOUT)
                    ?? OrderStatus::ordered()->value('id'),
                'shipping_method_id' => null,
                'subtotal' => $subtotal,
                'shipping_amount' => 0,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'total' => $total,
                'coupon_code' => $couponCode,
                'customer_note' => $request->input('customer_note'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product;
                $variant = $item->variant;
                $unitPrice = $variant ? $variant->effective_price : $product->effective_price;
                $name = $product->name;
                $sku = $variant ? $variant->sku : $product->sku;
                $variantDesc = $variant ? $variant->display_name : null;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $name,
                    'variant_description' => $variantDesc,
                    'sku' => $sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                    'total' => $unitPrice * $item->quantity,
                ]);

                if ($product->is_digital) {
                    $files = $product->files->where('downloadable', true);
                    foreach ($files as $file) {
                        $expiresAt = $product->digital_download_expiry_days
                            ? now()->addDays((int) $product->digital_download_expiry_days)
                            : null;
                        OrderDownload::create([
                            'order_id' => $order->id,
                            'order_item_id' => $orderItem->id,
                            'product_file_id' => $file->id,
                            'download_token' => Str::random(40),
                            'remaining_downloads' => $product->digital_download_limit,
                            'expires_at' => $expiresAt,
                        ]);
                    }
                }
            }

            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'billing',
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'phone' => $request->input('phone'),
                'address_line_1' => 'تسليم رقمي',
                'address_line_2' => $request->input('email'),
                'city' => '—',
                'state' => null,
                'postal_code' => null,
                'country' => $request->input('country', 'SA'),
            ]);

            Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'status' => 'pending',
            ]);

            if ($coupon && $discount > 0) {
                $this->couponService->recordUsage($coupon, $order, $discount);
            }

            $this->cartService->clear();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'حدث خطأ. يرجى المحاولة مرة أخرى.');
        }

        return redirect()->route('store.checkout.success', $order)->with('success', 'تم إنشاء الطلب بنجاح.');
    }

    public function success(Order $order)
    {
        $order->load('items.product', 'items.downloads.file', 'status');

        return view('store.checkout.success', compact('order'));
    }
}
