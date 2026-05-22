<section class="py-5 section-fade-up">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h6 class="text-accent fw-bold text-uppercase tracking-wide mb-2">آخر الأخبار</h6>
                <h2 class="fw-bold m-0">من مدونتنا</h2>
            </div>
            <a href="{{ route('frontend.blog.index') }}" class="btn btn-outline-light rounded-pill px-4 d-none d-md-block">جميع المقالات</a>
        </div>

        <div class="swiper blog-swiper position-relative pb-5">
            <div class="swiper-wrapper">
                @forelse($blogPosts as $post)
                <div class="swiper-slide">
                    <article class="blog-card rounded-4 overflow-hidden h-100">
                        <div class="blog-image-wrapper">
                            @if($post->category)
                            <span class="blog-category-badge">{{ $post->category->name }}</span>
                            @endif
                            @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-100 h-100 object-fit-cover">
                            @else
                            <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="{{ $post->title }}" class="w-100 h-100 object-fit-cover">
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="blog-meta">
                                <span><i class="far fa-calendar-alt"></i> {{ $post->published_at ? $post->published_at->format('d F Y') : 'غير محدد' }}</span>
                                <span><i class="far fa-comment"></i> {{ $post->comments_count ?? 0 }}</span>
                            </div>
                            <h3 class="blog-title">{{ $post->title }}</h3>
                            <p class="text-secondary small mb-4">{{ Str::limit(strip_tags($post->content), 100) }}</p>
                            <a href="{{ route('frontend.blog.show', $post->slug) }}" class="read-more-link">اقرأ المزيد <i class="fas fa-arrow-left"></i></a>
                        </div>
                    </article>
                </div>
                @empty
                <div class="swiper-slide">
                    <div class="text-center py-5">
                        <i class="fas fa-newspaper fa-4x text-secondary opacity-50 mb-3"></i>
                        <h5 class="text-white">لا توجد مقالات حالياً</h5>
                    </div>
                </div>
                @endforelse
            </div>

            <div class="swiper-pagination blog-pagination mt-4"></div>
            <div class="swiper-button-next blog-next"></div>
            <div class="swiper-button-prev blog-prev"></div>
        </div>

        <div class="text-center mt-4 d-md-none">
            <a href="{{ route('frontend.blog.index') }}" class="btn btn-outline-light rounded-pill px-4">جميع المقالات</a>
        </div>
    </div>
</section>
