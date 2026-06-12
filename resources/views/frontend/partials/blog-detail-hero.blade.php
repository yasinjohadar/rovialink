@php
    $blogBreadcrumbs = [
        ['label' => 'الرئيسية', 'url' => route('frontend.home')],
        ['label' => 'المدونة', 'url' => route('frontend.blog.index')],
    ];
    if ($post->category) {
        $blogBreadcrumbs[] = [
            'label' => $post->category->name,
            'url' => route('frontend.blog.category', $post->category->slug),
        ];
    }
    $blogBreadcrumbs[] = ['label' => Str::limit($post->title, 40)];
@endphp

@include('frontend.partials.page-hero', [
    'title' => $post->title,
    'icon' => 'fa-file-alt',
    'breadcrumbs' => $blogBreadcrumbs,
    'metaPartial' => 'frontend.partials.blog-detail-hero-meta',
    'metaData' => ['post' => $post],
])
