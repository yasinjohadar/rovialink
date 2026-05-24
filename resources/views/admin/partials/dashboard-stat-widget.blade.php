@php
    $variant = $widget['variant'] ?? 'violet';
    $badgeType = $widget['badge_type'] ?? 'info';
    $badgeClass = match ($badgeType) {
        'warning' => 'bg-warning-transparent text-warning',
        'danger' => 'bg-danger-transparent text-danger',
        'success' => 'bg-success-transparent text-success',
        default => 'bg-info-transparent text-info',
    };
@endphp
<div class="col-xl-3 col-lg-4 col-sm-6">
    <a href="{{ $widget['url'] }}" class="dash-widget dash-widget--{{ $variant }}">
        <div class="dash-widget__inner">
            <div class="dash-widget__top">
                <div>
                    <div class="dash-widget__label">{{ $widget['title'] }}</div>
                    <div class="dash-widget__value">{{ $widget['value'] }}</div>
                    @if(!empty($widget['subtitle']))
                        <div class="dash-widget__subtitle">{{ $widget['subtitle'] }}</div>
                    @endif
                    @if(!empty($widget['badge']))
                        <span class="dash-widget__badge {{ $badgeClass }}">{{ $widget['badge'] }}</span>
                    @endif
                </div>
                <div class="dash-widget__icon">
                    @include('admin.partials.dashboard-widget-icon', ['icon' => $widget['icon']])
                </div>
            </div>
            <div class="dash-widget__footer">
                @if(!empty($widget['trend']))
                    <span class="dash-widget__trend dash-widget__trend--{{ $widget['trend']['direction'] }}">
                        {{ $widget['trend']['label'] }}
                        <span class="opacity-75 fw-normal">أسبوعياً</span>
                    </span>
                @else
                    <span></span>
                @endif
                @if(!empty($widget['sparkline']))
                    <div class="dash-widget__sparkline" aria-hidden="true">
                        @foreach($widget['sparkline'] as $point)
                            <span style="height: {{ $point['height'] }}%"></span>
                        @endforeach
                    </div>
                @endif
            </div>
            <span class="dash-widget__arrow">
                <i class="fe fe-arrow-left"></i>
            </span>
        </div>
    </a>
</div>
