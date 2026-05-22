<div class="page-hero">
    <div class="page-hero-content container">
        <div class="page-hero-icon"><i class="fas fa-layer-group"></i></div>
        <h1 class="page-hero-title">{{ $category->name }}</h1>
        @if($category->description)
            <p class="page-hero-subtitle">{{ $category->description }}</p>
        @else
            <p class="page-hero-subtitle">تصفح منتجات قسم {{ $category->name }}</p>
        @endif
        <nav class="page-hero-breadcrumb" aria-label="breadcrumb">
            <a href="{{ route('frontend.home') }}">الرئيسية</a>
            <i class="fas fa-chevron-left sep"></i>
            <a href="{{ route('frontend.categories.index') }}">التصنيفات</a>
            <i class="fas fa-chevron-left sep"></i>
            <span class="current">{{ $category->name }}</span>
        </nav>
    </div>
    <div class="page-hero-wave">
        <svg viewBox="0 0 1440 65" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,30 C360,65 1080,0 1440,30 L1440,65 L0,65 Z" style="fill:var(--page-bg)"/>
        </svg>
    </div>
</div>
