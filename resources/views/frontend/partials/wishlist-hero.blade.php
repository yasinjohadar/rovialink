@include('frontend.partials.page-hero', [
    'title' => 'قائمة المفضلة',
    'subtitle' => 'احفظ المنتجات التي تعجبك وأضفها للسلة وقتما تشاء.',
    'icon' => 'fa-heart',
    'breadcrumbs' => [
        ['label' => 'الرئيسية', 'url' => route('frontend.home')],
        ['label' => 'المفضلة'],
    ],
])
