@extends('admin.layouts.master')

@section('page-title')
    قوالب الإيميلات
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
                    <h4 class="mb-0">قوالب الإيميلات</h4>
                    <p class="mb-0 text-muted">إدارة قوالب الإشعارات (الطلبات، المرتجعات، التسجيل، إعادة تعيين كلمة المرور)</p>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>
                        إضافة قالب
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <form action="{{ route('admin.email-templates.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="text" name="search" class="form-control" style="width: 200px;" placeholder="بحث بالاسم أو الموضوع أو المفتاح" value="{{ request('search') }}">
                        <select name="event" class="form-select" style="width: 200px;">
                            <option value="">كل الأحداث</option>
                            @foreach($events as $key => $label)
                                <option value="{{ $key }}" {{ request('event') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="locale" class="form-select" style="width: 140px;">
                            <option value="">كل اللغات</option>
                            <option value="ar" {{ request('locale') === 'ar' ? 'selected' : '' }}>العربية</option>
                            <option value="en" {{ request('locale') === 'en' ? 'selected' : '' }}>English</option>
                        </select>
                        <button type="submit" class="btn btn-secondary">فلترة</button>
                        <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-danger">مسح</a>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>المفتاح</th>
                                    <th>الاسم</th>
                                    <th>الحدث</th>
                                    <th>اللغة</th>
                                    <th>الموضوع</th>
                                    <th>الحالة</th>
                                    <th>عمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($templates as $template)
                                    <tr>
                                        <td><code>{{ $template->key }}</code></td>
                                        <td><strong>{{ $template->name }}</strong></td>
                                        <td>{{ $events[$template->event] ?? $template->event }}</td>
                                        <td>{{ $template->locale === 'ar' ? 'العربية' : 'English' }}</td>
                                        <td>{{ Str::limit($template->subject, 40) }}</td>
                                        <td>
                                            @if($template->is_active)
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-secondary">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.email-templates.edit', $template) }}" class="btn btn-sm btn-primary">تعديل</a>
                                            <form action="{{ route('admin.email-templates.destroy', $template) }}" method="POST" class="d-inline" onsubmit="return confirm('هل تريد حذف هذا القالب؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            لا توجد قوالب. <a href="{{ route('admin.email-templates.create') }}">إضافة قالب</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $templates->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@stop
