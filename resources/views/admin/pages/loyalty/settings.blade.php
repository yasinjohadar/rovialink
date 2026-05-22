@extends('admin.layouts.master')

@section('page-title')
    إعدادات نقاط الولاء
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">إعدادات نقاط الولاء</h5>
                    <p class="text-muted mb-0 small">تحديد قواعد منح النقاط عند إكمال الطلب وقواعد استبدال النقاط كخصم.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">قواعد النقاط والاستبدال</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.loyalty.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">نقاط لكل وحدة عملة (ر.س)</label>
                                <input type="number" min="0" step="1" class="form-control @error('loyalty_points_per_currency') is-invalid @enderror"
                                    name="loyalty_points_per_currency" value="{{ old('loyalty_points_per_currency', $settings['loyalty_points_per_currency'] ?? 1)">
                                <small class="text-muted">مثال: 1 = نقطة واحدة لكل 1 ر.س من إجمالي الطلب المكتمل</small>
                                @error('loyalty_points_per_currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">كل X نقطة = 1 ر.س خصم (نسبة الاستبدال)</label>
                                <input type="number" min="1" step="1" class="form-control @error('loyalty_redemption_rate') is-invalid @enderror"
                                    name="loyalty_redemption_rate" value="{{ old('loyalty_redemption_rate', $settings['loyalty_redemption_rate'] ?? 100)">
                                <small class="text-muted">مثال: 100 = 100 نقطة تعطي 1 ر.س خصم</small>
                                @error('loyalty_redemption_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الحد الأدنى لمبلغ الطلب لاستخدام النقاط (ر.س)</label>
                                <input type="number" min="0" step="0.01" class="form-control @error('loyalty_min_order_to_redeem') is-invalid @enderror"
                                    name="loyalty_min_order_to_redeem" value="{{ old('loyalty_min_order_to_redeem', $settings['loyalty_min_order_to_redeem'] ?? 0)">
                                @error('loyalty_min_order_to_redeem')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الحد الأقصى لنقاط الاستبدال في طلب واحد</label>
                                <input type="number" min="0" step="1" class="form-control @error('loyalty_max_points_per_order') is-invalid @enderror"
                                    name="loyalty_max_points_per_order" value="{{ old('loyalty_max_points_per_order', $settings['loyalty_max_points_per_order'] ?? 5000)">
                                @error('loyalty_max_points_per_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">منح النقاط عند وصول الطلب إلى الحالة</label>
                                <select name="loyalty_award_on_status" class="form-select @error('loyalty_award_on_status') is-invalid @enderror">
                                    @foreach($orderStatuses as $status)
                                        <option value="{{ $status->slug }}" {{ old('loyalty_award_on_status', $settings['loyalty_award_on_status'] ?? 'completed') === $status->slug ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">عند تغيير حالة الطلب إلى هذه الحالة يُمنح العميل النقاط تلقائياً</small>
                                @error('loyalty_award_on_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">العودة للعملاء</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
