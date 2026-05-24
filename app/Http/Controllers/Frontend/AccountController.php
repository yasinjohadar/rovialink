<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreAccountAddressRequest;
use App\Http\Requests\Frontend\StoreOrderReturnRequest;
use App\Http\Requests\Frontend\UpdateAccountAddressRequest;
use App\Http\Requests\Frontend\UpdateAccountPasswordRequest;
use App\Http\Requests\Frontend\UpdateAccountProfileRequest;
use App\Models\CustomerAddress;
use App\Models\LoyaltyPointTransaction;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\Wishlist;
use App\Services\Seo\SeoBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'orders_total' => $user->orders()->count(),
            'orders_active' => $user->orders()->whereHas('status', fn ($q) => $q->where('is_final', false))->count(),
            'wishlist_count' => $user->wishlists()->count(),
            'loyalty_points' => (int) ($user->loyalty_points_balance ?? 0),
        ];

        $recentOrders = $user->orders()
            ->with(['status', 'items.product.images'])
            ->latest()
            ->limit(3)
            ->get();

        $ordersQuery = $user->orders()
            ->with(['status', 'items.product.images']);

        if ($statusSlug = $request->input('status')) {
            $ordersQuery->whereHas('status', fn ($q) => $q->where('slug', $statusSlug));
        }

        $orders = $ordersQuery->latest()->paginate(10)->withQueryString();

        $activeOrder = $user->orders()
            ->with(['status', 'items.product.images', 'statusHistory.newStatus'])
            ->whereHas('status', fn ($q) => $q->where('is_final', false))
            ->latest()
            ->first();

        $orderStatuses = OrderStatus::ordered()->get();
        $statusSteps = $orderStatuses;
        $addresses = $user->addresses()->orderByDesc('is_default')->get();
        $wishlistProducts = $user->wishlistProducts()
            ->with(['images', 'category', 'brand'])
            ->withAvg(['reviews' => fn ($q) => $q->where('status', 'approved')], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('status', 'approved')])
            ->visible()
            ->orderByPivot('wishlists.created_at', 'desc')
            ->get();

        $notifications = $this->buildNotifications($user->id);
        $loyaltyTier = $this->loyaltyTierLabel($stats['loyalty_points']);

        $seo = SeoBuilder::forPage(
            'لوحة التحكم - إديو ستور',
            'تابع طلباتك وإعدادات حسابك من مكان واحد.',
            route('frontend.account')
        );

        return view('frontend.pages.account.index', compact(
            'user',
            'stats',
            'recentOrders',
            'orders',
            'activeOrder',
            'orderStatuses',
            'statusSteps',
            'addresses',
            'wishlistProducts',
            'notifications',
            'loyaltyTier',
            'seo'
        ));
    }

    public function showOrder(Request $request, Order $order): View
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        $order->load([
            'status',
            'items.product.images',
            'items.downloads.file',
            'addresses',
            'returns',
            'statusHistory.newStatus',
            'statusHistory.oldStatus',
            'payments.paymentMethod',
        ]);

        $statusSteps = OrderStatus::ordered()->get();
        $hasPendingReturn = $order->returns()->where('status', OrderReturn::STATUS_PENDING)->exists();

        $seo = SeoBuilder::forPage(
            'طلب ' . $order->order_number,
            'تفاصيل الطلب والتحميلات الرقمية.',
            route('frontend.account.orders.show', $order)
        );

        return view('frontend.pages.account.order', compact('order', 'statusSteps', 'hasPendingReturn', 'seo'));
    }

    public function updateProfile(UpdateAccountProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->safe()->only(['name', 'email', 'phone']);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('users/photos', 'public');
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->to(route('frontend.account') . '#profile')
            ->with('success', 'تم تحديث الملف الشخصي بنجاح.');
    }

    public function updatePassword(UpdateAccountPasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()->to(route('frontend.account') . '#security')
            ->with('success', 'تم تحديث كلمة المرور بنجاح.');
    }

    public function storeAddress(StoreAccountAddressRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $data['user_id'] = $user->id;

        if (! empty($data['is_default'])) {
            CustomerAddress::where('user_id', $user->id)
                ->where('type', $data['type'])
                ->update(['is_default' => false]);
        }

        CustomerAddress::create($data);

        return redirect()->to(route('frontend.account') . '#addresses')
            ->with('success', 'تم إضافة العنوان بنجاح.');
    }

    public function updateAddress(UpdateAccountAddressRequest $request, CustomerAddress $address): RedirectResponse
    {
        abort_if($address->user_id !== $request->user()->id, 403);

        $data = $request->validated();

        if (! empty($data['is_default'])) {
            CustomerAddress::where('user_id', $address->user_id)
                ->where('type', $data['type'] ?? $address->type)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect()->to(route('frontend.account') . '#addresses')
            ->with('success', 'تم تحديث العنوان بنجاح.');
    }

    public function destroyAddress(Request $request, CustomerAddress $address): RedirectResponse
    {
        abort_if($address->user_id !== $request->user()->id, 403);

        $address->delete();

        return redirect()->to(route('frontend.account') . '#addresses')
            ->with('success', 'تم حذف العنوان بنجاح.');
    }

    public function reorder(Request $request, Order $order): RedirectResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        $order->load('items.product');
        $cartService = app(\App\Services\CartService::class);

        foreach ($order->items as $item) {
            $product = $item->product ?? Product::find($item->product_id);
            if (! $product || ! $product->in_stock) {
                continue;
            }

            $cartService->add(
                (int) $product->id,
                (int) $item->quantity,
                $item->product_variant_id ? (int) $item->product_variant_id : null
            );
        }

        return redirect()->route('frontend.cart.index')
            ->with('success', 'تمت إضافة منتجات الطلب إلى السلة.');
    }

    public function storeReturn(StoreOrderReturnRequest $request, Order $order): RedirectResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        $order->load('status');

        if (! $order->status?->is_final) {
            return back()->withErrors(['return' => 'لا يمكن طلب إرجاع لطلب لم يكتمل بعد.']);
        }

        if ($order->returns()->where('status', OrderReturn::STATUS_PENDING)->exists()) {
            return back()->withErrors(['return' => 'لديك طلب إرجاع قيد المراجعة لهذا الطلب.']);
        }

        OrderReturn::create([
            'order_id' => $order->id,
            'requested_by' => $request->user()->id,
            'status' => OrderReturn::STATUS_PENDING,
            'reason' => $request->validated('reason'),
            'requested_at' => now(),
        ]);

        return redirect()->route('frontend.account.orders.show', $order)
            ->with('success', 'تم إرسال طلب الإرجاع وسيتم مراجعته.');
    }

    public function removeWishlist(Request $request, Product $product): RedirectResponse
    {
        Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->delete();

        return redirect()->to(route('frontend.account') . '#wishlist')
            ->with('success', 'تمت إزالة المنتج من المفضلة.');
    }

    public function toggleWishlist(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();

        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
            $message = 'تم الإزالة من المفضلة';
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
            $wishlisted = true;
            $message = 'تمت الإضافة للمفضلة!';
        }

        return response()->json([
            'wishlisted' => $wishlisted,
            'wishlist_count' => $user->wishlists()->count(),
            'message' => $message,
        ]);
    }

    protected function buildNotifications(int $userId): Collection
    {
        $orderIds = Order::where('user_id', $userId)->pluck('id');

        $statusEvents = OrderStatusHistory::query()
            ->whereIn('order_id', $orderIds)
            ->with(['order', 'newStatus'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($h) => [
                'type' => 'order_status',
                'title' => 'تحديث حالة الطلب',
                'body' => 'طلب #' . ($h->order?->order_number ?? '') . ' — ' . ($h->newStatus?->name ?? 'تحديث'),
                'at' => $h->created_at,
                'icon' => 'fa-truck',
                'icon_class' => 'text-accent',
                'badge' => null,
            ]);

        $loyaltyEvents = LoyaltyPointTransaction::query()
            ->where('user_id', $userId)
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($t) => [
                'type' => 'loyalty',
                'title' => $t->type === LoyaltyPointTransaction::TYPE_EARN ? 'نقاط مكافآت' : 'نقاط مستخدمة',
                'body' => $t->description ?? ('تم ' . ($t->amount >= 0 ? 'إضافة' : 'خصم') . ' ' . abs($t->amount) . ' نقطة'),
                'at' => $t->created_at,
                'icon' => 'fa-coins',
                'icon_class' => 'text-info',
                'badge' => null,
            ]);

        return $statusEvents->concat($loyaltyEvents)
            ->sortByDesc('at')
            ->take(15)
            ->values();
    }

    protected function loyaltyTierLabel(int $points): string
    {
        if ($points >= 500) {
            return 'عضو ذهبي';
        }
        if ($points >= 100) {
            return 'عضو فضي';
        }

        return 'عضو جديد';
    }

    public static function userInitials(?string $name): string
    {
        if (! $name) {
            return '؟';
        }

        $parts = preg_split('/\s+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY);

        if (count($parts) >= 2) {
            return mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1);
        }

        return mb_substr($parts[0], 0, 2);
    }
}
