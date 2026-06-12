@include('frontend.partials.page-hero', [
    'title' => $title ?? 'المدونة التعليمية',
    'subtitle' => $subtitle ?? null,
    'icon' => $icon ?? 'fa-blog',
    'breadcrumbParent' => $breadcrumbParent ?? null,
    'breadcrumbCurrent' => $breadcrumbCurrent ?? 'المدونة',
])
