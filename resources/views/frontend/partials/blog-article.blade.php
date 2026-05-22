<article class="glass-panel p-4 p-md-5 rounded-4">
    @if($post->featured_image)
    <img src="{{ blog_image_url($post->featured_image) }}"
         alt="{{ $post->featured_image_alt ?? $post->title }}"
         class="w-100 rounded-4 mb-5"
         loading="eager">
    @endif

    <div class="blog-post-content section-fade-up">
        {!! $post->content !!}
    </div>

    <hr class="my-5 border-secondary opacity-25">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        @if($post->tags && $post->tags->count() > 0)
        <div class="d-flex flex-wrap gap-2">
            @foreach($post->tags as $tag)
            <a href="{{ route('frontend.blog.tag', $tag->slug) }}" class="badge bg-glass border border-secondary text-secondary p-2 px-3 text-decoration-none">{{ $tag->name }}</a>
            @endforeach
        </div>
        @endif
        <div class="d-flex align-items-center gap-3">
            <span class="text-secondary small">شارك المقال:</span>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="team-social-btn" aria-label="مشاركة على فيسبوك"><i class="fab fa-facebook-f"></i></a>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener noreferrer" class="team-social-btn" aria-label="مشاركة على تويتر"><i class="fab fa-twitter"></i></a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="team-social-btn" aria-label="مشاركة على لينكدإن"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </div>
</article>
