@extends('admin.layouts.master')

@section('page-title')
    الطلبات
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
                <h5 class="page-title fs-21 mb-1">الطلبات</h5>
                <button type="button" class="btn btn-outline-primary btn-sm collapsed"
                    data-bs-toggle="collapse"
                    data-bs-target="#order-statuses-collapse"
                    aria-expanded="false"
                    aria-controls="order-statuses-collapse">
                    <i class="bi bi-tags me-1"></i> إدارة الحالات
                </button>
            </div>

            @include('admin.pages.orders.partials.statuses-manager')

            <div class="card">
                <div class="card-header">
                    <form id="orders-filter-form" action="{{ route('admin.orders.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="text" name="order_number" id="orders-order-number" class="form-control" style="width: 180px;" placeholder="رقم الطلب" value="{{ request('order_number') }}" autocomplete="off">
                        <select name="status" id="orders-status" class="form-select" style="width: 150px;">
                            <option value="">كل الحالات</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s->slug }}" {{ request('status') == $s->slug ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="from" id="orders-from" class="form-control" style="width: 150px;" value="{{ request('from') }}" title="من تاريخ">
                        <input type="date" name="to" id="orders-to" class="form-control" style="width: 150px;" value="{{ request('to') }}" title="إلى تاريخ">
                        <button type="submit" class="btn btn-secondary" id="orders-search-btn">
                            <span class="orders-search-label">بحث</span>
                            <span class="spinner-border spinner-border-sm d-none orders-search-spinner" role="status" aria-hidden="true"></span>
                        </button>
                        <button type="button" class="btn btn-danger" id="orders-clear-btn">مسح</button>
                    </form>
                </div>
                <div class="card-body position-relative" id="orders-table-container">
                    @include('admin.pages.orders.partials.table')
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
<style>
.order-status-picker.is-editing .js-order-status-badge { display: none !important; }
.order-status-picker.is-editing .js-order-status-select { display: block !important; }
.order-status-picker .js-order-status-badge { font-size: 0.85em; font-weight: 500; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let debounceTimer = null;
    let activeFetchController = null;

    const filterForm = document.getElementById('orders-filter-form');
    const tableContainer = document.getElementById('orders-table-container');
    const orderNumberInput = document.getElementById('orders-order-number');
    const searchBtn = document.getElementById('orders-search-btn');
    const clearBtn = document.getElementById('orders-clear-btn');
    const searchSpinner = searchBtn ? searchBtn.querySelector('.orders-search-spinner') : null;
    const searchLabel = searchBtn ? searchBtn.querySelector('.orders-search-label') : null;

    function setLoading(loading) {
        if (!tableContainer) return;
        tableContainer.style.opacity = loading ? '0.55' : '1';
        tableContainer.style.pointerEvents = loading ? 'none' : '';
        if (searchSpinner) searchSpinner.classList.toggle('d-none', !loading);
        if (searchLabel) searchLabel.classList.toggle('d-none', loading);
    }

    function buildFilterUrl(pageUrl) {
        if (typeof pageUrl === 'string' && pageUrl.length) {
            try {
                const parsed = new URL(pageUrl, window.location.href);
                return parsed.pathname + parsed.search;
            } catch (e) {
                return pageUrl;
            }
        }
        const params = new URLSearchParams(new FormData(filterForm));
        const url = new URL(filterForm.action, window.location.href);
        url.search = params.toString();
        return url.pathname + url.search;
    }

    function fetchOrders(pageUrl) {
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
            fetchOrders();
        });

        filterForm.querySelectorAll('select').forEach(function(select) {
            select.addEventListener('change', function() {
                fetchOrders();
            });
        });

        filterForm.querySelectorAll('input[type="date"]').forEach(function(input) {
            input.addEventListener('change', function() {
                fetchOrders();
            });
        });

        if (orderNumberInput) {
            orderNumberInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchOrders, 400);
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                filterForm.reset();
                if (orderNumberInput) orderNumberInput.value = '';
                fetchOrders();
            });
        }
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    function closeStatusPicker(picker) {
        if (!picker) return;
        picker.classList.remove('is-editing');
        const select = picker.querySelector('.js-order-status-select');
        if (select) select.classList.add('d-none');
    }

    function closeAllStatusPickers(exceptPicker) {
        if (!tableContainer) return;
        tableContainer.querySelectorAll('.order-status-picker.is-editing').forEach(function(p) {
            if (p !== exceptPicker) closeStatusPicker(p);
        });
    }

    function openStatusPicker(picker) {
        const select = picker.querySelector('.js-order-status-select');
        if (!select) return;
        closeAllStatusPickers(picker);
        picker.classList.add('is-editing');
        select.classList.remove('d-none');
        select.focus();
    }

    function updateStatusBadge(picker, status) {
        const badge = picker.querySelector('.js-order-status-badge');
        const select = picker.querySelector('.js-order-status-select');
        if (!badge) return;
        if (status.name) badge.textContent = status.name;
        if (status.color) {
            badge.style.backgroundColor = status.color;
            if (select) select.style.borderColor = status.color;
        }
        badge.title = (status.name || badge.textContent) + ' — انقر للتغيير';
    }

    if (tableContainer) {
        tableContainer.addEventListener('click', function(e) {
            const pageLink = e.target.closest('#orders-pagination a, .pagination a');
            if (pageLink && pageLink.href) {
                e.preventDefault();
                fetchOrders(pageLink.href);
                return;
            }

            const badge = e.target.closest('.js-order-status-badge');
            if (badge) {
                e.stopPropagation();
                const picker = badge.closest('.order-status-picker');
                if (picker) openStatusPicker(picker);
                return;
            }

            if (!e.target.closest('.order-status-picker')) {
                closeAllStatusPickers();
            }
        });

        tableContainer.addEventListener('change', function(e) {
            const select = e.target.closest('.js-order-status-select');
            if (!select || !select.dataset.url) return;

            const picker = select.closest('.order-status-picker');
            const previous = select.dataset.previous;
            const newStatusId = select.value;

            if (newStatusId === previous) {
                closeStatusPicker(picker);
                return;
            }

            select.disabled = true;

            const body = new URLSearchParams();
            body.append('order_status_id', newStatusId);
            if (csrfToken) body.append('_token', csrfToken.content);

            fetch(select.dataset.url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                },
                body: body,
            })
            .then(function(response) {
                return response.json().catch(function() { return {}; }).then(function(data) {
                    if (!response.ok) {
                        var msg = data.message;
                        if (data.errors) {
                            var firstKey = Object.keys(data.errors)[0];
                            if (firstKey && data.errors[firstKey][0]) msg = data.errors[firstKey][0];
                        }
                        throw new Error(msg || 'HTTP ' + response.status);
                    }
                    return data;
                });
            })
            .then(function(data) {
                select.dataset.previous = newStatusId;
                if (data.status && picker) updateStatusBadge(picker, data.status);
                closeStatusPicker(picker);
            })
            .catch(function(err) {
                select.value = previous;
                alert(err.message || 'تعذر تحديث حالة الطلب. حاول مرة أخرى.');
            })
            .finally(function() {
                select.disabled = false;
            });
        });

        tableContainer.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeAllStatusPickers();
        });
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#orders-table-container')) closeAllStatusPickers();
    });
});
</script>
@stop
