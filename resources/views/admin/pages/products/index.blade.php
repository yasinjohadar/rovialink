@extends('admin.layouts.master')

@section('page-title')
    قائمة المنتجات
@stop

@section('css')
<style>
    #products-filter-form {
        flex-wrap: nowrap;
    }
    #products-filter-form .form-control,
    #products-filter-form .form-select {
        flex: 0 0 auto;
    }
</style>
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
                    <h5 class="page-title fs-21 mb-1">كافة المنتجات</h5>
                </div>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">إضافة منتج جديد</a>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex flex-wrap gap-3 justify-content-between">
                            <div class="flex-shrink-0 d-flex align-items-center gap-2">
                                <form id="bulk-products-form" action="{{ route('admin.products.bulk-update') }}" method="POST" class="d-flex align-items-center gap-2 flex-wrap">
                                    @csrf
                                    <select name="action" class="form-select form-select-sm" style="width: 180px;">
                                        <option value="">إجراء جماعي...</option>
                                        <option value="activate">تفعيل المنتجات المحددة</option>
                                        <option value="draft">تحويل إلى مسودة</option>
                                        <option value="hide">إخفاء من المتجر</option>
                                        <option value="delete">حذف المنتجات المحددة</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary" id="bulk-apply-btn">تطبيق</button>
                                </form>
                                <button type="button" id="btn-compare-selected" class="btn btn-sm btn-outline-success" disabled>مقارنة المنتجات</button>
                            </div>
                            <form id="products-filter-form" action="{{ route('admin.products.index') }}" method="GET"
                                class="d-flex align-items-center gap-2 flex-shrink-0">
                                <input style="width: 200px" type="text" name="query" id="products-query" class="form-control form-control-sm"
                                    placeholder="بحث بالاسم أو SKU" value="{{ request('query') }}" autocomplete="off">
                                <select name="status" id="products-status" class="form-select form-select-sm" style="width: 140px;">
                                    <option value="">كل الحالات</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>أرشيف</option>
                                </select>
                                <select name="category_id" id="products-category" class="form-select form-select-sm" style="width: 180px;">
                                    <option value="">كل التصنيفات</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ (string) request('category_id') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-secondary btn-sm" id="products-search-btn">
                                    <span class="products-search-label">بحث</span>
                                    <span class="spinner-border spinner-border-sm d-none products-search-spinner" role="status" aria-hidden="true"></span>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" id="products-clear-btn">مسح</button>
                            </form>
                        </div>
                        <div class="card-body position-relative" id="products-table-container">
                            @include('admin.pages.products.partials.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let debounceTimer = null;
    let activeFetchController = null;

    const filterForm = document.getElementById('products-filter-form');
    const tableContainer = document.getElementById('products-table-container');
    const queryInput = document.getElementById('products-query');
    const searchBtn = document.getElementById('products-search-btn');
    const clearBtn = document.getElementById('products-clear-btn');
    const searchSpinner = searchBtn ? searchBtn.querySelector('.products-search-spinner') : null;
    const searchLabel = searchBtn ? searchBtn.querySelector('.products-search-label') : null;
    const compareUrl = @json(route('admin.products.compare'));

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

    function initProductsTableControls() {
        const selectAll = document.getElementById('select-all-products');
        const compareBtn = document.getElementById('btn-compare-selected');

        function getProductCheckboxes() {
            return Array.prototype.slice.call(
                tableContainer ? tableContainer.querySelectorAll('input[name="ids[]"]') : []
            );
        }

        function updateCompareState() {
            if (!compareBtn) return;
            const selectedIds = getProductCheckboxes().filter(function(cb) { return cb.checked; }).map(function(cb) { return cb.value; });
            compareBtn.disabled = selectedIds.length < 2;
            compareBtn.dataset.selectedIds = JSON.stringify(selectedIds);
        }

        if (selectAll) {
            selectAll.onchange = function() {
                const checked = this.checked;
                getProductCheckboxes().forEach(function(cb) { cb.checked = checked; });
                updateCompareState();
            };
        }

        getProductCheckboxes().forEach(function(cb) {
            cb.onchange = updateCompareState;
        });

        updateCompareState();
    }

    function fetchProducts(pageUrl) {
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
            window.history.replaceState({}, '', buildFilterUrl(pageUrl));
            initProductsTableControls();
        })
        .catch(function(err) {
            if (err.name !== 'AbortError') {
                alert('تعذر تحميل النتائج. حاول مرة أخرى.');
            }
        })
        .finally(function() {
            setLoading(false);
        });
    }

    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchProducts();
        });

        filterForm.querySelectorAll('select').forEach(function(select) {
            select.addEventListener('change', function() {
                fetchProducts();
            });
        });

        if (queryInput) {
            queryInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchProducts, 400);
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                filterForm.reset();
                if (queryInput) queryInput.value = '';
                fetchProducts();
            });
        }
    }

    if (tableContainer) {
        tableContainer.addEventListener('click', function(e) {
            const pageLink = e.target.closest('#products-pagination a, .pagination a');
            if (pageLink && pageLink.href) {
                e.preventDefault();
                fetchProducts(pageLink.href);
            }
        });
    }

    const compareBtn = document.getElementById('btn-compare-selected');
    if (compareBtn) {
        compareBtn.addEventListener('click', function() {
            const ids = JSON.parse(this.dataset.selectedIds || '[]');
            if (!ids || ids.length < 2) return;
            const params = new URLSearchParams();
            ids.forEach(function(id) { params.append('ids[]', id); });
            window.location.href = compareUrl + '?' + params.toString();
        });
    }

    initProductsTableControls();

    const bulkForm = document.getElementById('bulk-products-form');
    if (bulkForm) {
        bulkForm.addEventListener('submit', function(e) {
            const action = bulkForm.querySelector('[name="action"]')?.value;
            const checked = tableContainer
                ? tableContainer.querySelectorAll('input[name="ids[]"]:checked').length
                : 0;

            if (!action) {
                e.preventDefault();
                alert('اختر إجراءً جماعياً من القائمة.');
                return;
            }

            if (checked === 0) {
                e.preventDefault();
                alert('حدد منتجاً واحداً على الأقل.');
                return;
            }

            if (action === 'delete') {
                if (!confirm('حذف ' + checked + ' منتج(ات)؟ لا يمكن التراجع عن هذا الإجراء.')) {
                    e.preventDefault();
                }
                return;
            }

            if (!confirm('تأكيد تنفيذ الإجراء الجماعي على المنتجات المحددة؟')) {
                e.preventDefault();
            }
        });
    }
});
</script>
@stop
