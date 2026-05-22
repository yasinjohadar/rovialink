<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Support\Collection;

class CouponService
{
    /**
     * Validate coupon and calculate discount amount for the given cart.
     * Uses coupon's applicable_to (entire store / specific products / specific categories).
     *
     * @return array{success: bool, message?: string, discount_amount?: float, coupon_code?: string, coupon_id?: int}
     */
    public function calculateDiscount(string $code, ShoppingCart $cart): array
    {
        $cart->loadMissing('items.product.category');

        $lines = $cart->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'product' => $item->product,
            'line_total' => (float) $item->line_total,
        ]);

        return $this->calculateDiscountFromLines($code, $lines);
    }

    /**
     * Validate coupon against a session-based cart (frontend store).
     *
     * @param  array<string, array{product_id: int, price: float|int, quantity: int}>  $sessionCart
     * @return array{success: bool, message?: string, discount_amount?: float, coupon_code?: string, coupon_id?: int}
     */
    public function calculateDiscountForSessionCart(string $code, array $sessionCart): array
    {
        return $this->calculateDiscountFromLines($code, $this->lineItemsFromSessionCart($sessionCart));
    }

    /**
     * @param  array<string, array{product_id: int, price: float|int, quantity: int}>  $sessionCart
     */
    public function lineItemsFromSessionCart(array $sessionCart): Collection
    {
        $lines = collect();

        foreach ($sessionCart as $item) {
            $product = Product::with('category')->find($item['product_id'] ?? null);
            if (! $product) {
                continue;
            }

            $lines->push([
                'product_id' => $product->id,
                'product' => $product,
                'line_total' => (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0),
            ]);
        }

        return $lines;
    }

    /**
     * @param  Collection<int, array{product_id: int, product: ?Product, line_total: float}>  $lines
     * @return array{success: bool, message?: string, discount_amount?: float, coupon_code?: string, coupon_id?: int}
     */
    protected function calculateDiscountFromLines(string $code, Collection $lines): array
    {
        $code = trim($code);
        if ($code === '') {
            return ['success' => false, 'message' => 'يرجى إدخال كود الكوبون.'];
        }

        if ($lines->isEmpty()) {
            return ['success' => false, 'message' => 'السلة فارغة.'];
        }

        $coupon = Coupon::where('code', $code)->first();
        if (! $coupon) {
            return ['success' => false, 'message' => 'كود الكوبون غير صحيح.'];
        }

        if ($coupon->status !== 'active') {
            return ['success' => false, 'message' => 'هذا الكوبون غير نشط.'];
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return ['success' => false, 'message' => 'هذا الكوبون لم يبدأ بعد.'];
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return ['success' => false, 'message' => 'هذا الكوبون منتهي الصلاحية.'];
        }

        if ($coupon->usage_limit !== null && $coupon->usage_limit > 0) {
            $used = $coupon->usages()->count();
            if ($used >= $coupon->usage_limit) {
                return ['success' => false, 'message' => 'تم استنفاد عدد استخدامات هذا الكوبون.'];
            }
        }

        $applicableSubtotal = $coupon->getApplicableSubtotalFromLines($lines);

        if ($applicableSubtotal <= 0) {
            return [
                'success' => false,
                'message' => 'هذا الكوبون لا ينطبق على أي منتج في سلتك الحالية.',
            ];
        }

        $minAmount = $coupon->minimum_order_amount ? (float) $coupon->minimum_order_amount : 0;
        if ($minAmount > 0 && $applicableSubtotal < $minAmount) {
            return [
                'success' => false,
                'message' => 'الحد الأدنى للطلب لهذا الكوبون هو ' . number_format($minAmount, 2) . ' ر.س.',
            ];
        }

        $discountAmount = $this->computeDiscountAmount($coupon, $applicableSubtotal);

        return [
            'success' => true,
            'discount_amount' => round($discountAmount, 2),
            'coupon_code' => $coupon->code,
            'coupon_id' => $coupon->id,
        ];
    }

    public function recordUsage(Coupon $coupon, Order $order, float $discountAmount): void
    {
        if ($discountAmount <= 0) {
            return;
        }

        CouponUsage::create([
            'user_id' => $order->user_id,
            'coupon_id' => $coupon->id,
            'order_number' => $order->order_number,
            'discount_amount' => round($discountAmount, 2),
            'used_at' => now(),
        ]);
    }

    /**
     * Compute discount amount from coupon type and value.
     */
    protected function computeDiscountAmount(Coupon $coupon, float $applicableSubtotal): float
    {
        $value = (float) $coupon->value;

        if ($coupon->type === 'percentage') {
            return $applicableSubtotal * ($value / 100);
        }

        if ($coupon->type === 'fixed_amount') {
            return min($value, $applicableSubtotal);
        }

        if ($coupon->type === 'buy_x_get_y') {
            return min($value, $applicableSubtotal);
        }

        return 0.0;
    }
}
