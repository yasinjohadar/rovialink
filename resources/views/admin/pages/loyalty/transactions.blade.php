@extends('admin.layouts.master')

@section('page-title')
    سجل حركات نقاط الولاء
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">سجل حركات نقاط الولاء</h5>
                    <p class="text-muted mb-0 small">عرض جميع حركات النقاط (كسب، استبدال، تعديل يدوي) مع إمكانية الفلترة.</p>
                </div>
                <a href="{{ route('admin.loyalty.settings.index') }}" class="btn btn-outline-primary btn-sm">إعدادات النقاط</a>
            </div>

            <div class="card">
                <div class="card-header">
                    <form action="{{ route('admin.loyalty.transactions.index') }}" method="GET" class="row g-2 align-items-end flex-wrap">
                        <div class="col-md-2">
                            <label class="form-label">العميل</label>
                            <select name="user_id" class="form-select">
                                <option value="">الكل</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">النوع</label>
                            <select name="type" class="form-select">
                                <option value="">الكل</option>
                                <option value="earn" {{ request('type') === 'earn' ? 'selected' : '' }}>كسب</option>
                                <option value="redeem" {{ request('type') === 'redeem' ? 'selected' : '' }}>استبدال</option>
                                <option value="adjust" {{ request('type') === 'adjust' ? 'selected' : '' }}>تعديل يدوي</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary">فلترة</button>
                            <a href="{{ route('admin.loyalty.transactions.index') }}" class="btn btn-outline-danger">مسح</a>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>العميل</th>
                                    <th>النوع</th>
                                    <th>القيمة</th>
                                    <th>الوصف</th>
                                    <th>الطلب</th>
                                    <th>بواسطة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $tx)
                                    <tr>
                                        <td>{{ $tx->id }}</td>
                                        <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.customers.show', $tx->user_id) }}">{{ $tx->user->name ?? '—' }}</a>
                                        </td>
                                        <td>
                                            @if($tx->type === 'earn')
                                                <span class="badge bg-success">كسب</span>
                                            @elseif($tx->type === 'redeem')
                                                <span class="badge bg-info">استبدال</span>
                                            @else
                                                <span class="badge bg-secondary">تعديل يدوي</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="{{ $tx->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $tx->amount >= 0 ? '+' : '' }}{{ $tx->amount }} نقطة
                                            </span>
                                        </td>
                                        <td class="small">{{ Str::limit($tx->description ?? '—', 40) }}</td>
                                        <td>
                                            @if($tx->order_id && $tx->order)
                                                <a href="{{ route('admin.orders.show', $tx->order) }}">{{ $tx->order->order_number }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="small">{{ $tx->createdBy->name ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">لا توجد حركات مطابقة.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $transactions->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@stop
