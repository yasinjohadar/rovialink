@extends('admin.layouts.master')

@section('page-title')
    قائمة التصنيفات
@stop

@section('css')
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
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة التصنيفات</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('category-create')
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">إضافة تصنيف جديد</a>
                            @endcan

                            <div class="flex-shrink-0 ms-auto">
                                <form action="{{ route('admin.categories.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input style="width: 300px" type="text" name="query" class="form-control" 
                                        placeholder="بحث بالاسم أو الوصف" value="{{ request('query') }}">

                                    <select name="status" class="form-select">
                                        <option value="">كل الحالات</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                    </select>

                                    <select name="parent_id" class="form-select">
                                        <option value="">كل التصنيفات</option>
                                        <option value="null" {{ request('parent_id') == 'null' ? 'selected' : '' }}>تصنيفات رئيسية فقط</option>
                                        @foreach($parentCategories as $parent)
                                            <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.categories.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" style="width: 40px;">#</th>
                                            <th scope="col" style="min-width: 100px;">الصورة</th>
                                            <th scope="col" style="min-width: 200px;">الاسم</th>
                                            <th scope="col" style="min-width: 150px;">الرابط</th>
                                            <th scope="col" style="min-width: 150px;">التصنيف الأب</th>
                                            <th scope="col" style="min-width: 80px;">الترتيب</th>
                                            <th scope="col" style="min-width: 100px;">الحالة</th>
                                            <th scope="col" style="min-width: 200px;">العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($categories as $category)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</th>
                                                <td>
                                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" 
                                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.categories.show', $category->id) }}" class="text-decoration-none">
                                                        {{ $category->name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <code class="text-primary">{{ $category->slug ?? '-' }}</code>
                                                </td>
                                                <td>
                                                    @if($category->parent)
                                                        <span class="badge bg-info">{{ $category->parent->name }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $category->order }}</td>
                                                <td>
                                                    @if($category->status == 'active')
                                                        <span class="badge bg-success">نشط</span>
                                                    @else
                                                        <span class="badge bg-danger">غير نشط</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('category-show')
                                                            <a href="{{ route('admin.categories.show', $category->id) }}" 
                                                                class="btn btn-sm btn-info" title="عرض">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                        @endcan
                                                        @can('category-edit')
                                                            <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                                                class="btn btn-sm btn-primary" title="تعديل">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                        @endcan
                                                        @can('category-delete')
                                                            <button type="button" class="btn btn-sm btn-danger" 
                                                                    title="حذف" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#deleteCategoryModal"
                                                                    data-category-id="{{ $category->id }}"
                                                                    data-category-name="{{ $category->name }}">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <p class="text-muted mb-0">لا توجد تصنيفات</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $categories->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Category Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10" 
                             style="width: 80px; height: 80px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#dc3545" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                            </svg>
                        </div>
                    </div>
                    <h4 class="modal-title mb-3" id="deleteCategoryModalLabel">تأكيد الحذف</h4>
                    <p class="text-muted mb-4">
                        هل أنت متأكد من حذف التصنيف 
                        <strong class="text-dark" id="categoryNameToDelete"></strong>؟
                        <br>
                        <small class="text-danger">هذا الإجراء لا يمكن التراجع عنه!</small>
                    </p>
                    <form id="deleteCategoryForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="bi bi-trash me-1"></i> حذف التصنيف
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        // Handle delete modal
        const deleteCategoryModal = document.getElementById('deleteCategoryModal');
        if (deleteCategoryModal) {
            deleteCategoryModal.addEventListener('show.bs.modal', function (event) {
                // Button that triggered the modal
                const button = event.relatedTarget;
                
                // Extract info from data-bs-* attributes
                const categoryId = button.getAttribute('data-category-id');
                const categoryName = button.getAttribute('data-category-name');
                
                // Update the modal's content
                const modalTitle = deleteCategoryModal.querySelector('#categoryNameToDelete');
                const deleteForm = deleteCategoryModal.querySelector('#deleteCategoryForm');
                
                modalTitle.textContent = categoryName;
                deleteForm.action = '{{ route("admin.categories.destroy", ":id") }}'.replace(':id', categoryId);
            });
        }
    </script>
@stop
