@php
    $status = $order->status;
    $color = $status?->color ?? '#6c757d';
    $isLight = in_array(strtolower($color), ['#ffc107', '#ffff00', 'yellow', 'warning'], true);
@endphp
<span class="badge px-3 py-2 {{ $isLight ? 'text-dark' : 'text-white' }}" style="background-color: {{ $color }};">
    {{ $status?->name ?? 'غير محدد' }}
</span>
