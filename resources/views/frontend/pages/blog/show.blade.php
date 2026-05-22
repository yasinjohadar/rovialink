@extends('frontend.layouts.master')

@section('content')
    @include('frontend.partials.blog-detail-hero', ['post' => $post])

    <main class="container py-5">
        @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        <div class="row g-5">
            <div class="col-lg-8">
                @include('frontend.partials.blog-article', ['post' => $post])
                @include('frontend.partials.blog-comments', ['post' => $post, 'comments' => $comments])

                @if(isset($relatedPosts) && $relatedPosts->count() > 0)
                <div class="mt-5 pt-4">
                    <h3 class="fw-bold text-white mb-4"><i class="fas fa-layer-group text-accent me-2"></i>مقالات ذات صلة</h3>
                    <div class="row g-4">
                        @foreach($relatedPosts as $related)
                            @include('frontend.partials.blog-card', ['post' => $related])
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-4">
                @include('frontend.partials.blog-sidebar')
            </div>
        </div>
    </main>
@endsection
