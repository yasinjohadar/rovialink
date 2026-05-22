@php
    $roleLabels = [
        \App\Models\OrderStatus::ROLE_CHECKOUT => 'طلب جديد (عند الدفع)',
        \App\Models\OrderStatus::ROLE_RETURN_REFUND => 'مرتجع (عند اعتماد الإرجاع)',
    ];
@endphp

<div class="card mb-3" id="order-statuses-panel">
    <div class="card-header d-flex justify-content-between align-items-center py-2">
        <button class="btn btn-link text-decoration-none p-0 fw-semibold text-body d-flex align-items-center gap-2 collapsed"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#order-statuses-collapse"
            aria-expanded="false"
            aria-controls="order-statuses-collapse">
            <i class="bi bi-chevron-down"></i>
            إدارة حالات الطلب
        </button>
        <span class="text-muted small">{{ $statuses->count() }} حالة</span>
    </div>
    <div class="collapse" id="order-statuses-collapse">
        <div class="card-body border-top">
            <p class="text-muted small mb-3">
                <strong>التعديل:</strong> غيّر الاسم أو اللون أو الترتيب أو «دور النظام» ثم اضغط <strong>حفظ</strong> في نفس الصف.
                <strong>الحذف:</strong> اختر حالة بديلة لنقل الطلبات إليها ثم اضغط حذف. إن كانت الحالة مرتبطة بالنظام (طلب جديد / مرتجع) يُنقل دورها تلقائياً للحالة البديلة.
            </p>

            <form action="{{ route('admin.orders.statuses.store') }}" method="POST" class="row g-2 align-items-end mb-4">
                @csrf
                <div class="col-md-4">
                    <label class="form-label mb-1">اسم الحالة <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" placeholder="مثال: جاهز للتسليم" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">اللون</label>
                    <input type="color" name="color" class="form-control form-control-color w-100 @error('color') is-invalid @enderror"
                        value="{{ old('color', '#6c757d') }}" title="لون الشارة">
                    @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">الترتيب</label>
                    <input type="number" name="order" class="form-control @error('order') is-invalid @enderror"
                        value="{{ old('order') }}" min="0" placeholder="تلقائي">
                    @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="is_final" value="1" class="form-check-input" id="status-is-final-new"
                            {{ old('is_final') ? 'checked' : '' }}>
                        <label class="form-check-label" for="status-is-final-new">حالة نهائية</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i> إضافة
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>الحالة والتعديل</th>
                            <th>دور النظام</th>
                            <th>الطلبات</th>
                            <th>حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($statuses as $status)
                            @php
                                $ordersCount = $status->orders_count ?? 0;
                                $others = $statuses->where('id', '!=', $status->id);
                            @endphp
                            <tr>
                                <td>
                                    <form action="{{ route('admin.orders.statuses.update', $status) }}" method="POST"
                                        class="row g-2 align-items-center">
                                        @csrf
                                        @method('PUT')
                                        <div class="col-auto">
                                            <span class="badge d-inline-block" style="background-color: {{ $status->color ?? '#6c757d' }}; width: 1.5rem;">&nbsp;</span>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="name" class="form-control form-control-sm"
                                                value="{{ $status->name }}" required title="اسم الحالة">
                                        </div>
                                        <div class="col-auto">
                                            <input type="color" name="color" class="form-control form-control-color form-control-sm"
                                                value="{{ $status->color ?? '#6c757d' }}" title="اللون">
                                        </div>
                                        <div class="col-auto">
                                            <input type="number" name="order" class="form-control form-control-sm" style="width: 70px;"
                                                value="{{ $status->order }}" min="0" title="الترتيب">
                                        </div>
                                        <div class="col-auto">
                                            <div class="form-check mb-0">
                                                <input type="checkbox" name="is_final" value="1" class="form-check-input"
                                                    id="status-final-{{ $status->id }}" {{ $status->is_final ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="status-final-{{ $status->id }}">نهائية</label>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">حفظ</button>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{ route('admin.orders.statuses.update', $status) }}" method="POST" class="d-flex gap-1 align-items-center">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="name" value="{{ $status->name }}">
                                        <input type="hidden" name="color" value="{{ $status->color ?? '#6c757d' }}">
                                        <input type="hidden" name="order" value="{{ $status->order }}">
                                        @if($status->is_final)<input type="hidden" name="is_final" value="1">@endif
                                        <select name="system_role" class="form-select form-select-sm" style="min-width: 180px;" onchange="this.form.submit()">
                                            <option value="">— بدون دور —</option>
                                            <option value="{{ \App\Models\OrderStatus::ROLE_CHECKOUT }}" {{ $status->system_role === \App\Models\OrderStatus::ROLE_CHECKOUT ? 'selected' : '' }}>
                                                طلب جديد
                                            </option>
                                            <option value="{{ \App\Models\OrderStatus::ROLE_RETURN_REFUND }}" {{ $status->system_role === \App\Models\OrderStatus::ROLE_RETURN_REFUND ? 'selected' : '' }}>
                                                مرتجع
                                            </option>
                                        </select>
                                    </form>
                                    @if($status->system_role && isset($roleLabels[$status->system_role]))
                                        <small class="text-primary d-block mt-1">{{ $roleLabels[$status->system_role] }}</small>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $ordersCount }} طلب</td>
                                <td>
                                    @if($others->isEmpty())
                                        <span class="text-muted small">—</span>
                                    @else
                                        <form action="{{ route('admin.orders.statuses.destroy', $status) }}" method="POST"
                                            class="d-flex flex-column gap-1"
                                            onsubmit="return confirm('حذف «{{ $status->name }}» ونقل {{ $ordersCount }} طلب إلى الحالة المختارة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <select name="reassign_to" class="form-select form-select-sm" required>
                                                @foreach($others as $other)
                                                    <option value="{{ $other->id }}">{{ $other->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
