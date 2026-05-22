@extends('admin.layouts.master')

@section('page-title')
    قائمة المستخدمون
@stop



@section('css')
<style>
    #users-filter-form {
        flex-wrap: nowrap;
    }
    #users-filter-form .form-select {
        width: auto;
        min-width: 11rem;
        flex: 0 0 auto;
    }
    #users-filter-form #users-per-page {
        min-width: 4.5rem;
    }
    #users-filter-form .btn,
    #users-filter-form label {
        flex: 0 0 auto;
    }
    .users-card-toolbar {
        flex-direction: row;
    }
</style>
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    @if (\Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!! \Session::get('error') !!}</li>
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة المستخدمين</h5>

                </div>


            </div>
            <!-- Page Header Close -->



            <!-- Start::row-1 -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card" data-users-toggle-url="{{ route('admin.users.toggle-status', ['id' => 0]) }}">
                        <div class="card-header align-items-center d-flex flex-wrap gap-3 justify-content-between users-card-toolbar">
                            <form id="users-filter-form" action="{{ route('admin.users.index') }}" method="GET"
                                class="d-flex align-items-center gap-2">
                                <input style="width: 300px; flex: 0 0 300px;" type="text" name="query" id="users-query" class="form-control"
                                    placeholder="بحث بالاسم أو الإيميل أو الهاتف" value="{{ request('query') }}" autocomplete="off">

                                <select name="is_active" id="users-is-active" class="form-select">
                                            <option value="">كل الحالات النشطة</option>
                                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                                        </select>

                                        <select name="status" id="users-status" class="form-select">
                                            <option value="">كل الحالات</option>
                                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>مفعل</option>
                                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>موقوف</option>
                                            <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>محظور</option>
                                        </select>

                                        <label class="text-nowrap text-muted small align-self-center mb-0">عرض:</label>
                                        <select name="per_page" id="users-per-page" class="form-select" style="width: auto;" title="عدد العناصر في الصفحة">
                                            <option value="10" {{ (int) request('per_page', 10) === 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ (int) request('per_page') === 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ (int) request('per_page') === 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ (int) request('per_page') === 100 ? 'selected' : '' }}>100</option>
                                        </select>

                                        <button type="submit" class="btn btn-secondary" id="users-search-btn">
                                            <span class="users-search-label">بحث</span>
                                            <span class="spinner-border spinner-border-sm d-none users-search-spinner" role="status" aria-hidden="true"></span>
                                        </button>
                                <button type="button" class="btn btn-danger" id="users-clear-btn">مسح</button>
                            </form>

                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm flex-shrink-0">إنشاء مستخدم جديد</a>
                        </div>


                        <div class="card-body">
                            <div id="users-table-container" class="position-relative">
                                @include('admin.pages.users.partials.table')
                            </div>
                        </div><!-- end card-body -->
                    </div><!-- end card -->
                </div>
            </div>
            <!--End::row-1 -->


        </div>
    </div>
    <!-- End::app-content -->

    <!-- مودال تأكيد تغيير الحالة النشطة -->
    <div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="toggleStatusModalLabel">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>تأكيد تغيير الحالة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body pt-0">
                    <p id="toggleStatusModalMessage" class="mb-0"></p>
                    <p class="text-muted small mb-0 mt-2">
                        <i class="bi bi-info-circle me-1"></i>سيتم تحديث حالة المستخدم فور التأكيد.
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" id="toggleStatusModalConfirm">تأكيد</button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let pendingToggleButton = null;
    let debounceTimer = null;
    let activeFetchController = null;

    const modal = document.getElementById('toggleStatusModal');
    const modalMessage = document.getElementById('toggleStatusModalMessage');
    const modalConfirmBtn = document.getElementById('toggleStatusModalConfirm');
    const filterForm = document.getElementById('users-filter-form');
    const tableContainer = document.getElementById('users-table-container');
    const queryInput = document.getElementById('users-query');
    const searchBtn = document.getElementById('users-search-btn');
    const clearBtn = document.getElementById('users-clear-btn');
    const searchSpinner = searchBtn ? searchBtn.querySelector('.users-search-spinner') : null;
    const searchLabel = searchBtn ? searchBtn.querySelector('.users-search-label') : null;

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show';
        alertDiv.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>';
        const container = document.querySelector('.main-content');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
        } else {
            document.body.insertBefore(alertDiv, document.body.firstChild);
        }
        setTimeout(function() {
            if (alertDiv.parentNode) alertDiv.remove();
        }, 3000);
    }

    function setLoading(loading) {
        if (!tableContainer) return;
        tableContainer.style.opacity = loading ? '0.55' : '1';
        tableContainer.style.pointerEvents = loading ? 'none' : '';
        if (searchSpinner) searchSpinner.classList.toggle('d-none', !loading);
        if (searchLabel) searchLabel.classList.toggle('d-none', loading);
    }

    function buildFilterUrl(pageUrl) {
        if (pageUrl) return pageUrl;
        const params = new URLSearchParams(new FormData(filterForm));
        return filterForm.action + '?' + params.toString();
    }

    function fetchUsers(pageUrl) {
        if (!filterForm || !tableContainer) return;

        if (activeFetchController) activeFetchController.abort();
        activeFetchController = new AbortController();

        setLoading(true);

        fetch(buildFilterUrl(pageUrl), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            signal: activeFetchController.signal,
        })
        .then(function(response) {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(function(data) {
            tableContainer.innerHTML = data.html;
            const url = buildFilterUrl(pageUrl);
            window.history.replaceState({}, '', url);
        })
        .catch(function(err) {
            if (err.name !== 'AbortError') {
                showAlert('تعذر تحميل النتائج. حاول مرة أخرى.', 'error');
            }
        })
        .finally(function() {
            setLoading(false);
        });
    }

    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchUsers();
        });

        filterForm.querySelectorAll('select').forEach(function(select) {
            select.addEventListener('change', function() {
                fetchUsers();
            });
        });

        if (queryInput) {
            queryInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    fetchUsers();
                }, 400);
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                filterForm.reset();
                if (queryInput) queryInput.value = '';
                const perPage = document.getElementById('users-per-page');
                if (perPage) perPage.value = '10';
                fetchUsers();
            });
        }
    }

    if (tableContainer) {
        tableContainer.addEventListener('click', function(e) {
            const pageLink = e.target.closest('#users-pagination a, .pagination a');
            if (pageLink && pageLink.href) {
                e.preventDefault();
                fetchUsers(pageLink.href);
            }
        });
    }

    function buildToggleUrl(userId) {
        const card = document.querySelector('[data-users-toggle-url]');
        const baseUrl = card ? card.getAttribute('data-users-toggle-url') : '';
        return baseUrl ? baseUrl.replace(/\/0\/toggle-status/, '/' + userId + '/toggle-status') : '/admin/users/' + userId + '/toggle-status';
    }

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-toggle-status');
        if (!btn || !tableContainer || !tableContainer.contains(btn)) return;

        const userId = btn.getAttribute('data-user-id');
        const userName = btn.getAttribute('data-user-name') || '';
        const isActive = btn.getAttribute('data-is-active') === '1';
        if (!userId) return;

        pendingToggleButton = btn;
        if (isActive) {
            modalMessage.textContent = 'هل أنت متأكد من إلغاء تفعيل المستخدم «' + userName + '»؟ لن يتمكّن من تسجيل الدخول حتى إعادة التفعيل.';
        } else {
            modalMessage.textContent = 'هل أنت متأكد من تفعيل المستخدم «' + userName + '»؟';
        }
        bootstrap.Modal.getOrCreateInstance(modal).show();
    });

    modalConfirmBtn.addEventListener('click', function() {
        if (!pendingToggleButton) return;
        const userId = pendingToggleButton.getAttribute('data-user-id');
        const url = buildToggleUrl(userId);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        modalConfirmBtn.disabled = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(function(response) {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                const isActive = Boolean(data.is_active);
                pendingToggleButton.textContent = isActive ? 'نشط' : 'غير نشط';
                pendingToggleButton.setAttribute('data-is-active', isActive ? '1' : '0');
                pendingToggleButton.classList.remove('btn-success', 'btn-danger');
                pendingToggleButton.classList.add(isActive ? 'btn-success' : 'btn-danger');
                pendingToggleButton.title = isActive ? 'إلغاء التفعيل' : 'تفعيل';
                showAlert(data.message || 'تم تحديث حالة المستخدم بنجاح', 'success');
                bootstrap.Modal.getInstance(modal).hide();
            } else {
                showAlert(data.message || 'حدث خطأ', 'error');
            }
        })
        .catch(function(err) {
            showAlert('حدث خطأ أثناء تحديث حالة المستخدم.', 'error');
        })
        .finally(function() {
            modalConfirmBtn.disabled = false;
            pendingToggleButton = null;
        });
    });
});
</script>
@stop
