@php
    $raw = $icon ?? 'fa-star';
    $name = preg_replace('/^(fas|far|fab|fa)-/', '', $raw);
    $name = preg_replace('/^fa-/', '', $name);
    $class = $class ?? '';
@endphp
<span class="elegant-icon {{ $class }}" aria-hidden="true">
@switch($name)
    @case('cloud-arrow-down')
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.35" d="M6.5 17.5h11a3.5 3.5 0 0 0 .4-7 5.5 5.5 0 0 0-10.7 1.6A3.5 3.5 0 0 0 6.5 17.5z" fill="currentColor"/>
            <path d="M6.5 17.5h11a3.5 3.5 0 0 0 .4-7 5.5 5.5 0 0 0-10.7 1.6A3.5 3.5 0 0 0 6.5 17.5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
            <path d="M12 10.5v5.5M9.5 14l2.5 2.5L14.5 14" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('download')
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.35" d="M5 19.5h14a2 2 0 0 0 2-2v-1H3v1a2 2 0 0 0 2 2z" fill="currentColor"/>
            <path d="M12 4.5v9.5M8.5 11.5 12 15l3.5-3.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M5 19.5h14a2 2 0 0 0 2-2v-1H3v1a2 2 0 0 0 2 2z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
            <path d="M7 8.5h10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity="0.55"/>
        </svg>
        @break
    @case('shield-halved')
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.35" d="M12 3.5 5.5 6.2v5.3c0 4.1 2.8 7.9 6.5 8.8 3.7-.9 6.5-4.7 6.5-8.8V6.2L12 3.5z" fill="currentColor"/>
            <path d="M12 3.5 5.5 6.2v5.3c0 4.1 2.8 7.9 6.5 8.8 3.7-.9 6.5-4.7 6.5-8.8V6.2L12 3.5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
            <path d="M9.2 12.2 11 14l3.8-4" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('bolt')
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.4" d="M13.2 3.5 6.5 13h4.8l-1.3 7.5L17.5 11h-4.8l.5-7.5z" fill="currentColor"/>
            <path d="M13.2 3.5 6.5 13h4.8l-1.3 7.5L17.5 11h-4.8l.5-7.5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
        </svg>
        @break
    @case('headset')
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.35" d="M4.5 13.5v2.5a2 2 0 0 0 2 2h1v-6.5a6.5 6.5 0 0 1 13 0V18h1a2 2 0 0 0 2-2v-2.5" fill="currentColor"/>
            <path d="M6.5 18h-1a2 2 0 0 1-2-2v-2.5M17.5 18h1a2 2 0 0 0 2-2v-2.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            <path d="M6.5 11.5a5.5 5.5 0 0 1 11 0V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            <path d="M9.5 18v1.2a2.5 2.5 0 0 0 5 0V18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
        @break
    @case('credit-card')
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect opacity="0.35" x="3.5" y="6.5" width="17" height="11" rx="2.5" fill="currentColor"/>
            <rect x="3.5" y="6.5" width="17" height="11" rx="2.5" stroke="currentColor" stroke-width="1.6"/>
            <path d="M3.5 10.5h17" stroke="currentColor" stroke-width="1.6"/>
            <path d="M7.5 15h3.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            <circle cx="16.5" cy="15" r="1" fill="currentColor"/>
        </svg>
        @break
    @case('box')
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.35" d="M4.5 8 12 4.5 19.5 8v8L12 19.5 4.5 16V8z" fill="currentColor"/>
            <path d="M4.5 8 12 4.5 19.5 8v8L12 19.5 4.5 16V8z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
            <path d="M12 4.5v15M4.5 8l7.5 3.5L19.5 8" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
        </svg>
        @break
    @case('heart')
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.35" d="M12 20.5s-7-4.4-7-9.5a4 4 0 0 1 7-2.4 4 4 0 0 1 7 2.4c0 5.1-7 9.5-7 9.5z" fill="currentColor"/>
            <path d="M12 20.5s-7-4.4-7-9.5a4 4 0 0 1 7-2.4 4 4 0 0 1 7 2.4c0 5.1-7 9.5-7 9.5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
        </svg>
        @break
    @case('gift')
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect opacity="0.35" x="4.5" y="10.5" width="15" height="9" rx="1.5" fill="currentColor"/>
            <rect x="4.5" y="10.5" width="15" height="9" rx="1.5" stroke="currentColor" stroke-width="1.6"/>
            <path d="M12 10.5v9M4.5 14h15" stroke="currentColor" stroke-width="1.6"/>
            <path d="M8.5 10.5h7c1.4 0 2.5-1.1 2.5-2.5S16.9 5.5 15.5 5.5c-1.2 0-2.2.8-2.7 2M8.5 10.5h-2C5.1 10.5 4 9.4 4 8s1.1-2.5 2.5-2.5c1.2 0 2.2.8 2.7 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
        @break
    @case('coins')
    @case('star')
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.35" d="M12 4.5 13.8 9.2l5 .7-3.6 3.5.9 5-4.1-2.2-4.1 2.2.9-5L6.2 9.9l5-.7L12 4.5z" fill="currentColor"/>
            <path d="M12 4.5 13.8 9.2l5 .7-3.6 3.5.9 5-4.1-2.2-4.1 2.2.9-5L6.2 9.9l5-.7L12 4.5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
        </svg>
        @break
    @default
        <i class="fas fa-{{ $name }}"></i>
@endswitch
</span>
