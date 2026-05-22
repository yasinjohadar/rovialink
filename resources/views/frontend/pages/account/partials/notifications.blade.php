<div class="dashboard-section d-none" id="section-notifications">
    <div class="glass-card p-4 section-fade-up">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-white m-0"><i class="fas fa-bell text-accent me-2"></i> الإشعارات</h5>
        </div>

        @forelse($notifications as $notification)
        <div class="glass-panel p-3 mb-3 d-flex gap-3 align-items-start {{ $loop->first ? '' : '' }}" @if($loop->first) style="border-right: 3px solid var(--accent-color);" @endif>
            <div class="rounded-circle bg-accent bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width:42px;height:42px;">
                <i class="fas {{ $notification['icon'] }} {{ $notification['icon_class'] }}"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold text-white mb-1">{{ $notification['title'] }}</h6>
                <p class="text-secondary small mb-1">{{ $notification['body'] }}</p>
                <span class="text-secondary small en-text">{{ $notification['at']?->diffForHumans() }}</span>
            </div>
            @if($notification['badge'])
            <span class="badge bg-accent">{{ $notification['badge'] }}</span>
            @endif
        </div>
        @empty
        <p class="text-secondary text-center py-4 mb-0">لا توجد إشعارات حالياً.</p>
        @endforelse
    </div>
</div>
