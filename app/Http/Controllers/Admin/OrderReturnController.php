<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = OrderReturn::with(['order', 'requestedByUser', 'items.orderItem.product', 'items.orderItem.variant'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('order_id')) {
            $query->where('order_id', $request->input('order_id'));
        }

        $returns = $query->paginate(20);

        return view('admin.pages.order-returns.index', compact('returns'));
    }

    public function show(OrderReturn $orderReturn)
    {
        $orderReturn->load([
            'order.user',
            'order.status',
            'order.items.product',
            'order.items.variant',
            'requestedByUser',
            'processedByUser',
            'items.orderItem.product',
            'items.orderItem.variant',
        ]);

        return view('admin.pages.order-returns.show', compact('orderReturn'));
    }

    public function store(Request $request, Order $order)
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.order_item_id' => ['required', 'exists:order_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $data['items'] = array_values(array_filter($data['items'], fn($row) => (int) ($row['quantity'] ?? 0) > 0));
        if (empty($data['items'])) {
            return back()->with('error', 'يجب تحديد كمية مرتجعة واحدة على الأقل.');
        }

        foreach ($data['items'] as $row) {
            $orderItem = $order->items()->find($row['order_item_id']);
            if (!$orderItem || $orderItem->order_id !== $order->id) {
                return back()->with('error', 'أحد البنود لا يخص هذا الطلب.');
            }
            if ($row['quantity'] > $orderItem->quantity) {
                return back()->with('error', 'الكمية المرتجعة لا يمكن أن تتجاوز كمية البند في الطلب.');
            }
        }

        $returnedSoFar = [];
        foreach ($order->returns()->where('status', OrderReturn::STATUS_APPROVED)->with('items')->get() as $r) {
            foreach ($r->items as $ri) {
                $returnedSoFar[$ri->order_item_id] = ($returnedSoFar[$ri->order_item_id] ?? 0) + $ri->quantity;
            }
        }

        foreach ($data['items'] as $row) {
            $already = $returnedSoFar[$row['order_item_id']] ?? 0;
            $orderItem = $order->items()->find($row['order_item_id']);
            $maxAllowed = $orderItem->quantity - $already;
            if ($row['quantity'] > $maxAllowed) {
                return back()->with('error', 'الكمية المرتجعة تتجاوز المتبقي من البند بعد المرتجعات المعتمدة سابقاً.');
            }
        }

        DB::beginTransaction();
        try {
            $orderReturn = OrderReturn::create([
                'order_id' => $order->id,
                'requested_by' => Auth::id(),
                'status' => OrderReturn::STATUS_PENDING,
                'reason' => $data['reason'] ?? null,
                'requested_at' => now(),
            ]);

            foreach ($data['items'] as $row) {
                OrderReturnItem::create([
                    'order_return_id' => $orderReturn->id,
                    'order_item_id' => $row['order_item_id'],
                    'quantity' => $row['quantity'],
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء طلب المرتجع.');
        }

        return redirect()->route('admin.order-returns.show', $orderReturn)->with('success', 'تم إنشاء طلب المرتجع بنجاح.');
    }

    public function approve(Request $request, OrderReturn $orderReturn)
    {
        if ($orderReturn->status !== OrderReturn::STATUS_PENDING) {
            return back()->with('error', 'لا يمكن اعتماد طلب مرتجع تمت معالجته مسبقاً.');
        }

        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $orderReturn->load('items.orderItem.product', 'items.orderItem.variant', 'order');

        DB::beginTransaction();
        try {
            $orderReturn->update([
                'status' => OrderReturn::STATUS_APPROVED,
                'admin_note' => $data['admin_note'] ?? $orderReturn->admin_note,
                'processed_at' => now(),
                'processed_by' => Auth::id(),
            ]);

            $order = $orderReturn->order;
            $totalReturnedByItem = [];
            foreach ($order->returns()->where('status', OrderReturn::STATUS_APPROVED)->with('items')->get() as $r) {
                foreach ($r->items as $ri) {
                    $totalReturnedByItem[$ri->order_item_id] = ($totalReturnedByItem[$ri->order_item_id] ?? 0) + $ri->quantity;
                }
            }

            $allItemsFullyReturned = true;
            foreach ($order->items as $item) {
                $returned = $totalReturnedByItem[$item->id] ?? 0;
                if ($returned < $item->quantity) {
                    $allItemsFullyReturned = false;
                    break;
                }
            }

            if ($allItemsFullyReturned) {
                $refundedStatus = OrderStatus::where('system_role', OrderStatus::ROLE_RETURN_REFUND)->first();
                if ($refundedStatus) {
                    $order->update(['order_status_id' => $refundedStatus->id]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء اعتماد طلب المرتجع.');
        }

        return back()->with('success', 'تم اعتماد طلب المرتجع.');
    }

    public function reject(Request $request, OrderReturn $orderReturn)
    {
        if ($orderReturn->status !== OrderReturn::STATUS_PENDING) {
            return back()->with('error', 'لا يمكن رفض طلب مرتجع تمت معالجته مسبقاً.');
        }

        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $orderReturn->update([
            'status' => OrderReturn::STATUS_REJECTED,
            'admin_note' => $data['admin_note'] ?? $orderReturn->admin_note,
            'processed_at' => now(),
            'processed_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم رفض طلب المرتجع.');
    }
}
