<article class="blog-article">
    @if($post->featured_image)
    <div class="blog-article__cover">
        <img src="{{ blog_image_url($post->featured_image) }}"
             alt="{{ $post->featured_image_alt ?? $post->title }}"
             class="blog-article__image"
             loading="eager">
    </div>
    @endif

    <div class="blog-article__body">
        <div class="blog-post-content section-fade-up">
            {!! $post->content !!}
        </div>

        <hr class="blog-article__divider">

        <div class="blog-article__footer">
            @if($post->tags && $post->tags->count() > 0)
            <div class="blog-article__tags">
                @foreach($post->tags as $tag)
                <a href="{{ route('frontend.blog.tag', $tag->slug) }}" class="blog-article__tag">{{ $tag->name }}</a>
                @endforeach
            </div>
            @endif
            <div class="blog-article__share">
                <span class="blog-article__share-label">شارك المقال:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="team-social-btn" aria-label="مشاركة على فيسبوك"><i class="fab fa-facebook-f"></i></a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener noreferrer" class="team-social-btn" aria-label="مشاركة على تويتر"><i class="fab fa-twitter"></i></a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="team-social-btn" aria-label="مشاركة على لينكدإن"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>
</article>
