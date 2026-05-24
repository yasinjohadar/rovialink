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
<div class="col-xl-3 col-lg-4 col-sm-6 d-flex">
    <a href="{{ $widget['url'] }}" class="dash-widget dash-widget--{{ $variant }} w-100">
        <div class="dash-widget__inner">
            <div class="dash-widget__top">
                <div class="dash-widget__meta">
                    <div class="dash-widget__label">{{ $widget['title'] }}</div>
                    <div class="dash-widget__value" title="{{ $widget['value'] }}">{{ $widget['value'] }}</div>
                    <div class="dash-widget__subtitle">{{ $widget['subtitle'] ?? ' ' }}</div>
                    <div class="dash-widget__badge-wrap">
                        @if(!empty($widget['badge']))
                            <span class="dash-widget__badge {{ $badgeClass }}">{{ $widget['badge'] }}</span>
                        @endif
                    </div>
                </div>
                <div class="dash-widget__icon">
                    @include('admin.partials.dashboard-widget-icon', ['icon' => $widget['icon']])
                </div>
            </div>
            <div class="dash-widget__footer">
                <div class="dash-widget__footer-start">
                    @if(!empty($widget['trend']))
                        <span class="dash-widget__trend dash-widget__trend--{{ $widget['trend']['direction'] }}">
                            {{ $widget['trend']['label'] }}
                            <span class="opacity-75 fw-normal">أسبوعياً</span>
                        </span>
                    @endif
                </div>
                <div class="dash-widget__sparkline-slot" aria-hidden="true">
                    @if(!empty($widget['sparkline']))
                        <div class="dash-widget__sparkline">
                            @foreach($widget['sparkline'] as $point)
                                <span style="height: {{ $point['height'] }}%"></span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <span class="dash-widget__arrow">
                <i class="fe fe-arrow-left"></i>
            </span>
        </div>
    </a>
</div>
