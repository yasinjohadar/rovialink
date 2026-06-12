@extends('admin.layouts.master')

@section('page-title')
    حالة النظام
@stop

@section('css')
    @include('frontend.layouts.theme-variables')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-orders-index.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-system-status.css') }}?v=1">
@stop

@section('content')
    @php
        $storageOk = $storage['disk_ok'] && $storage['can_write_storage'] && $storage['can_write_cache'];
        $mailOk = ! $mail['missing_credentials'] || $mail['default'] === 'log';
        $hasBackups = ($backup['stats']['total'] ?? 0) > 0;
        $healthyCount = collect([
            $database['status'] === 'ok',
            $storageOk,
            $mailOk,
            ($queue['failed_jobs_count'] ?? 0) === 0,
        ])->filter()->count();
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid system-status-page my-4">
            <div class="orders-index-hero">
                <div class="orders-index-hero__top">
                    <div>
                        <h1 class="orders-index-hero__title">حالة النظام</h1>
                        <p class="orders-index-hero__subtitle">
                            نظرة سريعة على التطبيق، قاعدة البيانات، التخزين، الكاش، الطوابير، البريد، والنسخ الاحتياطية
                        </p>
                    </div>
                    <div class="orders-index-hero__actions">
                        <a href="{{ route('admin.backups.index') }}" class="btn btn-sm">
                            <i class="bi bi-cloud-arrow-up me-1"></i>
                            النسخ الاحتياطية
                        </a>
                    </div>
                </div>
                <div class="orders-index-stats">
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">البيئة</div>
                        <div class="orders-index-stat__value text-uppercase">{{ $app['env'] }}</div>
                    </div>
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">خدمات سليمة</div>
                        <div class="orders-index-stat__value">{{ $healthyCount }}/4</div>
                    </div>
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">زمن قاعدة البيانات</div>
                        <div class="orders-index-stat__value">
                            {{ $database['latency_ms'] !== null ? $database['latency_ms'].' ms' : '—' }}
                        </div>
                    </div>
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">Laravel / PHP</div>
                        <div class="orders-index-stat__value" style="font-size:0.95rem;">
                            {{ $app['laravel_version'] }} · {{ $app['php_version'] }}
                        </div>
                    </div>
                </div>
            </div>

            @if (!empty($alerts))
                <div class="system-status-alerts">
                    @foreach($alerts as $alert)
                        <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ $alert['message'] }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="row g-4">
                @include('admin.pages.system-status.partials.status-card', [
                    'icon' => 'bi-app-indicator',
                    'title' => 'التطبيق',
                    'subtitle' => 'معلومات البيئة والإصدار',
                    'badge' => 'APP',
                    'badgeTone' => 'info',
                    'rows' => [
                        ['label' => 'الاسم', 'value' => $app['name']],
                        ['label' => 'البيئة', 'value' => $app['env']],
                        ['label' => 'Debug', 'value' => $app['debug'] ? 'مفعل' : 'معطل', 'tone' => $app['debug'] ? 'warning' : 'success'],
                        ['label' => 'Laravel', 'value' => $app['laravel_version']],
                        ['label' => 'PHP', 'value' => $app['php_version']],
                        ['label' => 'المنطقة الزمنية', 'value' => $app['timezone']],
                        ['label' => 'اللغة', 'value' => $app['locale']],
                    ],
                ])

                @include('admin.pages.system-status.partials.status-card', [
                    'icon' => 'bi-database',
                    'title' => 'قاعدة البيانات',
                    'subtitle' => 'الاتصال والإعدادات الأساسية',
                    'badge' => $database['status'] === 'ok' ? 'سليم' : 'خطأ',
                    'badgeTone' => $database['status'] === 'ok' ? 'success' : 'danger',
                    'rows' => [
                        ['label' => 'الاتصال', 'value' => $database['connection']],
                        ['label' => 'Driver', 'value' => $database['driver'] ?? '—'],
                        ['label' => 'المضيف', 'value' => $database['host'] ?? '—'],
                        ['label' => 'قاعدة البيانات', 'value' => $database['database'] ?? '—'],
                        ['label' => 'الحالة', 'value' => $database['status_message'], 'tone' => $database['status'] === 'ok' ? 'success' : 'danger'],
                        ['label' => 'زمن الاستجابة', 'value' => $database['latency_ms'] !== null ? $database['latency_ms'].' ms' : 'غير متوفر'],
                    ],
                ])

                @include('admin.pages.system-status.partials.status-card', [
                    'icon' => 'bi-list-task',
                    'title' => 'الطوابير',
                    'subtitle' => 'السائق وحالة المهام',
                    'badge' => 'QUEUE',
                    'badgeTone' => 'info',
                    'rows' => [
                        ['label' => 'الاتصال', 'value' => $queue['default']],
                        ['label' => 'Driver', 'value' => $queue['driver'] ?? '—'],
                        ['label' => 'اتصال DB', 'value' => $queue['connection_name'] ?? '—'],
                        ['label' => 'الطابور', 'value' => $queue['queue'] ?? '—'],
                        ['label' => 'مهام معلّقة', 'value' => $queue['jobs_count'] !== null ? (string) $queue['jobs_count'] : 'غير متوفر'],
                        ['label' => 'مهام فاشلة', 'value' => $queue['failed_jobs_count'] !== null ? (string) $queue['failed_jobs_count'] : 'غير متوفر', 'tone' => ($queue['failed_jobs_count'] ?? 0) > 0 ? 'danger' : 'success'],
                    ],
                ])

                @include('admin.pages.system-status.partials.status-card', [
                    'icon' => 'bi-hdd',
                    'title' => 'التخزين',
                    'subtitle' => 'حالة الأقراص والصلاحيات',
                    'badge' => $storageOk ? 'سليم' : 'تحذير',
                    'badgeTone' => $storageOk ? 'success' : 'warning',
                    'rows' => [
                        ['label' => 'القرص الافتراضي', 'value' => $storage['default_disk']],
                        ['label' => 'الوصول للقرص', 'value' => $storage['disk_ok'] ? 'نعم' : 'لا', 'tone' => $storage['disk_ok'] ? 'success' : 'danger'],
                        ['label' => 'storage/', 'value' => $storage['can_write_storage'] ? 'مسموح' : 'مرفوض', 'tone' => $storage['can_write_storage'] ? 'success' : 'danger'],
                        ['label' => 'bootstrap/cache', 'value' => $storage['can_write_cache'] ? 'مسموح' : 'مرفوض', 'tone' => $storage['can_write_cache'] ? 'success' : 'danger'],
                    ],
                ])

                @include('admin.pages.system-status.partials.status-card', [
                    'icon' => 'bi-lightning-charge',
                    'title' => 'الكاش والجلسات',
                    'subtitle' => 'إعدادات التخزين المؤقت',
                    'badge' => 'CACHE',
                    'badgeTone' => 'muted',
                    'rows' => [
                        ['label' => 'Cache Store', 'value' => $cache['cache_store']],
                        ['label' => 'Session Driver', 'value' => $cache['session_driver']],
                        ['label' => 'مدة الجلسة', 'value' => $cache['session_lifetime'].' دقيقة'],
                    ],
                ])

                @include('admin.pages.system-status.partials.status-card', [
                    'icon' => 'bi-envelope',
                    'title' => 'البريد',
                    'subtitle' => 'إعدادات المرسل والناقل',
                    'badge' => $mailOk ? 'سليم' : 'تحذير',
                    'badgeTone' => $mailOk ? 'success' : 'warning',
                    'rows' => [
                        ['label' => 'Mailer', 'value' => $mail['default']],
                        ['label' => 'Transport', 'value' => $mail['transport'] ?? '—'],
                        ['label' => 'المضيف', 'value' => $mail['host'] ?? '—'],
                        ['label' => 'المنفذ', 'value' => $mail['port'] ?? '—'],
                        ['label' => 'المستخدم', 'value' => $mail['username'] ? 'مُعيَّن' : 'غير مُعين'],
                        ['label' => 'From', 'value' => $mail['from']],
                    ],
                ])

                <div class="col-12">
                    <div class="orders-index-panel">
                        <div class="orders-index-panel__head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="orders-index-panel__title mb-0">
                                <i class="bi bi-cloud-arrow-up"></i>
                                النسخ الاحتياطية
                            </h2>
                            <span class="sys-status-badge sys-status-badge--{{ $hasBackups ? 'success' : 'warning' }}">
                                {{ $hasBackups ? 'موجودة' : 'ليست هناك نسخ' }}
                            </span>
                        </div>
                        <div class="p-3 p-md-4">
                            <div class="sys-status-backup-stats">
                                <div class="sys-status-backup-stat">
                                    <div class="sys-status-backup-stat__value">{{ $backup['stats']['total'] ?? 0 }}</div>
                                    <div class="sys-status-backup-stat__label">إجمالي النسخ</div>
                                </div>
                                <div class="sys-status-backup-stat sys-status-backup-stat--success">
                                    <div class="sys-status-backup-stat__value">{{ $backup['stats']['completed'] ?? 0 }}</div>
                                    <div class="sys-status-backup-stat__label">مكتملة</div>
                                </div>
                                <div class="sys-status-backup-stat sys-status-backup-stat--danger">
                                    <div class="sys-status-backup-stat__value">{{ $backup['stats']['failed'] ?? 0 }}</div>
                                    <div class="sys-status-backup-stat__label">فاشلة</div>
                                </div>
                            </div>

                            @if($backup['latest'])
                                @php $latest = $backup['latest']; @endphp
                                <div class="sys-status-backup-latest">
                                    <h3 class="sys-status-backup-latest__title">آخر نسخة احتياطية</h3>
                                    <dl class="sys-status-dl mb-0">
                                        <div class="sys-status-dl__row">
                                            <dt>الاسم</dt>
                                            <dd>{{ $latest->name }}</dd>
                                        </div>
                                        <div class="sys-status-dl__row">
                                            <dt>النوع</dt>
                                            <dd>{{ \App\Models\Backup::BACKUP_TYPES[$latest->backup_type] ?? $latest->backup_type }}</dd>
                                        </div>
                                        <div class="sys-status-dl__row">
                                            <dt>الحالة</dt>
                                            <dd>{{ \App\Models\Backup::STATUSES[$latest->status] ?? $latest->status }}</dd>
                                        </div>
                                        <div class="sys-status-dl__row">
                                            <dt>التاريخ</dt>
                                            <dd>{{ $latest->created_at?->format('Y-m-d H:i') }}</dd>
                                        </div>
                                    </dl>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('admin.backups.index') }}" class="orders-index-view-btn">
                                        <i class="bi bi-arrow-left-short"></i>
                                        إدارة النسخ الاحتياطية
                                    </a>
                                </div>
                            @else
                                <p class="sys-status-empty-note">
                                    لا توجد نسخ احتياطية حتى الآن. يُنصح بإنشاء أول نسخة احتياطية من صفحة النسخ الاحتياطية.
                                </p>
                                <div class="mt-3">
                                    <a href="{{ route('admin.backups.index') }}" class="orders-index-view-btn">
                                        <i class="bi bi-plus-lg"></i>
                                        إنشاء نسخة احتياطية
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
