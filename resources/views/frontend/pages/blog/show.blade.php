@extends('frontend.layouts.master')

@section('content')
    @include('frontend.partials.blog-detail-hero', ['post' => $post])

    <main class="container blog-detail-main py-3 py-md-4">
        @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif

        <div class="row g-3 g-lg-4">
            <div class="col-lg-8">
                @include('frontend.partials.blog-article', ['post' => $post])
                @include('frontend.partials.blog-comments', ['post' => $post, 'comments' => $comments])

                @if(isset($relatedPosts) && $relatedPosts->count() > 0)
                <div class="blog-related">
                    <h3 class="blog-related__title"><i class="fas fa-layer-group text-accent me-2"></i>مقالات ذات صلة</h3>
                    <div class="row g-3">
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
