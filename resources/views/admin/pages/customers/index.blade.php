@extends('admin.layouts.master')

@section('page-title')
    العملاء
@stop

@section('styles')
    @include('frontend.layouts.theme-variables')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-orders-index.css') }}?v=2">
@endsection

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid orders-index-page my-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                </div>
            @endif

            <div class="orders-index-hero">
                <div class="orders-index-hero__top">
                    <div>
                        <h1 class="orders-index-hero__title">العملاء</h1>
                        <p class="orders-index-hero__subtitle">إدارة قاعدة العملاء ومتابعة نشاطهم وإنفاقهم</p>
                    </div>
                </div>
                <div class="orders-index-stats">
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">نتائج البحث</div>
                        <div class="orders-index-stat__value">{{ number_format($customers->total()) }}</div>
                    </div>
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">هذه الصفحة</div>
                        <div class="orders-index-stat__value">{{ $customers->count() }}</div>
                    </div>
                    <div class="orders-index-stat">
                        <div class="orders-index-stat__label">لديهم طلبات</div>
                        <div class="orders-index-stat__value">{{ number_format($customers->sum(fn ($c) => ($c->orders_count ?? 0) > 0 ? 1 : 0)) }}</div>
                    </div>
                </div>
            </div>

            <div class="orders-index-panel">
                <div class="orders-index-panel__head">
                    <h2 class="orders-index-panel__title">
                        <i class="bi bi-funnel"></i>
                        تصفية العملاء
                    </h2>
                </div>
                <div class="p-3 p-md-4 border-bottom">
                    <form id="customers-filter-form" action="{{ route('admin.customers.index') }}" method="GET" class="orders-index-filters">
                        <div class="orders-index-filters__field">
                            <label class="form-label small fw-semibold mb-1">بحث</label>
                            <input type="text" name="search" id="customers-search" class="form-control"
                                placeholder="الاسم أو البريد أو الجوال" value="{{ request('search') }}" autocomplete="off">
                        </div>
                        <div class="orders-index-filters__field">
                            <label class="form-label small fw-semibold mb-1">حالة الطلبات</label>
                            <select name="has_orders" id="customers-has-orders" class="form-select">
                                <option value="">الكل</option>
                                <option value="1" {{ request('has_orders') === '1' ? 'selected' : '' }}>لديه طلبات</option>
                                <option value="0" {{ request('has_orders') === '0' ? 'selected' : '' }}>بدون طلبات</option>
                            </select>
                        </div>
                        <div class="orders-index-filters__field">
                            <label class="form-label small fw-semibold mb-1">إجمالي إنفاق من</label>
                            <input type="number" name="min_total" id="customers-min-total" class="form-control"
                                min="0" step="0.01" value="{{ request('min_total') }}" placeholder="0.00">
                        </div>
                        <div class="orders-index-filters__field">
                            <label class="form-label small fw-semibold mb-1">تسجيل من</label>
                            <input type="date" name="registered_from" id="customers-from" class="form-control" value="{{ request('registered_from') }}">
                        </div>
                        <div class="orders-index-filters__field">
                            <label class="form-label small fw-semibold mb-1">تسجيل إلى</label>
                            <input type="date" name="registered_to" id="customers-to" class="form-control" value="{{ request('registered_to') }}">
                        </div>
                        <div class="orders-index-filters__actions">
                            <button type="submit" class="btn btn-search" id="customers-search-btn">
                                <span class="customers-search-label"><i class="bi bi-search me-1"></i> بحث</span>
                                <span class="spinner-border spinner-border-sm d-none customers-search-spinner" role="status" aria-hidden="true"></span>
                            </button>
                            <button type="button" class="btn btn-clear" id="customers-clear-btn">
                                <i class="bi bi-x-lg me-1"></i> مسح
                            </button>
                        </div>
                    </form>
                </div>

                <div class="orders-index-table-wrap position-relative" id="customers-table-container">
                    @include('admin.pages.customers.partials.table')
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

    const filterForm = document.getElementById('customers-filter-form');
    const tableContainer = document.getElementById('customers-table-container');
    const searchInput = document.getElementById('customers-search');
    const searchBtn = document.getElementById('customers-search-btn');
    const clearBtn = document.getElementById('customers-clear-btn');
    const searchSpinner = searchBtn ? searchBtn.querySelector('.customers-search-spinner') : null;
    const searchLabel = searchBtn ? searchBtn.querySelector('.customers-search-label') : null;

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

    function fetchCustomers(pageUrl) {
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

    function copyEmail(button) {
        const email = button.dataset.email;
        if (!email) return;

        const onSuccess = function() {
            const icon = button.querySelector('i');
            if (!icon) return;
            const original = icon.className;
            icon.className = 'bi bi-check2';
            button.classList.add('is-copied');
            setTimeout(function() {
                icon.className = original;
                button.classList.remove('is-copied');
            }, 1600);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(email).then(onSuccess).catch(function() {
                fallbackCopy(email, onSuccess);
            });
        } else {
            fallbackCopy(email, onSuccess);
        }
    }

    function fallbackCopy(text, onSuccess) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            onSuccess();
        } catch (e) {
            alert('تعذر نسخ البريد الإلكتروني');
        }
        document.body.removeChild(textarea);
    }

    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchCustomers();
        });

        filterForm.querySelectorAll('select').forEach(function(select) {
            select.addEventListener('change', function() {
                fetchCustomers();
            });
        });

        filterForm.querySelectorAll('input[type="date"]').forEach(function(input) {
            input.addEventListener('change', function() {
                fetchCustomers();
            });
        });

        const minTotalInput = document.getElementById('customers-min-total');
        if (minTotalInput) {
            minTotalInput.addEventListener('change', function() {
                fetchCustomers();
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchCustomers, 400);
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                filterForm.reset();
                if (searchInput) searchInput.value = '';
                fetchCustomers();
            });
        }
    }

    if (tableContainer) {
        tableContainer.addEventListener('click', function(e) {
            const copyBtn = e.target.closest('.js-copy-email');
            if (copyBtn) {
                e.preventDefault();
                copyEmail(copyBtn);
                return;
            }

            const pageLink = e.target.closest('#customers-pagination a, .pagination a');
            if (pageLink && pageLink.href) {
                e.preventDefault();
                fetchCustomers(pageLink.href);
            }
        });
    }
});
</script>
@stop
