<div class="col-md-4 mb-4">
    <article class="blog-card rounded-4 overflow-hidden h-100">
        <div class="blog-image-wrapper">
            @if($post->category)
            <span class="blog-category-badge">{{ $post->category->name }}</span>
            @endif
            <a href="{{ route('frontend.blog.show', $post->slug) }}">
                <img src="{{ $post->featured_image ? blog_image_url($post->featured_image) : 'https://picsum.photos/seed/blog' . $post->id . '/800/500' }}"
                     alt="{{ $post->featured_image_alt ?? $post->title }}"
                     class="w-100 h-100 object-fit-cover"
                     loading="lazy">
            </a>
        </div>
        <div class="p-4">
            <div class="blog-meta">
                <span><i class="far fa-calendar-alt"></i> {{ $post->published_at ? $post->published_at->translatedFormat('d F Y') : $post->created_at->translatedFormat('d F Y') }}</span>
                <span><i class="far fa-comment"></i> {{ $post->comments_count ?? 0 }}</span>
                @if($post->reading_time)
                <span><i class="far fa-clock"></i> {{ $post->reading_time }} د</span>
                @endif
            </div>
            <h3 class="blog-title">
                <a href="{{ route('frontend.blog.show', $post->slug) }}" class="text-white text-decoration-none">{{ $post->title }}</a>
            </h3>
            <p class="text-secondary small mb-4">{{ Str::limit(strip_tags($post->excerpt ?? ''), 120) }}</p>
            <a href="{{ route('frontend.blog.show', $post->slug) }}" class="read-more-link">اقرأ المزيد <i class="fas fa-arrow-left"></i></a>
        </div>
    </article>
</div>
