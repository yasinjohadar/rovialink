<div class="table-responsive">
    <table class="table orders-index-table align-middle mb-0">
        <thead>
            <tr>
                <th>الرقم</th>
                <th>العميل</th>
                <th>المبلغ</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentOrders as $order)
                <tr>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="orders-index-order-no text-decoration-none">
                            <i class="bi bi-receipt"></i>
                            {{ $order->order_number }}
                        </a>
                    </td>
                    <td>
                        <span class="orders-index-customer">{{ $order->user?->name ?? 'زائر' }}</span>
                    </td>
                    <td class="orders-index-total">{{ format_money($order->total) }}</td>
                    <td>
                        @if($order->status)
                            <span class="orders-index-status-badge" style="background-color: {{ $order->status->color ?? '#6c757d' }};">
                                {{ $order->status->name }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <div class="orders-index-empty py-4">
                            <i class="bi bi-bag d-block"></i>
                            <p class="mb-0">لا توجد طلبات بعد</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
