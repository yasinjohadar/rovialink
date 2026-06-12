@php
    $badgeTone = $badgeTone ?? 'muted';
    $icon = $icon ?? 'bi-hdd-stack';
@endphp
<div class="col-md-6 col-xl-4 d-flex">
    <div class="sys-status-card w-100">
        <div class="sys-status-card__head">
            <div class="sys-status-card__title-wrap">
                <span class="sys-status-card__icon" aria-hidden="true">
                    <i class="bi {{ $icon }}"></i>
                </span>
                <div>
                    <h3 class="sys-status-card__title">{{ $title }}</h3>
                    @if(!empty($subtitle))
                        <p class="sys-status-card__subtitle">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            <span class="sys-status-badge sys-status-badge--{{ $badgeTone }}">{{ $badge }}</span>
        </div>
        <div class="sys-status-card__body">
            <dl class="sys-status-dl mb-0">
                @foreach($rows as $row)
                    <div class="sys-status-dl__row">
                        <dt>{{ $row['label'] }}</dt>
                        <dd>
                            @if(!empty($row['tone']))
                                <span class="sys-status-value sys-status-value--{{ $row['tone'] }}">{{ $row['value'] }}</span>
                            @else
                                {{ $row['value'] }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>
</div>
