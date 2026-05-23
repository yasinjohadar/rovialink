<aside class="blog-sidebar">
    <div class="blog-sidebar__panel">
        <section class="blog-sidebar__block">
            <h2 class="blog-sidebar__heading">ابحث في المدونة</h2>
            <form method="GET" action="{{ route('frontend.blog.index') }}" class="blog-sidebar__search-form" role="search">
                <label class="visually-hidden" for="sidebar-blog-search">بحث في المدونة</label>
                <div class="blog-sidebar__search">
                    <i class="fas fa-search blog-sidebar__search-icon" aria-hidden="true"></i>
                    <input type="search"
                           id="sidebar-blog-search"
                           name="search"
                           class="blog-sidebar__search-input"
                           placeholder="كلمة البحث..."
                           value="{{ request('search') }}"
                           autocomplete="off">
                    <button type="submit" class="blog-sidebar__search-btn" aria-label="بحث">
                        <i class="fas fa-search" aria-hidden="true"></i>
                    </button>
                </div>
            </form>
        </section>

        @if(isset($categories) && $categories->count() > 0)
        <section class="blog-sidebar__block">
            <h2 class="blog-sidebar__heading">التصنيفات</h2>
            <ul class="blog-sidebar__list">
                @foreach($categories as $category)
                <li>
                    <a href="{{ route('frontend.blog.category', $category->slug) }}" class="blog-sidebar__link">
                        <span>{{ $category->name }}</span>
                        <span class="blog-sidebar__count">{{ $category->posts_count ?? 0 }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </section>
        @endif

        @if(isset($recentPosts) && $recentPosts->count() > 0)
        <section class="blog-sidebar__block">
            <h2 class="blog-sidebar__heading">أحدث المقالات</h2>
            @foreach($recentPosts as $recentPost)
            <a href="{{ route('frontend.blog.show', $recentPost->slug) }}" class="blog-sidebar__post">
                <img src="{{ $recentPost->featured_image ? blog_image_url($recentPost->featured_image) : 'https://picsum.photos/seed/recent' . $recentPost->id . '/150/150' }}"
                     alt="{{ $recentPost->featured_image_alt ?? $recentPost->title }}"
                     class="blog-sidebar__post-img"
                     loading="lazy">
                <div class="blog-sidebar__post-text">
                    <h3 class="blog-sidebar__post-title">{{ Str::limit($recentPost->title, 50) }}</h3>
                    <time class="blog-sidebar__post-date">
                        {{ $recentPost->published_at ? $recentPost->published_at->translatedFormat('d F Y') : $recentPost->created_at->translatedFormat('d F Y') }}
                    </time>
                </div>
            </a>
            @endforeach
        </section>
        @endif

        @if(isset($tags) && $tags->count() > 0)
        <section class="blog-sidebar__block">
            <h2 class="blog-sidebar__heading">الوسوم</h2>
            <div class="blog-sidebar__tags">
                @foreach($tags as $tag)
                <a href="{{ route('frontend.blog.tag', $tag->slug) }}" class="blog-sidebar__tag">{{ $tag->name }}</a>
                @endforeach
            </div>
        </section>
        @endif

        <section class="blog-sidebar__block blog-sidebar__block--newsletter">
            <h2 class="blog-sidebar__heading blog-sidebar__heading--light">اشترك في النشرة</h2>
            <p class="blog-sidebar__newsletter-text">احصل على آخر المقالات والكورسات مباشرة في بريدك الإلكتروني.</p>
            <input type="email" class="blog-sidebar__newsletter-input" placeholder="بريدك الإلكتروني">
            <button type="button" class="blog-sidebar__newsletter-btn">اشترك الآن</button>
        </section>
    </div>
</aside>
