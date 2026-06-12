@include('frontend.partials.page-hero', [
    'title' => 'تصفح جميع المنتجات',
    'subtitle' => 'اكتشف مئات المنتجات الرقمية — برامج، مفاتيح، واشتراكات بتوصيل فوري.',
    'icon' => 'fa-bag-shopping',
    'breadcrumbs' => [
        ['label' => 'الرئيسية', 'url' => route('frontend.home')],
        ['label' => 'المنتجات'],
    ],
])
