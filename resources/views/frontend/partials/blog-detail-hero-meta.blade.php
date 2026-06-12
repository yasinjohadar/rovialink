<div class="page-hero-meta blog-meta justify-content-center">
    <span><i class="far fa-user ms-1"></i> {{ $post->author->name ?? 'الإدارة' }}</span>
    <span><i class="far fa-calendar-alt ms-1"></i> {{ $post->published_at ? $post->published_at->translatedFormat('d F Y') : $post->created_at->translatedFormat('d F Y') }}</span>
    <span><i class="far fa-eye ms-1"></i> {{ number_format($post->views_count) }} مشاهدة</span>
    @if($post->reading_time)
        <span><i class="far fa-clock ms-1"></i> {{ $post->reading_time }} دقائق قراءة</span>
    @endif
</div>
