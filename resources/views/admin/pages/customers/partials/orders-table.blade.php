<div class="table-responsive">
    <table class="table order-show-items mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>رقم الطلب</th>
                <th>الحالة</th>
                <th>الإجمالي</th>
                <th>التاريخ</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="text-muted">{{ $order->id }}</td>
                    <td class="fw-semibold">{{ $order->order_number }}</td>
                    <td>
                        <span class="orders-index-status-badge"
                            style="background-color: {{ $order->status->color ?? '#6c757d' }};">
                            {{ $order->status->name ?? '—' }}
                        </span>
                    </td>
                    <td class="order-show-price-total">{{ format_money($order->total) }}</td>
                    <td class="text-muted small">{{ $order->created_at->format('Y/m/d — H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="orders-index-view-btn">
                            <i class="bi bi-eye"></i>
                            عرض
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="orders-index-empty py-4">
                            <i class="bi bi-bag d-block"></i>
                            <p class="mb-0">لا توجد طلبات لهذا العميل بعد.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
