@extends('admin.layouts.master')

@section('page-title')
    حالة النظام
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">حالة النظام</h5>
                    <p class="text-muted mb-0 small">نظرة سريعة على حالة التطبيق، قاعدة البيانات، التخزين، الكاش، الطوابير، البريد، والنسخ الاحتياطية.</p>
                </div>
            </div>

            @if (!empty($alerts))
                @foreach($alerts as $alert)
                    <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                        {{ $alert['message'] }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                    </div>
                @endforeach
            @endif

            <div class="row g-3">
                {{-- App / Env --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header border-0 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">التطبيق</h6>
                                <small class="text-muted">معلومات البيئة والإصدار</small>
                            </div>
                            <span class="badge bg-primary">APP</span>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0 small">
                                <li><strong>الاسم:</strong> {{ $app['name'] }}</li>
                                <li><strong>البيئة:</strong> {{ $app['env'] }}</li>
                                <li><strong>Debug:</strong> {{ $app['debug'] ? 'مفعل' : 'معطل' }}</li>
                                <li><strong>Laravel:</strong> {{ $app['laravel_version'] }}</li>
                                <li><strong>PHP:</strong> {{ $app['php_version'] }}</li>
                                <li><strong>المنطقة الزمنية:</strong> {{ $app['timezone'] }}</li>
                                <li><strong>اللغة:</strong> {{ $app['locale'] }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Database --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header border-0 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">قاعدة البيانات</h6>
                                <small class="text-muted">الاتصال والإعدادات الأساسية</small>
                            </div>
                            @php
                                $dbStatusClass = $database['status'] === 'ok' ? 'bg-success' : 'bg-danger';
                            @endphp
                            <span class="badge {{ $dbStatusClass }}">
                                {{ $database['status'] === 'ok' ? 'سليم' : 'خطأ' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0 small">
                                <li><strong>الاتصال الافتراضي:</strong> {{ $database['connection'] }}</li>
                                <li><strong>Driver:</strong> {{ $database['driver'] ?? '—' }}</li>
                                <li><strong>المضيف:</strong> {{ $database['host'] ?? '—' }}</li>
                                <li><strong>قاعدة البيانات:</strong> {{ $database['database'] ?? '—' }}</li>
                                <li><strong>الحالة:</strong> {{ $database['status_message'] }}</li>
                                <li><strong>زمن الاستجابة:</strong>
                                    {{ $database['latency_ms'] !== null ? $database['latency_ms'].' ms' : 'غير متوفر' }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Queue / Jobs --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header border-0 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">الطوابير (Queues)</h6>
                                <small class="text-muted">السائق وحالة المهام</small>
                            </div>
                            <span class="badge bg-info">QUEUE</span>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0 small">
                                <li><strong>الاتصال الافتراضي:</strong> {{ $queue['default'] }}</li>
                                <li><strong>Driver:</strong> {{ $queue['driver'] ?? '—' }}</li>
                                <li><strong>اتصال DB:</strong> {{ $queue['connection_name'] ?? '—' }}</li>
                                <li><strong>الطابور:</strong> {{ $queue['queue'] ?? '—' }}</li>
                                <li><strong>عدد المهام المعلقة:</strong>
                                    {{ $queue['jobs_count'] !== null ? $queue['jobs_count'] : 'غير متوفر' }}
                                </li>
                                <li><strong>عدد المهام الفاشلة:</strong>
                                    {{ $queue['failed_jobs_count'] !== null ? $queue['failed_jobs_count'] : 'غير متوفر' }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Storage --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header border-0 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">التخزين</h6>
                                <small class="text-muted">حالة الأقراص والصلاحيات</small>
                            </div>
                            @php
                                $storageOk = $storage['disk_ok'] && $storage['can_write_storage'] && $storage['can_write_cache'];
                            @endphp
                            <span class="badge {{ $storageOk ? 'bg-success' : 'bg-warning' }}">
                                {{ $storageOk ? 'سليم' : 'تحذير' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0 small">
                                <li><strong>القرص الافتراضي:</strong> {{ $storage['default_disk'] }}</li>
                                <li><strong>إمكانية الوصول للقرص:</strong> {{ $storage['disk_ok'] ? 'نعم' : 'لا' }}</li>
                                <li><strong>الكتابة في storage/:</strong> {{ $storage['can_write_storage'] ? 'مسموح' : 'مرفوض' }}</li>
                                <li><strong>الكتابة في bootstrap/cache:</strong> {{ $storage['can_write_cache'] ? 'مسموح' : 'مرفوض' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Cache & Session --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header border-0 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">الكاش والجلسات</h6>
                                <small class="text-muted">Drivers الإعدادات الحالية</small>
                            </div>
                            <span class="badge bg-secondary">CACHE</span>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0 small">
                                <li><strong>Cache Store:</strong> {{ $cache['cache_store'] }}</li>
                                <li><strong>Session Driver:</strong> {{ $cache['session_driver'] }}</li>
                                <li><strong>مدة الجلسة (دقائق):</strong> {{ $cache['session_lifetime'] }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Mail --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header border-0 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">البريد</h6>
                                <small class="text-muted">إعدادات المرسل والناقل</small>
                            </div>
                            @php
                                $mailOk = !$mail['missing_credentials'] || $mail['default'] === 'log';
                            @endphp
                            <span class="badge {{ $mailOk ? 'bg-success' : 'bg-warning' }}">
                                {{ $mailOk ? 'سليم' : 'تحذير' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0 small">
                                <li><strong>Mailer افتراضي:</strong> {{ $mail['default'] }}</li>
                                <li><strong>Transport:</strong> {{ $mail['transport'] ?? '—' }}</li>
                                <li><strong>المضيف:</strong> {{ $mail['host'] ?? '—' }}</li>
                                <li><strong>المنفذ:</strong> {{ $mail['port'] ?? '—' }}</li>
                                <li><strong>اسم المستخدم:</strong> {{ $mail['username'] ? 'مُعيَّن' : 'غير مُعين' }}</li>
                                <li><strong>From:</strong> {{ $mail['from'] }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Backups --}}
                <div class="col-md-12 col-xl-8">
                    <div class="card shadow-sm h-100">
                        <div class="card-header border-0 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">النسخ الاحتياطية</h6>
                                <small class="text-muted">آخر نسخة وإحصائيات سريعة</small>
                            </div>
                            @php
                                $hasBackups = ($backup['stats']['total'] ?? 0) > 0;
                            @endphp
                            <span class="badge {{ $hasBackups ? 'bg-success' : 'bg-warning' }}">
                                {{ $hasBackups ? 'موجودة' : 'ليست هناك نسخ' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="border rounded p-2 text-center">
                                        <div class="fw-bold">{{ $backup['stats']['total'] ?? 0 }}</div>
                                        <div class="text-muted small">إجمالي النسخ</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-2 text-center">
                                        <div class="fw-bold text-success">{{ $backup['stats']['completed'] ?? 0 }}</div>
                                        <div class="text-muted small">مكتملة</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-2 text-center">
                                        <div class="fw-bold text-danger">{{ $backup['stats']['failed'] ?? 0 }}</div>
                                        <div class="text-muted small">فاشلة</div>
                                    </div>
                                </div>
                            </div>

                            @if($backup['latest'])
                                @php $latest = $backup['latest']; @endphp
                                <h6 class="mb-2">آخر نسخة احتياطية</h6>
                                <ul class="list-unstyled mb-0 small">
                                    <li><strong>الاسم:</strong> {{ $latest->name }}</li>
                                    <li><strong>النوع:</strong> {{ \App\Models\Backup::BACKUP_TYPES[$latest->backup_type] ?? $latest->backup_type }}</li>
                                    <li><strong>الحالة:</strong>
                                        @php
                                            $statusLabel = \App\Models\Backup::STATUSES[$latest->status] ?? $latest->status;
                                        @endphp
                                        {{ $statusLabel }}
                                    </li>
                                    <li><strong>التاريخ:</strong> {{ $latest->created_at?->format('Y-m-d H:i') }}</li>
                                </ul>
                                <div class="mt-2">
                                    <a href="{{ route('admin.backups.index') }}" class="btn btn-outline-primary btn-sm">
                                        إدارة النسخ الاحتياطية
                                    </a>
                                </div>
                            @else
                                <p class="text-muted small mb-0">
                                    لا توجد نسخ احتياطية حتى الآن. يُنصح بإنشاء أول نسخة احتياطية من صفحة النسخ الاحتياطية.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

