<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function __construct(
        protected CouponService $couponService
    ) {}
    public function getCart(): ShoppingCart
    {
        if (Auth::check()) {
            $cart = ShoppingCart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['user_id' => Auth::id()]
            );
        } else {
            $sessionId = Session::getId();
            $cart = ShoppingCart::firstOrCreate(
                ['session_id' => $sessionId],
                ['session_id' => $sessionId]
            );
        }
        return $cart->load('items.product', 'items.variant');
    }

    public function add(int $productId, int $quantity = 1, ?int $variantId = null): ShoppingCart
    {
        $cart = $this->getCart();
        $product = Product::active()->findOrFail($productId);

        if ($variantId !== null) {
            $variant = ProductVariant::where('id', $variantId)->where('product_id', $productId)->firstOrFail();
        }

        $item = $cart->items()->where('product_id', $productId)->where('product_variant_id', $variantId)->first();
        if ($item) {
            $item->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }

        $this->refreshCouponIfApplied($cart);

        return $this->getCart();
    }

    public function update(int $cartItemId, int $quantity): void
    {
        $cart = $this->getCart();
        $item = $cart->items()->findOrFail($cartItemId);
        if ($quantity <= 0) {
            $item->delete();
            return;
        }
        $item->update(['quantity' => $quantity]);
        $this->refreshCouponIfApplied($cart);
    }

    public function remove(int $cartItemId): void
    {
        $cart = $this->getCart();
        $cart->items()->where('id', $cartItemId)->delete();
        $this->refreshCouponIfApplied($cart);
    }

    public function clear(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
        $cart->update(['coupon_code' => null, 'discount_amount' => 0]);
    }

    /**
     * Apply a coupon to the cart. Returns ['success' => true] or ['success' => false, 'message' => '...'].
     */
    public function applyCoupon(string $code): array
    {
        $cart = $this->getCart();
        $result = $this->couponService->calculateDiscount($code, $cart);
        if (!$result['success']) {
            return $result;
        }
        $cart->update([
            'coupon_code' => $result['coupon_code'],
            'discount_amount' => $result['discount_amount'],
        ]);
        return $result;
    }

    /**
     * Remove applied coupon from the cart.
     */
    public function removeCoupon(): void
    {
        $cart = $this->getCart();
        $cart->update(['coupon_code' => null, 'discount_amount' => 0]);
    }

    protected function refreshCouponIfApplied(ShoppingCart $cart): void
    {
        if (! $cart->coupon_code) {
            return;
        }

        $cart = $cart->fresh()->load('items.product.category');
        $result = $this->couponService->calculateDiscount($cart->coupon_code, $cart);

        if ($result['success']) {
            $cart->update(['discount_amount' => $result['discount_amount']]);

            return;
        }

        $cart->update(['coupon_code' => null, 'discount_amount' => 0]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toViewItems(ShoppingCart $cart): array
    {
        $cart->loadMissing('items.product.primaryImage', 'items.product.images', 'items.variant');

        return $cart->items->map(function ($item) {
            $product = $item->product;

            return [
                'row_id' => (string) $item->id,
                'product_id' => $product->id,
                'variant_id' => $item->product_variant_id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $item->unit_price,
                'quantity' => $item->quantity,
                'image' => $product->primary_image?->path,
                'subtotal' => $item->line_total,
            ];
        })->values()->all();
    }

    public function migrateSessionCart(array $sessionCart): void
    {
        if (empty($sessionCart)) {
            return;
        }

        foreach ($sessionCart as $item) {
            if (empty($item['product_id'])) {
                continue;
            }
            $this->add(
                (int) $item['product_id'],
                (int) ($item['quantity'] ?? 1),
                isset($item['variant_id']) ? (int) $item['variant_id'] : null
            );
        }
    }
}
