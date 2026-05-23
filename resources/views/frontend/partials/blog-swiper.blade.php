<section class="py-5 section-fade-up">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h6 class="text-accent fw-bold text-uppercase tracking-wide mb-2">آخر الأخبار</h6>
                <h2 class="fw-bold m-0">من مدونتنا</h2>
            </div>
            <a href="{{ route('frontend.blog.index') }}" class="btn btn-outline-light rounded-pill px-4 d-none d-md-block">جميع المقالات</a>
        </div>

        <div class="swiper blog-swiper home-catalog-swiper position-relative">
            <div class="swiper-wrapper">
                @forelse($blogPosts as $post)
                <div class="swiper-slide h-auto">
                    @include('frontend.partials.blog-card', ['post' => $post, 'inSwiper' => true])
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

            <div class="swiper-pagination home-catalog-pagination blog-pagination"></div>
            <div class="swiper-button-next home-catalog-next blog-next"></div>
            <div class="swiper-button-prev home-catalog-prev blog-prev"></div>
        </div>

        <div class="text-center mt-4 d-md-none">
            <a href="{{ route('frontend.blog.index') }}" class="btn btn-outline-light rounded-pill px-4">جميع المقالات</a>
        </div>
    </div>
</section>
