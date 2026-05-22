@extends('admin.layouts.master')

@section('page-title')
    سجل النشاط والتدقيق
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">سجل النشاط والتدقيق</h5>
                    <p class="text-muted mb-0 small">عرض من قام بماذا ومتى مع إمكانية الفلترة حسب المستخدم، النوع، والتاريخ.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <form action="{{ route('admin.activity-log.index') }}" method="GET" class="row g-2 align-items-end flex-wrap">
                        <div class="col-md-2">
                            <label class="form-label">المستخدم</label>
                            <select name="user_id" class="form-select form-select-sm">
                                <option value="">الكل</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">نوع النشاط</label>
                            <select name="log_type" class="form-select form-select-sm">
                                <option value="">الكل</option>
                                @foreach($logTypes as $type)
                                    <option value="{{ $type }}" {{ request('log_type') === $type ? 'selected' : '' }}>
                                        {{ \App\Models\ActivityLog::logTypeLabels()[$type] ?? $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">الوصف</label>
                            <input type="text" name="description" class="form-control form-control-sm" placeholder="بحث في الوصف..." value="{{ request('description') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary btn-sm">فلترة</button>
                            <a href="{{ route('admin.activity-log.index') }}" class="btn btn-outline-secondary btn-sm">مسح</a>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ والوقت</th>
                                    <th>المستخدم</th>
                                    <th>نوع النشاط</th>
                                    <th>الوصف</th>
                                    <th>الجهة (موضوع)</th>
                                    <th>عنوان IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td class="text-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            @if($log->user_id && $log->user)
                                                <a href="{{ route('admin.users.show', $log->user) }}">{{ $log->user->name }}</a>
                                                <small class="d-block text-muted">{{ $log->user->email }}</small>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $log->log_type_label }}</span>
                                        </td>
                                        <td class="small">{{ $log->description }}</td>
                                        <td class="small">
                                            @if($log->subject_type && $log->subject_id)
                                                {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="small">{{ $log->ip_address ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">لا توجد سجلات نشاط مطابقة.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $logs->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@stop
