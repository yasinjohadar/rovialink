@extends('admin.layouts.master')

@section('page-title')
    معدلات الضرائب - {{ $taxClass->name }}
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
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <div>
                    <h5 class="page-title mb-1">معدلات الضرائب - {{ $taxClass->name }}</h5>
                    <p class="text-muted small mb-0">يمكنك إنشاء معدلات مختلفة لكل دولة / مدينة / رمز بريدي.</p>
                </div>
                <a href="{{ route('admin.tax.index') }}" class="btn btn-secondary">العودة لفئات الضرائب</a>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.tax.classes.rates.store', $taxClass) }}">
                        @csrf
                        <h6 class="mb-3 text-primary">إضافة معدل جديد</h6>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">الاسم</label>
                                <input type="text" name="name" class="form-control" required placeholder="مثال: ضريبة قياسية 15%">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">النسبة %</label>
                                <input type="number" name="rate" step="0.01" min="0" class="form-control" placeholder="مثال: 15" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">الدولة (ISO-2)</label>
                                <input type="text" name="country_code" maxlength="2" class="form-control" placeholder="SA">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">الولاية / المنطقة</label>
                                <input type="text" name="state" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">المدينة</label>
                                <input type="text" name="city" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">نمط الرمز البريدي</label>
                                <input type="text" name="postal_code_pattern" class="form-control" placeholder="مثال: 12* أو 12345">
                            </div>
                            <div class="col-md-2 d-flex align-items-center mt-4 pt-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">مفعّل</label>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-center mt-4 pt-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_inclusive" name="is_inclusive" value="1">
                                    <label class="form-check-label" for="is_inclusive">شامل للسعر</label>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-center mt-4 pt-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_compound" name="is_compound" value="1">
                                    <label class="form-check-label" for="is_compound">مركّب</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">الترتيب</label>
                                <input type="number" name="order" min="0" class="form-control" value="0">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary w-100">إضافة</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>النسبة</th>
                                    <th>الموقع</th>
                                    <th>شامل / مركّب</th>
                                    <th>الحالة</th>
                                    <th>الترتيب</th>
                                    <th style="width: 200px;">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($taxClass->rates as $rate)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $rate->name }}</td>
                                        <td>{{ $rate->rate * 100 }}%</td>
                                        <td>
                                            <div class="small text-muted">
                                                <div>الدولة: {{ $rate->country_code ?? 'أي' }}</div>
                                                <div>الولاية: {{ $rate->state ?? 'أي' }}</div>
                                                <div>المدينة: {{ $rate->city ?? 'أي' }}</div>
                                                <div>الرمز البريدي: {{ $rate->postal_code_pattern ?? 'أي' }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <span class="badge bg-{{ $rate->is_inclusive ? 'info' : 'secondary' }} mb-1">شامل: {{ $rate->is_inclusive ? 'نعم' : 'لا' }}</span><br>
                                                <span class="badge bg-{{ $rate->is_compound ? 'warning' : 'secondary' }}">مركّب: {{ $rate->is_compound ? 'نعم' : 'لا' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($rate->is_active)
                                                <span class="badge bg-success">مفعّل</span>
                                            @else
                                                <span class="badge bg-secondary">معطّل</span>
                                            @endif
                                        </td>
                                        <td>{{ $rate->order }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.tax.classes.rates.update', [$taxClass, $rate]) }}" class="mb-1">
                                                @csrf
                                                @method('PUT')
                                                <div class="d-flex gap-1 flex-wrap align-items-center">
                                                    <input type="number" name="rate" step="0.01" min="0" value="{{ $rate->rate * 100 }}" class="form-control form-control-sm" style="width: 80px;">
                                                    <div class="form-check form-switch small">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $rate->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label">مفعّل</label>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-primary">تحديث سريع</button>
                                                </div>
                                            </form>
                                            <form method="POST" action="{{ route('admin.tax.classes.rates.destroy', [$taxClass, $rate]) }}" onsubmit="return confirm('حذف هذا المعدل؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">لا توجد معدلات ضرائب لهذه الفئة بعد.</td>
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

