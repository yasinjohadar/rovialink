@include('frontend.partials.page-hero', [
    'title' => 'إتمام الدفع الآمن',
    'subtitle' => 'خطوة واحدة تفصلك عن استلام منتجاتك الرقمية — الدفع مشفّر وآمن.',
    'icon' => 'fa-lock',
    'breadcrumbs' => [
        ['label' => 'الرئيسية', 'url' => route('frontend.home')],
        ['label' => 'السلة', 'url' => route('frontend.cart.index')],
        ['label' => 'الدفع'],
    ],
])
