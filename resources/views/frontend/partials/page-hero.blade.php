@php
    $heroTitle = $title ?? '';
    $heroSubtitle = $subtitle ?? null;
    $heroIcon = $icon ?? 'fa-file';
    $heroVariant = $variant ?? null;
    $heroContainerClass = $containerClass ?? 'container';
    $heroIconClass = $iconClass ?? '';
    $showIcon = ($showIcon ?? true) && $heroIcon !== false;

    if (! isset($breadcrumbs)) {
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('frontend.home')],
        ];
        if (! empty($breadcrumbParent)) {
            $breadcrumbs[] = $breadcrumbParent;
        }
        if (! empty($breadcrumbCurrent)) {
            $breadcrumbs[] = ['label' => $breadcrumbCurrent];
        }
    }

    $heroClasses = trim('page-hero' . ($heroVariant ? ' page-hero--' . $heroVariant : ''));
@endphp

<section class="{{ $heroClasses }}">
    <div class="page-hero-content {{ $heroContainerClass }}">
        @if($showIcon)
            <div class="page-hero-icon {{ $heroIconClass }}">
                <i class="fas {{ $heroIcon }}"></i>
            </div>
        @endif

        <h1 class="page-hero-title">{{ $heroTitle }}</h1>

        @if(! empty($heroSubtitle))
            <p class="page-hero-subtitle">{{ $heroSubtitle }}</p>
        @endif

        @if(! empty($metaPartial))
            @include($metaPartial, $metaData ?? [])
        @endif

        @if(! empty($breadcrumbs))
            <nav class="page-hero-breadcrumb" aria-label="breadcrumb">
                @foreach($breadcrumbs as $index => $crumb)
                    @if($index > 0)
                        <i class="fas fa-chevron-left sep" aria-hidden="true"></i>
                    @endif
                    @if(! empty($crumb['url']))
                        <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                    @else
                        <span class="current">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif
    </div>
</section>
