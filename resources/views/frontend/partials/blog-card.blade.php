@php
    $colClass = $colClass ?? 'col-md-4';
    $inSwiper = !empty($inSwiper);
    $postUrl = route('frontend.blog.show', $post->slug);
    $publishedLabel = $post->published_at
        ? $post->published_at->translatedFormat('d F Y')
        : $post->created_at->translatedFormat('d F Y');
@endphp
@if(!$inSwiper)
<div class="{{ $colClass }}">
@endif
    <article class="blog-card h-100">
        <div class="blog-card__media">
            @if($post->category)
            <span class="blog-card__category">{{ $post->category->name }}</span>
            @endif
            <a href="{{ $postUrl }}" class="blog-card__media-link" tabindex="-1" aria-hidden="true">
                <img src="{{ $post->featured_image ? blog_image_url($post->featured_image) : 'https://picsum.photos/seed/blog' . $post->id . '/800/500' }}"
                     alt="{{ $post->featured_image_alt ?? $post->title }}"
                     class="blog-card__image"
                     loading="lazy">
            </a>
        </div>
        <div class="blog-card__body">
            <ul class="blog-card__meta">
                <li>
                    <i class="far fa-calendar-alt" aria-hidden="true"></i>
                    <span>{{ $publishedLabel }}</span>
                </li>
                <li>
                    <i class="far fa-comment" aria-hidden="true"></i>
                    <span>{{ $post->comments_count ?? 0 }}</span>
                </li>
                @if($post->reading_time)
                <li>
                    <i class="far fa-clock" aria-hidden="true"></i>
                    <span>{{ $post->reading_time }} د</span>
                </li>
                @endif
            </ul>
            <h3 class="blog-card__title">
                <a href="{{ $postUrl }}">{{ $post->title }}</a>
            </h3>
            <p class="blog-card__excerpt">{{ Str::limit(strip_tags($post->excerpt ?? ''), 120) }}</p>
            <a href="{{ $postUrl }}" class="blog-card__cta">
                اقرأ المزيد
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
            </a>
        </div>
    </article>
@if(!$inSwiper)
</div>
@endif
