@extends('frontend.layouts.master')

@section('content')
    @include('frontend.partials.blog-hero', [
        'title' => $heroTitle ?? 'المدونة التعليمية',
        'subtitle' => $heroSubtitle ?? 'مقالات، أخبار، ونصائح تعليمية بأيدي خبراء في مختلف المجالات الرقمية.',
        'breadcrumbCurrent' => $breadcrumbCurrent ?? 'المدونة',
        'icon' => 'fa-blog',
    ])

    <main class="container py-5">
        @include('frontend.partials.blog-filters')

        <div class="row g-4">
            @forelse($posts as $post)
                @include('frontend.partials.blog-card', ['post' => $post])
            @empty
            <div class="col-12 text-center py-5 blog-empty-state">
                <i class="fas fa-newspaper fa-4x text-secondary opacity-50 mb-4"></i>
                <h4>لا توجد مقالات</h4>
                <p class="text-secondary">جرّب تغيير البحث أو التصنيف.</p>
                <a href="{{ route('frontend.blog.index') }}" class="btn btn-accent rounded-pill px-4 mt-2">عرض كل المقالات</a>
            </div>
            @endforelse
        </div>

        {{ $posts->withQueryString()->links('frontend.partials.pagination-catalog') }}
    </main>
@endsection
