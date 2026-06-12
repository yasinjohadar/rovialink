<div class="table-responsive">
    <table class="table orders-index-table align-middle mb-0">
        <thead>
            <tr>
                <th>العميل</th>
                <th>البريد الإلكتروني</th>
                <th>الجوال</th>
                <th>رصيد النقاط</th>
                <th>عدد الطلبات</th>
                <th>إجمالي الإنفاق</th>
                <th>تاريخ التسجيل</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>
                        <a href="{{ route('admin.customers.show', $customer) }}" class="orders-index-customer text-decoration-none">
                            {{ $customer->name }}
                        </a>
                    </td>
                    <td>
                        <div class="orders-index-email-cell">
                            <span class="orders-index-email" dir="ltr">{{ $customer->email }}</span>
                            <button type="button"
                                class="orders-index-copy-btn js-copy-email"
                                data-email="{{ $customer->email }}"
                                title="نسخ البريد الإلكتروني"
                                aria-label="نسخ البريد الإلكتروني">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </td>
                    <td class="orders-index-date" dir="ltr">{{ $customer->phone ?? '—' }}</td>
                    <td>
                        <span class="order-show-qty">{{ number_format($customer->loyalty_points_balance ?? 0, 0) }}</span>
                        <span class="small text-muted">نقطة</span>
                    </td>
                    <td class="fw-semibold">{{ $customer->orders_count ?? 0 }}</td>
                    <td class="orders-index-total">{{ format_money($customer->total_spent ?? 0) }}</td>
                    <td class="orders-index-date">{{ $customer->created_at?->format('Y/m/d — H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.customers.show', $customer) }}" class="orders-index-view-btn">
                            <i class="bi bi-person-lines-fill"></i>
                            عرض الملف
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div class="orders-index-empty">
                            <i class="bi bi-people d-block"></i>
                            <p>لا يوجد عملاء مطابقون لشروط البحث</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($customers->hasPages())
    <div id="customers-pagination">{{ $customers->links() }}</div>
@endif
