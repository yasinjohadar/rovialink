@extends('admin.layouts.master')

@section('page-title')
    قائمة آراء العملاء
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

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">آراء العملاء</h5>
                </div>
                <div>
                    @can('review-statistics')
                        <a href="{{ route('admin.reviews.statistics') }}" class="btn btn-info btn-sm me-2">
                            <i class="bi bi-bar-chart"></i> الإحصائيات
                        </a>
                    @endcan
                    @can('review-create')
                        <a href="{{ route('admin.reviews.create') }}" class="btn btn-primary btn-sm">إضافة رأي جديد</a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            <div class="flex-shrink-0 ms-auto">
                                <form action="{{ route('admin.reviews.index') }}" method="GET" class="d-flex align-items-center flex-wrap gap-2">
                                    <select name="product_id" class="form-select" style="width: 200px;">
                                        <option value="">كل المنتجات</option>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ Str::limit($p->name, 40) }}</option>
                                        @endforeach
                                    </select>
                                    <input style="width: 250px" type="text" name="query" class="form-control"
                                        placeholder="بحث بالمستخدم أو التعليق" value="{{ request('query') }}">

                                    <select name="status" class="form-select" style="width: 150px">
                                        <option value="">كل الحالات</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                        <option value="spam" {{ request('status') == 'spam' ? 'selected' : '' }}>محتوى غير مرغوب</option>
                                    </select>

                                    <select name="rating" class="form-select" style="width: 120px">
                                        <option value="">كل التقييمات</option>
                                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 نجوم</option>
                                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 نجوم</option>
                                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 نجوم</option>
                                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 نجوم</option>
                                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 نجم</option>
                                    </select>

                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" style="width: 40px;">#</th>
                                            <th scope="col" style="min-width: 120px;">المنتج</th>
                                            <th scope="col" style="min-width: 150px;">المستخدم</th>
                                            <th scope="col" style="min-width: 100px;">التقييم</th>
                                            <th scope="col" style="min-width: 200px;">التعليق</th>
                                            <th scope="col" style="min-width: 100px;">الحالة</th>
                                            <th scope="col" style="min-width: 150px;">العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($reviews as $review)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration + ($reviews->currentPage() - 1) * $reviews->perPage() }}</th>
                                                <td>
                                                    @if($review->product)
                                                        <a href="{{ route('admin.products.edit', $review->product) }}" class="text-decoration-none">{{ Str::limit($review->product->name, 35) }}</a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($review->user)
                                                        <div>
                                                            <strong>{{ $review->user->name }}</strong>
                                                            @if($review->is_verified_purchase)
                                                                <span class="badge bg-success ms-1" title="شراء موثق">✓</span>
                                                            @endif
                                                        </div>
                                                        <small class="text-muted">{{ $review->user->email }}</small>
                                                    @else
                                                        <span class="text-muted">زائر</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-warning me-1">{{ str_repeat('★', $review->rating) }}</span>
                                                        <span class="text-muted">{{ str_repeat('☆', 5 - $review->rating) }}</span>
                                                        <span class="ms-2">({{ $review->rating }}/5)</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        @if($review->title)
                                                            <strong>{{ Str::limit($review->title, 50) }}</strong>
                                                        @endif
                                                        @if($review->comment)
                                                            <p class="mb-0 text-muted small">{{ Str::limit($review->comment, 80) }}</p>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $review->status_badge }}">{{ $review->status_text }}</span>
                                                    @if($review->is_featured)
                                                        <span class="badge bg-warning text-dark ms-1">مميز</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('review-show')
                                                            <a href="{{ route('admin.reviews.show', $review->id) }}" 
                                                                class="btn btn-sm btn-info" title="عرض">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                        @endcan
                                                        @can('review-edit')
                                                            <a href="{{ route('admin.reviews.edit', $review->id) }}" 
                                                                class="btn btn-sm btn-primary" title="تعديل">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                        @endcan
                                                        @can('review-approve')
                                                            @if($review->status != 'approved')
                                                                <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-success" title="اعتماد">
                                                                        <i class="bi bi-check-circle"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endcan
                                                        @can('review-delete')
                                                            <button type="button" class="btn btn-sm btn-danger" 
                                                                    title="حذف" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#deleteReviewModal"
                                                                    data-review-id="{{ $review->id }}">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <p class="text-muted mb-0">لا توجد آراء</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $reviews->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Review Modal -->
    <div class="modal fade" id="deleteReviewModal" tabindex="-1" aria-labelledby="deleteReviewModalLabel" aria-hidden="true">
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
                    <h4 class="modal-title mb-3" id="deleteReviewModalLabel">تأكيد الحذف</h4>
                    <p class="text-muted mb-4">
                        هل أنت متأكد من حذف هذا الرأي؟
                        <br>
                        <small class="text-danger">هذا الإجراء لا يمكن التراجع عنه!</small>
                    </p>
                    <form id="deleteReviewForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="bi bi-trash me-1"></i> حذف الرأي
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
        const deleteReviewModal = document.getElementById('deleteReviewModal');
        if (deleteReviewModal) {
            deleteReviewModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const reviewId = button.getAttribute('data-review-id');
                
                const deleteForm = deleteReviewModal.querySelector('#deleteReviewForm');
                deleteForm.action = '{{ route("admin.reviews.destroy", ":id") }}'.replace(':id', reviewId);
            });
        }
    </script>
@stop
