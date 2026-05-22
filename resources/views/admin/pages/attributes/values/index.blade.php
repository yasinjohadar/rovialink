@extends('admin.layouts.master')

@section('page-title')
    قيم السمة: {{ $attribute->name }}
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
                <h5 class="page-title mb-0">قيم السمة: {{ $attribute->name }}</h5>
                <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">العودة للسمات</a>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h6 class="mb-0">إضافة قيمة جديدة</h6></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.attributes.values.store', $attribute) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">القيمة <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="value" required placeholder="مثال: أحمر، M">
                                </div>
                                @if($attribute->type === 'color')
                                    <div class="mb-3">
                                        <label class="form-label">كود اللون (Hex)</label>
                                        <input type="text" class="form-control" name="color_hex" placeholder="#ff0000">
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label class="form-label">الترتيب</label>
                                    <input type="number" min="0" class="form-control" name="order" value="0">
                                </div>
                                <button type="submit" class="btn btn-primary">إضافة</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h6 class="mb-0">القيم الحالية ({{ $attribute->values->count() }})</h6></div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>القيمة</th>
                                            @if($attribute->type === 'color')
                                                <th>اللون</th>
                                            @endif
                                            <th>الترتيب</th>
                                            <th>عمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($attribute->values as $val)
                                            <tr>
                                                <td>{{ $val->value }}</td>
                                                @if($attribute->type === 'color')
                                                    <td>
                                                        @if($val->color_hex)
                                                            <span class="d-inline-block rounded border" style="width:24px;height:24px;background:{{ $val->color_hex }};"></span>
                                                            <code class="ms-1">{{ $val->color_hex }}</code>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                @endif
                                                <td>{{ $val->order }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editValueModal" data-id="{{ $val->id }}" data-value="{{ $val->value }}" data-color="{{ $val->color_hex }}" data-order="{{ $val->order }}">تعديل</button>
                                                    <form action="{{ route('admin.attributes.values.destroy', [$attribute, $val]) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف هذه القيمة؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ $attribute->type === 'color' ? '4' : '3' }}" class="text-center py-4 text-muted">لا توجد قيم. أضف من النموذج على اليسار.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editValueModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="editValueForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">تعديل القيمة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">القيمة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="value" id="edit_value" required>
                        </div>
                        @if($attribute->type === 'color')
                            <div class="mb-3">
                                <label class="form-label">كود اللون (Hex)</label>
                                <input type="text" class="form-control" name="color_hex" id="edit_color_hex" placeholder="#ff0000">
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">الترتيب</label>
                            <input type="number" min="0" class="form-control" name="order" id="edit_order" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('script')
<script>
document.getElementById('editValueModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    var id = btn.getAttribute('data-id');
    var value = btn.getAttribute('data-value');
    var color = btn.getAttribute('data-color') || '';
    var order = btn.getAttribute('data-order') || '0';
    var form = document.getElementById('editValueForm');
    form.action = '{{ route("admin.attributes.values.update", [$attribute, ":id"]) }}'.replace(':id', id);
    document.getElementById('edit_value').value = value;
    document.getElementById('edit_order').value = order;
    @if($attribute->type === 'color')
    document.getElementById('edit_color_hex').value = color;
    @endif
});
</script>
@endsection
