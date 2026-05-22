<?php

namespace App\Services;

use App\Models\LoyaltyPointTransaction;
use App\Models\Order;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    public const SETTING_POINTS_PER_CURRENCY = 'loyalty_points_per_currency';
    public const SETTING_REDEMPTION_RATE = 'loyalty_redemption_rate';
    public const SETTING_MIN_ORDER_TO_REDEEM = 'loyalty_min_order_to_redeem';
    public const SETTING_MAX_POINTS_PER_ORDER = 'loyalty_max_points_per_order';
    public const SETTING_AWARD_ON_STATUS = 'loyalty_award_on_status';

    /**
     * Get loyalty settings from SystemSetting (group: loyalty).
     *
     * @return array<string, mixed>
     */
    public function getSettings(): array
    {
        return [
            self::SETTING_POINTS_PER_CURRENCY => (int) SystemSetting::getValue(self::SETTING_POINTS_PER_CURRENCY, 1),
            self::SETTING_REDEMPTION_RATE => (int) SystemSetting::getValue(self::SETTING_REDEMPTION_RATE, 100),
            self::SETTING_MIN_ORDER_TO_REDEEM => (float) SystemSetting::getValue(self::SETTING_MIN_ORDER_TO_REDEEM, 0),
            self::SETTING_MAX_POINTS_PER_ORDER => (int) SystemSetting::getValue(self::SETTING_MAX_POINTS_PER_ORDER, 5000),
            self::SETTING_AWARD_ON_STATUS => (string) SystemSetting::getValue(self::SETTING_AWARD_ON_STATUS, 'completed'),
        ];
    }

    /**
     * Award points for a completed order if not already awarded.
     */
    public function awardPointsForOrder(Order $order): bool
    {
        $settings = $this->getSettings();
        $awardStatusSlug = $settings[self::SETTING_AWARD_ON_STATUS];

        $order->loadMissing('status', 'user');
        if (!$order->user_id || !$order->user) {
            return false;
        }

        $statusSlug = $order->status ? $order->status->slug : null;
        if ($statusSlug !== $awardStatusSlug) {
            return false;
        }

        $alreadyAwarded = LoyaltyPointTransaction::where('order_id', $order->id)
            ->where('type', LoyaltyPointTransaction::TYPE_EARN)
            ->exists();
        if ($alreadyAwarded) {
            return false;
        }

        $pointsPerCurrency = (int) $settings[self::SETTING_POINTS_PER_CURRENCY];
        $orderTotal = (float) $order->total;
        $points = (int) floor($orderTotal * $pointsPerCurrency);
        if ($points <= 0) {
            return false;
        }

        DB::transaction(function () use ($order, $points) {
            $order->user->increment('loyalty_points_balance', $points);
            LoyaltyPointTransaction::create([
                'user_id' => $order->user_id,
                'amount' => $points,
                'type' => LoyaltyPointTransaction::TYPE_EARN,
                'order_id' => $order->id,
                'description' => 'نقاط مكتسبة من الطلب #' . $order->order_number,
            ]);
        });

        return true;
    }

    /**
     * Validate redemption and return discount amount. Does NOT change balance (use applyRedemptionToOrder for that).
     *
     * @return array{success: bool, message?: string, discount_amount?: float}
     */
    public function redeemPoints(User $user, int $points, float $orderSubtotal): array
    {
        if ($points <= 0) {
            return ['success' => false, 'message' => 'عدد النقاط يجب أن يكون موجباً.'];
        }

        $settings = $this->getSettings();
        $minOrder = (float) $settings[self::SETTING_MIN_ORDER_TO_REDEEM];
        if ($minOrder > 0 && $orderSubtotal < $minOrder) {
            return [
                'success' => false,
                'message' => 'الحد الأدنى للطلب لاستخدام النقاط هو ' . number_format($minOrder, 2) . ' ر.س.',
            ];
        }

        $maxPoints = (int) $settings[self::SETTING_MAX_POINTS_PER_ORDER];
        if ($points > $maxPoints) {
            return [
                'success' => false,
                'message' => 'الحد الأقصى للنقاط في طلب واحد هو ' . $maxPoints . ' نقطة.',
            ];
        }

        $balance = (int) $user->loyalty_points_balance;
        if ($points > $balance) {
            return ['success' => false, 'message' => 'رصيد النقاط غير كافٍ. الرصيد الحالي: ' . $balance];
        }

        $rate = (int) $settings[self::SETTING_REDEMPTION_RATE];
        if ($rate <= 0) {
            return ['success' => false, 'message' => 'إعدادات استبدال النقاط غير صحيحة.'];
        }

        $discountAmount = ($points / $rate);
        if ($discountAmount > $orderSubtotal) {
            $discountAmount = $orderSubtotal;
        }

        return [
            'success' => true,
            'discount_amount' => round($discountAmount, 2),
        ];
    }

    /**
     * Apply points redemption to an order: deduct user balance, update order, create transaction.
     */
    public function applyRedemptionToOrder(Order $order, User $user, int $points): bool
    {
        $result = $this->redeemPoints($user, $points, (float) $order->subtotal);
        if (!$result['success'] || !isset($result['discount_amount'])) {
            return false;
        }

        $discountAmount = (float) $result['discount_amount'];

        DB::transaction(function () use ($order, $user, $points, $discountAmount) {
            $user->decrement('loyalty_points_balance', $points);

            $newDiscount = (float) $order->discount_amount + $discountAmount;
            $newTotal = (float) $order->total - $discountAmount;

            $order->update([
                'points_redeemed' => (int) $points,
                'points_discount_amount' => $discountAmount,
                'discount_amount' => $newDiscount,
                'total' => max(0, $newTotal),
            ]);

            LoyaltyPointTransaction::create([
                'user_id' => $user->id,
                'amount' => -$points,
                'type' => LoyaltyPointTransaction::TYPE_REDEEM,
                'order_id' => $order->id,
                'description' => 'استبدال نقاط في الطلب #' . $order->order_number,
            ]);
        });

        return true;
    }

    /**
     * Manual adjust points by admin.
     */
    public function adjustPoints(User $user, int $amount, string $description, ?int $adminId = null): void
    {
        if ($amount === 0) {
            return;
        }

        DB::transaction(function () use ($user, $amount, $description, $adminId) {
            $user->increment('loyalty_points_balance', $amount);
            LoyaltyPointTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => LoyaltyPointTransaction::TYPE_ADJUST,
                'order_id' => null,
                'description' => $description,
                'created_by' => $adminId,
            ]);
        });
    }
}
