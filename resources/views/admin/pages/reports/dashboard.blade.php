@extends('admin.layouts.master')

@section('page-title')
    لوحة التقارير
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h5 class="page-title fs-21 mb-1">لوحة تقارير المتجر</h5>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{ route('admin.reports.dashboard') }}" method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">المدى الزمني</label>
                            <select name="preset" class="form-select">
                                <option value="today" {{ ($filters['preset'] ?? '') === 'today' ? 'selected' : '' }}>اليوم</option>
                                <option value="last_7_days" {{ ($filters['preset'] ?? '') === 'last_7_days' ? 'selected' : '' }}>آخر 7 أيام</option>
                                <option value="this_month" {{ ($filters['preset'] ?? '') === 'this_month' ? 'selected' : '' }}>هذا الشهر</option>
                                <option value="last_30_days" {{ ($filters['preset'] ?? '') === 'last_30_days' ? 'selected' : '' }}>آخر 30 يومًا</option>
                                <option value="custom" {{ ($filters['preset'] ?? '') === 'custom' ? 'selected' : '' }}>مخصص</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">من</label>
                            <input type="date" name="start" class="form-control" value="{{ $filters['start'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">إلى</label>
                            <input type="date" name="end" class="form-control" value="{{ $filters['end'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <p class="text-muted mb-1">إجمالي المبيعات</p>
                            <h4 class="mb-0">{{ $currencyService->format((float) ($kpis['total_sales'] ?? 0)) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <p class="text-muted mb-1">عدد الطلبات</p>
                            <h4 class="mb-0">{{ $kpis['orders_count'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <p class="text-muted mb-1">متوسط قيمة الطلب</p>
                            <h4 class="mb-0">{{ $currencyService->format((float) ($kpis['average_order_value'] ?? 0)) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <p class="text-muted mb-1">إجمالي الخصومات</p>
                            <h4 class="mb-0 text-success">-{{ $currencyService->format((float) ($kpis['total_discounts'] ?? 0)) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">منحنى المبيعات حسب اليوم (الطلبات المكتملة / الجارية)</h6>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="120"></canvas>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">أفضل المنتجات مبيعًا في الفترة</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المنتج</th>
                                            <th>SKU</th>
                                            <th>التصنيف</th>
                                            <th>الكمية المباعة</th>
                                            <th>إجمالي الإيراد</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topProducts as $row)
                                            <tr>
                                                <td>{{ $row->product_name ?? 'منتج محذوف' }}</td>
                                                <td><code>{{ $row->sku ?? '—' }}</code></td>
                                                <td>{{ $row->category_name ?? '—' }}</td>
                                                <td>{{ (int) $row->total_qty }}</td>
                                                <td>{{ $currencyService->format((float) $row->total_revenue) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">لا توجد بيانات مبيعات في الفترة المحددة.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">ملاحظات سريعة</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-1">- يتم احتساب المبيعات بناءً على الطلبات في حالة \"قيد التنفيذ\" أو \"مكتمل\" فقط.</p>
                            <p class="text-muted small mb-1">- أفضل المنتجات تعتمد على الكمية والإيراد في الفترة الزمنية المختارة أعلى الصفحة.</p>
                            <p class="text-muted small mb-0">- يمكن توسيع هذه اللوحة لاحقًا لتشمل تقارير التصنيفات والعملاء والمصادر التسويقية.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const series = @json($salesSeries ?? []);
            const labels = series.map(item => item.date);
            const totals = series.map(item => item.total);
            const counts = series.map(item => item.count);

            const ctx = document.getElementById('salesChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'إجمالي المبيعات (ر.س)',
                            data: totals,
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            tension: 0.3,
                            yAxisID: 'y',
                        },
                        {
                            label: 'عدد الطلبات',
                            data: counts,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.3,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    stacked: false,
                    plugins: {
                        legend: {
                            display: true,
                        },
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            ticks: {
                                callback: function (value) {
                                    return value.toLocaleString('ar-EG');
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                            ticks: {
                                precision: 0,
                            }
                        }
                    }
                }
            });
        })();
    </script>
@endsection

