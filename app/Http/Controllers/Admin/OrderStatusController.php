<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrderStatusRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(StoreOrderStatusRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $maxOrder = (int) OrderStatus::max('order');

        OrderStatus::create([
            'name' => $data['name'],
            'slug' => OrderStatus::uniqueSlugFromName($data['name']),
            'color' => $data['color'] ?? '#6c757d',
            'order' => (int) ($data['order'] ?? ($maxOrder + 1)),
            'is_final' => $request->boolean('is_final'),
        ]);

        return $this->redirectBack('تم إضافة حالة الطلب بنجاح.');
    }

    public function update(UpdateOrderStatusRequest $request, OrderStatus $orderStatus): RedirectResponse
    {
        $data = $request->validated();
        $systemRole = $data['system_role'] ?? null;

        $orderStatus->update([
            'name' => $data['name'],
            'color' => $data['color'] ?? '#6c757d',
            'order' => (int) ($data['order'] ?? $orderStatus->order),
            'is_final' => $request->boolean('is_final'),
            'system_role' => $systemRole,
        ]);

        if ($systemRole) {
            OrderStatus::clearRoleExcept($systemRole, $orderStatus->id);
        }

        return $this->redirectBack('تم تحديث حالة الطلب بنجاح.');
    }

    public function destroy(Request $request, OrderStatus $orderStatus): RedirectResponse
    {
        $otherStatuses = OrderStatus::where('id', '!=', $orderStatus->id)->ordered()->get();

        if ($otherStatuses->isEmpty()) {
            return $this->redirectBack('يجب أن تبقى حالة واحدة على الأقل.', 'error');
        }

        $request->validate([
            'reassign_to' => [
                'required',
                Rule::exists('order_statuses', 'id')->whereNot('id', $orderStatus->id),
            ],
        ]);

        $reassignTo = (int) $request->input('reassign_to');
        $replacement = OrderStatus::findOrFail($reassignTo);

        DB::transaction(function () use ($orderStatus, $reassignTo, $replacement) {
            Order::where('order_status_id', $orderStatus->id)->update(['order_status_id' => $reassignTo]);

            if ($orderStatus->system_role) {
                $replacement->update(['system_role' => $orderStatus->system_role]);
                OrderStatus::clearRoleExcept($orderStatus->system_role, $replacement->id);
            }

            $orderStatus->delete();
        });

        return $this->redirectBack('تم حذف الحالة ونقل الطلبات المرتبطة.');
    }

    private function redirectBack(string $message, string $type = 'success'): RedirectResponse
    {
        return redirect()
            ->route('admin.orders.index')
            ->with($type, $message);
    }
}
