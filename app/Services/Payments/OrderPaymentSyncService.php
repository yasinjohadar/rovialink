<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\OrderDownload;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Services\LoyaltyService;
use Illuminate\Support\Str;

class OrderPaymentSyncService
{
    public function __construct(
        protected LoyaltyService $loyaltyService
    ) {}

    public function markCompleted(Payment $payment, ?string $transactionId = null, array $metadata = []): void
    {
        if ($payment->status === 'completed') {
            return;
        }

        $payment->update([
            'status' => 'completed',
            'transaction_id' => $transactionId ?? $payment->transaction_id,
            'paid_at' => now(),
            'metadata' => array_merge($payment->metadata ?? [], $metadata),
        ]);

        $order = $payment->order()->with(['items.product.files', 'status'])->first();
        if (! $order) {
            return;
        }

        $processingId = OrderStatus::where('slug', 'processing')->value('id')
            ?? OrderStatus::idForRole(OrderStatus::ROLE_CHECKOUT);

        if ($processingId && $order->order_status_id !== $processingId) {
            $order->update(['order_status_id' => $processingId]);
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'order_status_id' => $processingId,
                'user_id' => null,
                'note' => 'تم تأكيد الدفع',
            ]);
        }

        $this->provisionDigitalDownloads($order);
        $this->loyaltyService->awardPointsForOrder($order->fresh(['status', 'user']));
    }

    public function markFailed(Payment $payment, ?string $reason = null): void
    {
        $payment->update([
            'status' => 'failed',
            'gateway_response' => $reason,
        ]);
    }

    public function markCancelled(Payment $payment, ?string $reason = null): void
    {
        $payment->update([
            'status' => 'cancelled',
            'gateway_response' => $reason,
        ]);
    }

    public function markRefunded(Payment $payment): void
    {
        $payment->update(['status' => 'refunded']);

        $order = $payment->order;
        if (! $order) {
            return;
        }

        $refundedId = OrderStatus::idForRole(OrderStatus::ROLE_RETURN_REFUND)
            ?? OrderStatus::where('slug', 'refunded')->value('id');

        if ($refundedId) {
            $order->update(['order_status_id' => $refundedId]);
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'order_status_id' => $refundedId,
                'user_id' => auth()->id(),
                'note' => 'تم استرداد المبلغ',
            ]);
        }
    }

    protected function provisionDigitalDownloads(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            if (! $product?->is_digital) {
                continue;
            }

            $existing = OrderDownload::where('order_item_id', $item->id)->exists();
            if ($existing) {
                continue;
            }

            $product->loadMissing('files');
            $files = $product->files->where('downloadable', true);

            foreach ($files as $file) {
                $expiresAt = $product->digital_download_expiry_days
                    ? now()->addDays((int) $product->digital_download_expiry_days)
                    : null;

                OrderDownload::create([
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'product_file_id' => $file->id,
                    'download_token' => Str::random(40),
                    'remaining_downloads' => $product->digital_download_limit,
                    'expires_at' => $expiresAt,
                ]);
            }
        }
    }
}
