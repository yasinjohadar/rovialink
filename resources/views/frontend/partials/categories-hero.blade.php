@include('frontend.partials.page-hero', [
    'title' => 'تصنيفات المتجر',
    'subtitle' => 'اختر القسم الذي يناسب احتياجاتك واكتشف أفضل المنتجات.',
    'icon' => 'fa-th-large',
    'breadcrumbs' => [
        ['label' => 'الرئيسية', 'url' => route('frontend.home')],
        ['label' => 'التصنيفات'],
    ],
])
