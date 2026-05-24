<?php

namespace App\Services\Payments;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\ShoppingCart;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\TaxService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        protected CartService $cartService,
        protected CouponService $couponService,
        protected TaxService $taxService,
        protected PaymentSettingsService $paymentSettings,
    ) {}

    public function getCheckoutCart(): ShoppingCart
    {
        return $this->cartService->getCart();
    }

    /**
     * @return array{cart: ShoppingCart, subtotal: float, tax: float, discount: float, total: float, shipping: float}
     */
    public function calculateTotals(ShoppingCart $cart, array $address = []): array
    {
        $cart->loadMissing('items.product', 'items.variant');

        $subtotal = (float) $cart->subtotal;
        $taxAddress = [
            'country' => $address['country'] ?? 'SA',
            'state' => $address['state'] ?? null,
            'city' => $address['city'] ?? null,
            'postal_code' => $address['postal_code'] ?? null,
        ];
        $taxResult = $this->taxService->calculateForCart($cart, $taxAddress);
        $tax = (float) ($taxResult['tax_amount'] ?? 0);
        $discount = (float) ($cart->discount_amount ?? 0);
        $shipping = 0.0;
        $total = max(0, $subtotal + $tax + $shipping - $discount);

        return compact('cart', 'subtotal', 'tax', 'discount', 'total', 'shipping');
    }

  /**
     * @param  array<string, mixed>  $input
     */
    public function createOrderFromCart(array $input, PaymentMethod $paymentMethod): Order
    {
        $cart = $this->cartService->getCart();
        if ($cart->items->isEmpty()) {
            throw new \RuntimeException('السلة فارغة.');
        }

        $cart->load('items.product.files', 'items.variant');

        return DB::transaction(function () use ($cart, $input, $paymentMethod) {
            $totals = $this->calculateTotals($cart, [
                'country' => $input['country'] ?? 'SA',
                'city' => $input['city'] ?? null,
                'postal_code' => $input['zip_code'] ?? null,
            ]);

            $coupon = null;
            if ($cart->coupon_code) {
                $result = $this->couponService->calculateDiscount($cart->coupon_code, $cart);
                if ($result['success']) {
                    $coupon = Coupon::where('code', $result['coupon_code'])->first();
                }
            }

            $currency = strtoupper($this->paymentSettings->defaultCurrency());

            $userId = Auth::id();
            if (! $userId) {
                throw new \RuntimeException('يجب تسجيل الدخول لإتمام الطلب.');
            }

            $order = Order::create([
                'user_id' => $userId,
                'order_status_id' => OrderStatus::idForRole(OrderStatus::ROLE_CHECKOUT)
                    ?? OrderStatus::ordered()->value('id'),
                'subtotal' => $totals['subtotal'],
                'shipping_amount' => $totals['shipping'],
                'tax_amount' => $totals['tax'],
                'discount_amount' => $totals['discount'],
                'total' => $totals['total'],
                'coupon_code' => $cart->coupon_code,
                'customer_note' => $input['notes'] ?? $input['customer_note'] ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'currency' => $currency,
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product;
                $variant = $item->variant;
                $unitPrice = $variant ? $variant->effective_price : $product->effective_price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'variant_description' => $variant?->display_name,
                    'sku' => $variant ? $variant->sku : $product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                    'total' => $unitPrice * $item->quantity,
                ]);
            }

            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'billing',
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'phone' => $input['phone'],
                'address_line_1' => $input['address'] ?? 'تسليم رقمي',
                'address_line_2' => $input['email'],
                'city' => $input['city'] ?? '—',
                'postal_code' => $input['zip_code'] ?? null,
                'country' => $input['country'] ?? 'SA',
            ]);

            Payment::create([
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethod->id,
                'amount' => $totals['total'],
                'currency' => $currency,
                'status' => 'pending',
                'metadata' => array_filter([
                    'bank_reference' => $input['bank_reference'] ?? null,
                    'payment_receipt_path' => $input['payment_receipt_path'] ?? null,
                    'payment_receipt_original_name' => $input['payment_receipt_original_name'] ?? null,
                ], fn ($v) => $v !== null && $v !== ''),
            ]);

            if ($coupon && $totals['discount'] > 0) {
                $this->couponService->recordUsage($coupon, $order, $totals['discount']);
            }

            return $order->fresh(['items', 'payments', 'addresses']);
        });
    }

    public function clearCheckoutCart(): void
    {
        $this->cartService->clear();
    }

    public function abortPendingCheckoutOrder(Order $order): void
    {
        if ($order->payments()->where('status', 'completed')->exists()) {
            return;
        }

        DB::transaction(function () use ($order) {
            $order->items()->delete();
            $order->payments()->delete();
            $order->addresses()->delete();
            $order->delete();
        });
    }
}
