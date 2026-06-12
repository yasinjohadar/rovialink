@include('frontend.partials.page-hero', [
    'title' => 'سلة المشتريات',
    'subtitle' => 'راجع منتجاتك الرقمية وأكمل عملية الدفع بكل أمان وسهولة.',
    'icon' => 'fa-shopping-cart',
    'breadcrumbs' => [
        ['label' => 'الرئيسية', 'url' => route('frontend.home')],
        ['label' => 'السلة'],
    ],
])
