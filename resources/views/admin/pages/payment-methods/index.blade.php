@extends('admin.layouts.master')

@section('page-title')
    وسائل الدفع
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h4 class="mb-0">وسائل الدفع</h4>
                    <p class="mb-0 text-muted">إدارة وسائل الدفع المعروضة للعميل عند إتمام الطلب (دفع عند الاستلام، تحويل بنكي، باي بال، فيزا/ماستركارد)</p>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('admin.payments.settings.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-gear me-2"></i>
                        إعدادات الدفع
                    </a>
                    <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>
                        إضافة وسيلة دفع
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>الاسم</th>
                                    <th>النوع</th>
                                    <th>الترتيب</th>
                                    <th>الحالة</th>
                                    <th>عدد المدفوعات</th>
                                    <th>عمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($methods as $method)
                                    <tr>
                                        <td><strong>{{ $method->name }}</strong></td>
                                        <td>
                                            @php
                                                $drivers = \App\Http\Controllers\Admin\PaymentMethodController::drivers();
                                                $label = $drivers[$method->driver]['label'] ?? $method->driver;
                                            @endphp
                                            {{ $label }}
                                        </td>
                                        <td>{{ $method->order }}</td>
                                        <td>
                                            @if($method->is_active)
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-secondary">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>{{ $method->payments_count ?? 0 }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="{{ route('admin.payment-methods.edit', $method) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                <form action="{{ route('admin.payment-methods.toggle-active', $method) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm {{ $method->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                                        {{ $method->is_active ? 'تعطيل' : 'تفعيل' }}
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.payment-methods.destroy', $method) }}" method="POST" class="d-inline payment-method-delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        data-payments-count="{{ (int) ($method->payments_count ?? 0) }}"
                                                        data-method-name="{{ $method->name }}">
                                                        حذف
                                                    </button>
                                                </form>
                                            </div>
                                            @if(($method->payments_count ?? 0) > 0)
                                                <small class="text-muted d-block mt-1">لا يمكن الحذف — {{ $method->payments_count }} مدفوعة مرتبطة</small>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            لا توجد وسائل دفع. <a href="{{ route('admin.payment-methods.create') }}">إضافة وسيلة دفع</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
<script>
document.querySelectorAll('.payment-method-delete-form').forEach(form => {
    form.addEventListener('submit', function (e) {
        const btn = form.querySelector('button[type="submit"]');
        const count = parseInt(btn?.dataset.paymentsCount || '0', 10);
        const name = btn?.dataset.methodName || 'وسيلة الدفع';

        if (count > 0) {
            e.preventDefault();
            alert('لا يمكن حذف «' + name + '» لوجود ' + count + ' مدفوعة مرتبطة.\n\nاستخدم «تعطيل» لإخفائها من صفحة الدفع.');
            return;
        }

        if (!confirm('هل تريد حذف «' + name + '» نهائياً؟')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
