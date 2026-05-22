<div class="dashboard-section d-none" id="section-orders">
    <div class="glass-card p-4 section-fade-up">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h5 class="fw-bold text-white m-0"><i class="fas fa-box text-accent me-2"></i> جميع الطلبات</h5>
            <form method="GET" action="{{ route('frontend.account') }}" class="d-flex gap-2" id="orders-filter-form">
                <select name="status" class="form-select form-select-sm bg-glass text-white border-secondary rounded-pill" style="min-width: 160px;" onchange="this.form.submit()">
                    <option value="">جميع الحالات</option>
                    @foreach($orderStatuses as $status)
                    <option value="{{ $status->slug }}" @selected(request('status') === $status->slug)>{{ $status->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @forelse($orders as $order)
            @include('frontend.pages.account.partials.order-card', ['order' => $order])
        @empty
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-secondary opacity-50 mb-3 d-block"></i>
                <p class="text-secondary">لا توجد طلبات مطابقة.</p>
                <a href="{{ route('frontend.shop.index') }}" class="btn btn-accent rounded-pill px-4">تصفح المنتجات</a>
            </div>
        @endforelse

        @if($orders->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
