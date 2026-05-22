<div class="dashboard-section active" id="section-overview">
    <div class="row g-3 mb-4 section-fade-up">
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 text-center h-100">
                <div class="bg-accent bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width:48px;height:48px;">
                    <i class="fas fa-box text-accent fs-5"></i>
                </div>
                <h3 class="fw-bold text-white m-0 en-text">{{ $stats['orders_total'] }}</h3>
                <p class="text-secondary small m-0">إجمالي الطلبات</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 text-center h-100">
                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width:48px;height:48px;">
                    <i class="fas fa-spinner text-success fs-5"></i>
                </div>
                <h3 class="fw-bold text-white m-0 en-text">{{ $stats['orders_active'] }}</h3>
                <p class="text-secondary small m-0">طلبات قيد المعالجة</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 text-center h-100">
                <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width:48px;height:48px;">
                    <i class="fas fa-heart text-warning fs-5"></i>
                </div>
                <h3 class="fw-bold text-white m-0 en-text">{{ $stats['wishlist_count'] }}</h3>
                <p class="text-secondary small m-0">المفضلة</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 text-center h-100">
                <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width:48px;height:48px;">
                    <i class="fas fa-coins text-info fs-5"></i>
                </div>
                <h3 class="fw-bold text-white m-0 en-text">{{ $stats['loyalty_points'] }}</h3>
                <p class="text-secondary small m-0">نقاط المكافآت</p>
            </div>
        </div>
    </div>

    <div class="glass-card p-4 mb-4 section-fade-up">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-white m-0"><i class="fas fa-clock text-accent me-2"></i> آخر الطلبات</h5>
            <a href="#orders" class="btn btn-sm btn-outline-light rounded-pill px-3" data-section-link="orders">عرض الكل</a>
        </div>
        @if($recentOrders->isEmpty())
            <p class="text-secondary mb-0">لا توجد طلبات بعد. <a href="{{ route('frontend.shop.index') }}" class="text-accent">تصفح المنتجات</a></p>
        @else
        <div class="table-responsive">
            <table class="table table-borderless text-secondary mb-0">
                <thead>
                    <tr class="border-bottom border-secondary border-opacity-25">
                        <th class="text-secondary small pb-3">رقم الطلب</th>
                        <th class="text-secondary small pb-3">التاريخ</th>
                        <th class="text-secondary small pb-3">الحالة</th>
                        <th class="text-secondary small pb-3">الإجمالي</th>
                        <th class="text-secondary small pb-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr class="border-bottom border-secondary border-opacity-10">
                        <td class="py-3 en-text text-white fw-bold">#{{ $order->order_number }}</td>
                        <td class="py-3 en-text">{{ $order->created_at->format('Y-m-d') }}</td>
                        <td class="py-3">@include('frontend.pages.account.partials.order-status-badge', ['order' => $order])</td>
                        <td class="py-3 en-text text-accent fw-bold">{{ number_format($order->total, 2) }} ر.س</td>
                        <td class="py-3">
                            <a href="{{ route('frontend.account.orders.show', $order) }}" class="btn btn-sm btn-glass rounded-pill px-3">التفاصيل</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    @if($activeOrder)
    @php
        $currentStatusOrder = $activeOrder->status?->order ?? 0;
        $totalSteps = max(1, $statusSteps->count());
        $completedSteps = $statusSteps->filter(fn ($s) => ($s->order ?? 0) <= $currentStatusOrder)->count();
        $progressPercent = min(100, ($completedSteps / $totalSteps) * 100);
        $itemNames = $activeOrder->items->pluck('product_name')->take(2)->implode(' + ');
    @endphp
    <div class="glass-card p-4 mb-4 section-fade-up">
        <h5 class="fw-bold text-white mb-4"><i class="fas fa-route text-accent me-2"></i> حالة طلبك الحالية</h5>
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-3 mb-3 p-3 rounded-3" style="background: rgba(37,99,235,0.1);">
                    <div class="rounded-circle bg-accent d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="fas fa-download text-white"></i>
                    </div>
                    <div>
                        <p class="text-white fw-bold mb-0">طلب #{{ $activeOrder->order_number }}</p>
                        <p class="text-secondary small mb-0">{{ Str::limit($itemNames, 60) }}</p>
                    </div>
                </div>
                <div class="tracking-progress">
                    <div class="progress-line" style="width: {{ $progressPercent }}%;"></div>
                    @foreach($statusSteps->take(5) as $step)
                    @php
                        $stepOrder = $step->order ?? 0;
                        $stepClass = $stepOrder < $currentStatusOrder ? 'completed' : ($stepOrder === $currentStatusOrder ? 'active' : '');
                    @endphp
                    <div class="tracking-step {{ $stepClass }}">
                        <div class="step-icon"><i class="fas fa-{{ $stepClass === 'completed' ? 'check' : ($stepClass === 'active' ? 'spinner' : 'circle') }}"></i></div>
                        <span class="step-label">{{ $step->name }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-5 text-center mt-4 mt-md-0">
                <div class="glass-panel p-3 rounded-3">
                    <p class="text-secondary small mb-1">التسليم الرقمي</p>
                    <h4 class="fw-bold text-accent m-0 small">{{ $activeOrder->status?->name ?? 'قيد المعالجة' }}</h4>
                    <p class="text-success small mt-2 mb-0"><i class="fas fa-bolt me-1"></i> متاح فور إتمام الدفع</p>
                    <a href="{{ route('frontend.account.orders.show', $activeOrder) }}" class="btn btn-sm btn-accent rounded-pill mt-3">تفاصيل الطلب</a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-3 section-fade-up">
        <div class="col-md-4">
            <a href="#orders" class="glass-card p-4 text-center text-decoration-none d-block h-100" data-section-link="orders">
                <i class="fas fa-redo text-accent fs-3 mb-3 d-block"></i>
                <h6 class="fw-bold text-white">طلباتي</h6>
                <p class="text-secondary small m-0">أعد طلب منتجات سابقة بسهولة</p>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('frontend.shop.index') }}" class="glass-card p-4 text-center text-decoration-none d-block h-100">
                <i class="fas fa-store text-warning fs-3 mb-3 d-block"></i>
                <h6 class="fw-bold text-white">تسوق الآن</h6>
                <p class="text-secondary small m-0">اكتشف منتجاتنا الرقمية</p>
            </a>
        </div>
        <div class="col-md-4">
            <a href="#profile" class="glass-card p-4 text-center text-decoration-none d-block h-100" data-section-link="profile">
                <i class="fas fa-gift text-success fs-3 mb-3 d-block"></i>
                <h6 class="fw-bold text-white">نقاط المكافآت</h6>
                <p class="text-secondary small m-0">لديك {{ $stats['loyalty_points'] }} نقطة — {{ $loyaltyTier }}</p>
            </a>
        </div>
    </div>
</div>
