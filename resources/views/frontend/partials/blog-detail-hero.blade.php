<div class="page-hero">
    <div class="page-hero-content container">
        <div class="page-hero-icon"><i class="fas fa-file-alt"></i></div>
        <h1 class="page-hero-title">{{ $post->title }}</h1>
        <div class="blog-meta justify-content-center mt-3 text-white opacity-75">
            <span><i class="far fa-user ms-1"></i> {{ $post->author->name ?? 'الإدارة' }}</span>
            <span><i class="far fa-calendar-alt ms-1"></i> {{ $post->published_at ? $post->published_at->translatedFormat('d F Y') : $post->created_at->translatedFormat('d F Y') }}</span>
            <span><i class="far fa-eye ms-1"></i> {{ number_format($post->views_count) }} مشاهدة</span>
            @if($post->reading_time)
            <span><i class="far fa-clock ms-1"></i> {{ $post->reading_time }} دقائق قراءة</span>
            @endif
        </div>
        <nav class="page-hero-breadcrumb" aria-label="breadcrumb">
            <a href="{{ route('frontend.home') }}">الرئيسية</a>
            <i class="fas fa-chevron-left sep"></i>
            <a href="{{ route('frontend.blog.index') }}">المدونة</a>
            @if($post->category)
            <i class="fas fa-chevron-left sep"></i>
            <a href="{{ route('frontend.blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
            @endif
            <i class="fas fa-chevron-left sep"></i>
            <span class="current">{{ Str::limit($post->title, 40) }}</span>
        </nav>
    </div>
    <div class="page-hero-wave">
        <svg viewBox="0 0 1440 65" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,30 C360,65 1080,0 1440,30 L1440,65 L0,65 Z" style="fill:var(--page-bg)"/>
        </svg>
    </div>
</div>
