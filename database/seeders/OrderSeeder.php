<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class OrderSeeder extends Seeder
{
    private const DEMO_ORDER_PREFIX = 'ORD-DEMO-';

    private const DEMO_EMAIL_DOMAIN = '@rovialink.test';

    public function run(): void
    {
        $products = Product::query()->where('status', 'active')->get();
        if ($products->isEmpty()) {
            $this->command->error('لا توجد منتجات نشطة. شغّل ProductSeeder أولاً: php artisan db:seed --class=ProductSeeder');

            return;
        }

        $statuses = OrderStatus::orderBy('order')->get()->keyBy('slug');
        if ($statuses->isEmpty()) {
            $this->command->error('جدول حالات الطلب فارغ. تأكد من تشغيل migrations.');

            return;
        }

        $this->clearDemoOrders();

        $paymentMethods = $this->ensurePaymentMethods();
        $customers = $this->ensureCustomers(25);
        $admin = User::where('email', 'admin@admin.com')->first();

        $statusPlan = array_merge(
            array_fill(0, 5, 'pending'),
            array_fill(0, 6, 'processing'),
            array_fill(0, 4, 'shipped'),
            array_fill(0, 12, 'completed'),
            array_fill(0, 3, 'cancelled'),
            array_fill(0, 2, 'refunded'),
            array_fill(0, 3, null),
        );

        shuffle($statusPlan);

        $couponCodes = ['WELCOME10', 'SAVE20', 'VIP15', null, null, null];
        $orderSeq = 1;

        DB::transaction(function () use (
            $products,
            $statuses,
            $paymentMethods,
            $customers,
            $admin,
            $statusPlan,
            $couponCodes,
            &$orderSeq
        ) {
            foreach ($statusPlan as $index => $statusSlug) {
                $isGuest = $statusSlug === null;
                $statusSlug = $isGuest ? fake()->randomElement(['pending', 'processing', 'completed']) : $statusSlug;
                $status = $statuses[$statusSlug];
                $customer = $isGuest ? null : $customers->random();
                $createdAt = Carbon::now()->subDays(rand(0, 60))->subHours(rand(0, 23));

                $orderNumber = self::DEMO_ORDER_PREFIX . $createdAt->format('Ymd') . '-' . str_pad((string) $orderSeq, 4, '0', STR_PAD_LEFT);
                $orderSeq++;

                $itemCount = rand(1, min(3, $products->count()));
                $picked = $products->random($itemCount);

                $subtotal = 0.0;
                $orderItemsPayload = [];

                foreach ($picked as $product) {
                    $qty = rand(1, 2);
                    $unitPrice = (float) $product->effective_price;
                    $lineTotal = round($unitPrice * $qty, 2);
                    $subtotal += $lineTotal;

                    $orderItemsPayload[] = [
                        'product' => $product,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'total' => $lineTotal,
                    ];
                }

                $shippingAmount = 0;
                $taxAmount = round($subtotal * 0.15, 2);
                $coupon = fake()->randomElement($couponCodes);
                $discountAmount = $coupon ? round($subtotal * 0.1, 2) : 0;
                $pointsRedeemed = rand(0, 1) ? rand(50, 200) : 0;
                $pointsDiscount = $pointsRedeemed > 0 ? round($pointsRedeemed * 0.05, 2) : 0;
                $total = max(0, round($subtotal + $shippingAmount + $taxAmount - $discountAmount - $pointsDiscount, 2));

                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => $customer?->id,
                    'order_status_id' => $status->id,
                    'subtotal' => $subtotal,
                    'shipping_amount' => $shippingAmount,
                    'tax_amount' => $taxAmount,
                    'discount_amount' => $discountAmount,
                    'total' => $total,
                    'coupon_code' => $coupon,
                    'points_redeemed' => $pointsRedeemed,
                    'points_discount_amount' => $pointsDiscount,
                    'currency' => 'SAR',
                    'customer_note' => rand(0, 3) === 0 ? fake('ar_SA')->sentence() : null,
                    'admin_note' => rand(0, 4) === 0 ? 'طلب تجريبي للاختبار' : null,
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => 'OrderSeeder/1.0',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                foreach ($orderItemsPayload as $row) {
                    $product = $row['product'];
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                        'product_name' => $product->name,
                        'variant_description' => null,
                        'sku' => $product->sku,
                        'quantity' => $row['quantity'],
                        'unit_price' => $row['unit_price'],
                        'total' => $row['total'],
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }

                $this->seedAddresses($order, $customer, $createdAt);
                $this->seedPayment($order, $paymentMethods->random(), $statusSlug, $total, $createdAt);
                $this->seedStatusHistory($order, $status, $admin?->id, $createdAt);

                if ($index < 3 && in_array($statusSlug, ['completed', 'shipped'], true)) {
                    $this->seedPendingReturn($order, $customer?->id, $createdAt->copy()->addDay());
                }
            }

            $completedOrders = Order::where('order_number', 'like', self::DEMO_ORDER_PREFIX . '%')
                ->whereHas('status', fn ($q) => $q->where('slug', 'completed'))
                ->with('items')
                ->limit(2)
                ->get();

            foreach ($completedOrders as $order) {
                $this->seedApprovedReturn($order, $admin?->id, $statuses['refunded']);
            }
        });

        $this->command->info('تم إنشاء ' . count($statusPlan) . ' طلب تجريبي.');
        $this->command->info('عملاء تجريبيون: customer-demo-1' . self::DEMO_EMAIL_DOMAIN . ' … customer-demo-25' . self::DEMO_EMAIL_DOMAIN);
        $this->command->info('كلمة المرور للعملاء: password');
    }

    private function clearDemoOrders(): void
    {
        $demoOrderIds = Order::withTrashed()
            ->where('order_number', 'like', self::DEMO_ORDER_PREFIX . '%')
            ->pluck('id');

        if ($demoOrderIds->isNotEmpty()) {
            Order::withTrashed()->whereIn('id', $demoOrderIds)->forceDelete();
            $this->command->warn('تم حذف طلبات تجريبية سابقة (ORD-DEMO-*).');
        }
    }

    private function ensurePaymentMethods()
    {
        $methods = [
            ['name' => 'الدفع عند الاستلام', 'slug' => 'cod', 'driver' => 'cod', 'order' => 1],
            ['name' => 'تحويل بنكي', 'slug' => 'bank_transfer', 'driver' => 'bank_transfer', 'order' => 2],
            ['name' => 'بطاقة ائتمان', 'slug' => 'card', 'driver' => 'card', 'order' => 3, 'config' => ['gateway' => 'stripe']],
            ['name' => 'PayPal', 'slug' => 'paypal', 'driver' => 'paypal', 'order' => 4],
        ];

        foreach ($methods as $data) {
            PaymentMethod::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'driver' => $data['driver'],
                    'is_active' => true,
                    'order' => $data['order'],
                ]
            );
        }

        return PaymentMethod::where('is_active', true)->orderBy('order')->get();
    }

    private function ensureCustomers(int $count)
    {
        $role = Role::firstOrCreate(['name' => 'user']);
        $customers = collect();

        for ($i = 1; $i <= $count; $i++) {
            $email = 'customer-demo-' . $i . self::DEMO_EMAIL_DOMAIN;
            $name = fake('ar_SA')->name();

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'username' => 'customer_demo_' . $i,
                    'phone' => '05' . fake()->numerify('########'),
                    'password' => 'password',
                    'status' => 'active',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'loyalty_points_balance' => rand(0, 500),
                ]
            );

            if (! $user->hasRole($role->name)) {
                $user->assignRole($role);
            }

            $customers->push($user);
        }

        return $customers;
    }

    private function seedAddresses(Order $order, ?User $customer, Carbon $at): void
    {
        $parts = $customer
            ? preg_split('/\s+/', trim($customer->name), 2)
            : ['عميل', 'زائر'];

        $firstName = $parts[0] ?? 'عميل';
        $lastName = $parts[1] ?? 'تجريبي';
        $email = $customer?->email ?? 'guest@rovialink.test';
        $phone = $customer?->phone ?? '0500000000';
        $city = fake()->randomElement(['الرياض', 'جدة', 'الدمام', 'مكة', 'المدينة']);

        $base = [
            'order_id' => $order->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'address_line_1' => 'شارع ' . fake()->numberBetween(1, 99),
            'address_line_2' => 'حي ' . fake()->word(),
            'city' => $city,
            'state' => 'منطقة ' . $city,
            'postal_code' => (string) fake()->numberBetween(10000, 99999),
            'country' => 'SA',
            'created_at' => $at,
            'updated_at' => $at,
        ];

        OrderAddress::create(array_merge($base, ['type' => 'billing']));
    }

    private function seedPayment(Order $order, PaymentMethod $method, string $statusSlug, float $total, Carbon $at): void
    {
        $paymentStatus = match ($statusSlug) {
            'completed', 'shipped', 'processing' => fake()->randomElement(['completed', 'completed', 'pending']),
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
            default => 'pending',
        };

        Payment::create([
            'order_id' => $order->id,
            'payment_method_id' => $method->id,
            'transaction_id' => 'TXN-DEMO-' . strtoupper(Str::random(10)),
            'amount' => $total,
            'currency' => 'SAR',
            'status' => $paymentStatus,
            'paid_at' => $paymentStatus === 'completed' ? $at->copy()->addMinutes(rand(5, 120)) : null,
            'created_at' => $at,
            'updated_at' => $at,
        ]);
    }

    private function seedStatusHistory(Order $order, OrderStatus $currentStatus, ?int $adminId, Carbon $at): void
    {
        $pending = OrderStatus::where('slug', 'pending')->first();
        if (! $pending) {
            return;
        }

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status_id' => null,
            'new_status_id' => $pending->id,
            'changed_by' => null,
            'note' => 'إنشاء الطلب',
            'created_at' => $at,
            'updated_at' => $at,
        ]);

        if ($currentStatus->slug !== 'pending') {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status_id' => $pending->id,
                'new_status_id' => $currentStatus->id,
                'changed_by' => $adminId,
                'note' => 'تحديث الحالة (بيانات تجريبية)',
                'created_at' => $at->copy()->addHours(rand(1, 48)),
                'updated_at' => $at->copy()->addHours(rand(1, 48)),
            ]);
        }
    }

    private function seedPendingReturn(Order $order, ?int $userId, Carbon $at): void
    {
        $item = $order->items()->first();
        if (! $item) {
            return;
        }

        $return = OrderReturn::create([
            'order_id' => $order->id,
            'requested_by' => $userId,
            'status' => OrderReturn::STATUS_PENDING,
            'reason' => 'المنتج لا يطابق الوصف',
            'requested_at' => $at,
            'created_at' => $at,
            'updated_at' => $at,
        ]);

        OrderReturnItem::create([
            'order_return_id' => $return->id,
            'order_item_id' => $item->id,
            'quantity' => 1,
        ]);
    }

    private function seedApprovedReturn(Order $order, ?int $adminId, OrderStatus $refundedStatus): void
    {
        $item = $order->items()->first();
        if (! $item || $order->returns()->exists()) {
            return;
        }

        $at = $order->created_at->copy()->addDays(rand(3, 10));

        $return = OrderReturn::create([
            'order_id' => $order->id,
            'requested_by' => $order->user_id,
            'status' => OrderReturn::STATUS_APPROVED,
            'reason' => 'طلب استرجاع تجريبي',
            'admin_note' => 'معتمد للاختبار',
            'requested_at' => $at,
            'processed_at' => $at->copy()->addDay(),
            'processed_by' => $adminId,
            'created_at' => $at,
            'updated_at' => $at,
        ]);

        OrderReturnItem::create([
            'order_return_id' => $return->id,
            'order_item_id' => $item->id,
            'quantity' => min(1, $item->quantity),
        ]);

        $order->update(['order_status_id' => $refundedStatus->id]);
    }
}
