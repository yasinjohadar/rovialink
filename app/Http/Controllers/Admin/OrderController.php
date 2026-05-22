<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\OrderStatus;
use App\Services\CurrencyService;
use App\Services\LoyaltyService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        protected LoyaltyService $loyaltyService
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $orders = $this->filteredOrdersQuery($request)
            ->paginate(15)
            ->withQueryString();
        $statuses = OrderStatus::withCount('orders')->ordered()->get();
        $currencyService = app(CurrencyService::class);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('admin.pages.orders.partials.table', compact('orders', 'currencyService', 'statuses'))->render(),
            ]);
        }

        return view('admin.pages.orders.index', compact('orders', 'statuses', 'currencyService'));
    }

    private function filteredOrdersQuery(Request $request)
    {
        $query = Order::with(['user', 'status'])->orderByDesc('created_at');

        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%' . $request->input('order_number') . '%');
        }

        if ($request->filled('status')) {
            $query->whereHas('status', fn ($q) => $q->where('slug', $request->input('status')));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        return $query;
    }

    public function show(Order $order)
    {
        $order->load([
            'user',
            'status',
            'items.product',
            'items.variant',
            'addresses',
            'payments',
            'statusHistory.user',
            'statusHistory.newStatus',
            'returns.items',
        ]);
        $currencyService = app(CurrencyService::class);

        return view('admin.pages.orders.show', compact('order', 'currencyService'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'order_status_id' => 'required|exists:order_statuses,id',
            'admin_note' => 'nullable|string|max:2000',
        ]);

        $oldStatusId = $order->order_status_id;
        $newStatusId = (int) $data['order_status_id'];

        $oldStatus = OrderStatus::find($oldStatusId);
        $newStatus = OrderStatus::find($newStatusId);

        $order->update([
            'order_status_id' => $newStatusId,
            'admin_note' => $data['admin_note'] ?? $order->admin_note,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status_id' => $oldStatusId,
            'new_status_id' => $newStatusId,
            'changed_by' => Auth::id(),
            'note' => $data['admin_note'] ?? null,
        ]);

        app(ActivityLogger::class)->orderStatusChanged(
            $order,
            $oldStatus ? $oldStatus->name : '—',
            $newStatus ? $newStatus->name : '—'
        );

        // منح نقاط الولاء عند إكمال الطلب
        $this->loyaltyService->awardPointsForOrder($order->fresh(['status', 'user']));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الطلب.',
                'status' => [
                    'id' => $newStatus->id,
                    'name' => $newStatus->name,
                    'color' => $newStatus->color ?? '#6c757d',
                ],
            ]);
        }

        return back()->with('success', 'تم تحديث حالة الطلب وتسجيل الملاحظة.');
    }
}
