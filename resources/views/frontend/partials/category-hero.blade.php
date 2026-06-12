@include('frontend.partials.page-hero', [
    'title' => $category->name,
    'subtitle' => $category->description ?: 'تصفح منتجات قسم ' . $category->name,
    'icon' => 'fa-layer-group',
    'breadcrumbs' => [
        ['label' => 'الرئيسية', 'url' => route('frontend.home')],
        ['label' => 'التصنيفات', 'url' => route('frontend.categories.index')],
        ['label' => $category->name],
    ],
])
