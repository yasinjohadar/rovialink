<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomerAddressRequest;
use App\Http\Requests\Admin\StoreCustomerNoteRequest;
use App\Http\Requests\Admin\UpdateCustomerAddressRequest;
use App\Models\CustomerAddress;
use App\Models\CustomerNote;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function __construct(
        protected LoyaltyService $loyaltyService
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = User::query()
            ->withCount(['orders as orders_count' => function ($q) {
                $q->whereNull('deleted_at');
            }])
            ->withSum(['orders as total_spent' => function ($q) {
                $q->whereNull('deleted_at');
            }], 'total');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('has_orders')) {
            if ($request->boolean('has_orders')) {
                $query->has('orders');
            } else {
                $query->doesntHave('orders');
            }
        }

        if ($minTotal = $request->input('min_total')) {
            $query->having('total_spent', '>=', (float) $minTotal);
        }

        if ($from = $request->input('registered_from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->input('registered_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $query->orderByDesc('total_spent')->orderByDesc('created_at');

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.pages.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $customer->load([
            'orders' => function ($q) {
                $q->whereNull('deleted_at')->latest();
            },
            'addresses',
            'notes.admin',
            'loyaltyPointTransactions' => function ($q) {
                $q->with('order')->latest()->limit(50);
            },
        ]);

        $ordersQuery = $customer->orders()->whereNull('deleted_at');

        $ordersCount = (clone $ordersQuery)->count();
        $totalSpent = (clone $ordersQuery)->sum('total');
        $averageOrderValue = $ordersCount > 0 ? $totalSpent / $ordersCount : 0;
        $lastOrder = (clone $ordersQuery)->latest()->first();

        $orderIds = $ordersQuery->pluck('id');

        $topProduct = null;
        $topCategory = null;

        if ($orderIds->isNotEmpty()) {
            $topProduct = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
                ->whereIn('order_id', $orderIds)
                ->groupBy('product_id')
                ->orderByDesc('total_qty')
                ->with('product')
                ->first();

            $topCategory = OrderItem::select('products.category_id', DB::raw('SUM(order_items.quantity) as total_qty'))
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->whereIn('order_items.order_id', $orderIds)
                ->groupBy('products.category_id')
                ->orderByDesc('total_qty')
                ->with(['product.category'])
                ->first();
        }

        return view('admin.pages.customers.show', [
            'customer' => $customer,
            'orders' => $customer->orders,
            'ordersCount' => $ordersCount,
            'totalSpent' => $totalSpent,
            'averageOrderValue' => $averageOrderValue,
            'lastOrder' => $lastOrder,
            'topProduct' => $topProduct,
            'topCategory' => $topCategory,
        ]);
    }

    public function storeAddress(StoreCustomerAddressRequest $request, User $customer)
    {
        $data = $request->validated();
        $data['user_id'] = $customer->id;

        if (!empty($data['is_default'])) {
            CustomerAddress::where('user_id', $customer->id)
                ->where('type', $data['type'] ?? 'shipping')
                ->update(['is_default' => false]);
        }

        CustomerAddress::create($data);

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'تم إضافة العنوان بنجاح.');
    }

    public function updateAddress(UpdateCustomerAddressRequest $request, User $customer, CustomerAddress $address)
    {
        abort_if($address->user_id !== $customer->id, 403);

        $data = $request->validated();

        if (!empty($data['is_default'])) {
            CustomerAddress::where('user_id', $customer->id)
                ->where('type', $data['type'] ?? $address->type)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'تم تحديث العنوان بنجاح.');
    }

    public function destroyAddress(User $customer, CustomerAddress $address)
    {
        abort_if($address->user_id !== $customer->id, 403);

        $address->delete();

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'تم حذف العنوان بنجاح.');
    }

    public function storeNote(StoreCustomerNoteRequest $request, User $customer)
    {
        CustomerNote::create([
            'user_id' => $customer->id,
            'admin_id' => Auth::id(),
            'note' => $request->validated()['note'],
        ]);

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'تم إضافة الملاحظة الداخلية بنجاح.');
    }

    public function adjustLoyaltyPoints(Request $request, User $customer)
    {
        $data = $request->validate([
            'amount' => 'required|integer',
            'description' => 'required|string|max:500',
        ]);

        $amount = (int) $data['amount'];
        if ($amount === 0) {
            return redirect()->route('admin.customers.show', $customer)
                ->with('error', 'قيمة التعديل يجب أن تكون مختلفة عن الصفر.');
        }

        $this->loyaltyService->adjustPoints(
            $customer,
            $amount,
            $data['description'],
            Auth::id()
        );

        $message = $amount > 0
            ? 'تم إضافة ' . $amount . ' نقطة إلى رصيد العميل.'
            : 'تم خصم ' . abs($amount) . ' نقطة من رصيد العميل.';

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', $message);
    }
}

