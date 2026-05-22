<div class="blog-sidebar-card">
    <h5 class="fw-bold text-white mb-4">ابحث في المدونة</h5>
    <form method="GET" action="{{ route('frontend.blog.index') }}" class="input-group">
        <input type="text" name="search" class="form-control bg-transparent border-secondary text-white" placeholder="كلمة البحث..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-accent"><i class="fas fa-search"></i></button>
    </form>
</div>

@if(isset($categories) && $categories->count() > 0)
<div class="blog-sidebar-card">
    <h5 class="fw-bold text-white mb-4">التصنيفات</h5>
    <ul class="list-unstyled mb-0">
        @foreach($categories as $category)
        <li class="mb-2">
            <a href="{{ route('frontend.blog.category', $category->slug) }}" class="text-secondary text-decoration-none d-flex justify-content-between">
                <span>{{ $category->name }}</span>
                <span class="text-accent small">({{ $category->posts_count ?? 0 }})</span>
            </a>
        </li>
        @endforeach
    </ul>
</div>
@endif

@if(isset($recentPosts) && $recentPosts->count() > 0)
<div class="blog-sidebar-card">
    <h5 class="fw-bold text-white mb-4">أحدث المقالات</h5>
    @foreach($recentPosts as $recentPost)
    <a href="{{ route('frontend.blog.show', $recentPost->slug) }}" class="sidebar-post-item text-decoration-none">
        <img src="{{ $recentPost->featured_image ? blog_image_url($recentPost->featured_image) : 'https://picsum.photos/seed/recent' . $recentPost->id . '/150/150' }}"
             alt="{{ $recentPost->featured_image_alt ?? $recentPost->title }}"
             class="sidebar-post-img"
             loading="lazy">
        <div>
            <h6 class="sidebar-post-title">{{ Str::limit($recentPost->title, 50) }}</h6>
            <span class="text-secondary x-small">{{ $recentPost->published_at ? $recentPost->published_at->translatedFormat('d F Y') : $recentPost->created_at->translatedFormat('d F Y') }}</span>
        </div>
    </a>
    @endforeach
</div>
@endif

@if(isset($tags) && $tags->count() > 0)
<div class="blog-sidebar-card">
    <h5 class="fw-bold text-white mb-4">الوسوم</h5>
    <div class="d-flex flex-wrap gap-2">
        @foreach($tags as $tag)
        <a href="{{ route('frontend.blog.tag', $tag->slug) }}" class="badge bg-glass border border-secondary text-secondary p-2 px-3 text-decoration-none">{{ $tag->name }}</a>
        @endforeach
    </div>
</div>
@endif

<div class="blog-sidebar-card bg-accent text-white border-0">
    <h5 class="fw-bold mb-3">اشترك في النشرة</h5>
    <p class="small mb-4 opacity-75">احصل على آخر المقالات والكورسات مباشرة في بريدك الإلكتروني.</p>
    <input type="email" class="form-control bg-white bg-opacity-25 border-0 text-white placeholder-white mb-3" placeholder="بريدك الإلكتروني">
    <button type="button" class="btn btn-white w-100 text-accent fw-bold border-0">اشترك الآن</button>
</div>
