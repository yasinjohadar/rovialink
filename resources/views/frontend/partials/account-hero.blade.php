@include('frontend.partials.page-hero', [
    'title' => 'لوحة التحكم',
    'subtitle' => 'مرحباً ' . $user->name . '! تابع طلباتك وإعدادات حسابك من مكان واحد.',
    'icon' => 'fa-gauge-high',
    'containerClass' => 'container-fluid px-3 px-lg-4 px-xl-5',
    'breadcrumbs' => [
        ['label' => 'الرئيسية', 'url' => route('frontend.home')],
        ['label' => 'حسابي'],
    ],
])
