<div class="table-responsive">
    <table class="table orders-index-table align-middle mb-0">
        <thead>
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
                    <td>
                        <span class="orders-index-order-no">
                            <i class="bi bi-receipt"></i>
                            {{ $order->order_number }}
                        </span>
                    </td>
                    <td>
                        @if($order->user)
                            <span class="orders-index-customer">{{ $order->user->name }}</span>
                        @else
                            <span class="orders-index-customer orders-index-customer--guest">ضيف</span>
                        @endif
                    </td>
                    <td>
                        <div class="order-status-picker" data-order-id="{{ $order->id }}">
                            <button type="button"
                                class="badge border-0 js-order-status-badge orders-index-status-badge"
                                style="background-color: {{ $order->status?->color ?? '#6c757d' }};"
                                title="{{ $order->status?->name ?? 'غير معروف' }} — انقر للتغيير">
                                {{ $order->status?->name ?? 'غير معروف' }}
                            </button>
                            <select class="form-select form-select-sm js-order-status-select d-none"
                                data-order-id="{{ $order->id }}"
                                data-previous="{{ $order->order_status_id }}"
                                data-url="{{ route('admin.orders.update-status', $order) }}"
                                style="border-color: {{ $order->status?->color ?? '#6c757d' }}">
                                @foreach ($statuses as $s)
                                    <option value="{{ $s->id }}" {{ $order->order_status_id == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td class="orders-index-total">{{ $currencyService->format((float) $order->total) }}</td>
                    <td class="orders-index-date">{{ $order->created_at->format('Y/m/d — H:i') }}</td>
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
                        <div class="orders-index-empty">
                            <i class="bi bi-inbox d-block"></i>
                            <p>لا توجد طلبات مطابقة للبحث</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($orders->hasPages())
    <div id="orders-pagination">{{ $orders->links() }}</div>
@endif
