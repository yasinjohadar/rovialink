<div class="table-responsive">
    <table class="table table-striped table-hover align-middle table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th>رقم الطلب</th>
                <th>العميل</th>
                <th>الحالة</th>
                <th>المجموع</th>
                <th>التاريخ</th>
                <th>عمليات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td><strong>{{ $order->order_number }}</strong></td>
                    <td>{{ $order->user->name ?? 'ضيف' }}</td>
                    <td>
                        <div class="order-status-picker" data-order-id="{{ $order->id }}">
                            <button type="button"
                                class="badge border-0 js-order-status-badge"
                                style="background-color: {{ $order->status?->color ?? '#6c757d' }}; cursor: pointer;"
                                title="{{ $order->status?->name ?? 'غير معروف' }} — انقر للتغيير">
                                {{ $order->status?->name ?? 'غير معروف' }}
                            </button>
                            <select class="form-select form-select-sm js-order-status-select d-none"
                                data-order-id="{{ $order->id }}"
                                data-previous="{{ $order->order_status_id }}"
                                data-url="{{ route('admin.orders.update-status', $order) }}"
                                style="min-width: 140px; border-color: {{ $order->status?->color ?? '#6c757d' }}">
                                @foreach ($statuses as $s)
                                    <option value="{{ $s->id }}" {{ $order->order_status_id == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>{{ $currencyService->format((float) $order->total) }}</td>
                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">عرض</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">لا توجد طلبات</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($orders->hasPages())
    <div class="mt-3" id="orders-pagination">{{ $orders->links() }}</div>
@endif
