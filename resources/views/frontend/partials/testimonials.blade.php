@php
    $testimonials = [
        [
            'name' => 'أحمد محمود',
            'role' => 'عميل مميز — الإمارات',
            'initial' => 'أ',
            'rating' => 5,
            'text' => 'تجربة تسوق رائعة! المنتجات أصلية والتوصيل كان سريعاً جداً. أنصح الجميع بالتسوق من إديو ستور.',
        ],
        [
            'name' => 'سارة خليل',
            'role' => 'عميلة دائمة — السعودية',
            'initial' => 'س',
            'rating' => 5,
            'text' => 'أسعار منافسة وجودة عالية. خدمة العملاء ممتازة وسريعة في الرد. متجر يستحق الثقة فعلاً.',
        ],
        [
            'name' => 'عمر حسن',
            'role' => 'عميل VIP — الأردن',
            'initial' => 'ع',
            'rating' => 5,
            'text' => 'أفضل متجر إلكتروني تعاملت معه. التغليف ممتاز والمنتجات تصل بحالة مثالية. شكراً إديو ستور!',
        ],
    ];
@endphp

<section class="py-5 section-fade-up testimonials-section">
    <div class="container pb-4">
        <div class="text-center mb-5">
            <h6 class="text-accent fw-bold text-uppercase tracking-wide mb-2">آراء العملاء</h6>
            <h2 class="fw-bold m-0 testimonials-section__title">ماذا يقول عملاؤنا؟</h2>
            <p class="text-secondary mt-2 max-w-lg mx-auto mb-0">آراء حقيقية من عملائنا عن تجربة التسوق والدعم والتسليم الرقمي.</p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($testimonials as $item)
                <div class="col-md-6 col-lg-4">
                    <article class="testimonial-card h-100">
                        <div class="testimonial-card__top">
                            <span class="testimonial-card__quote" aria-hidden="true">
                                <i class="fas fa-quote-right"></i>
                            </span>
                            <div class="testimonial-card__stars en-text" aria-label="{{ $item['rating'] }} من 5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= $item['rating'] ? '' : ' text-muted opacity-25' }}"></i>
                                @endfor
                            </div>
                        </div>

                        <p class="testimonial-card__text">«{{ $item['text'] }}»</p>

                        <footer class="testimonial-card__author">
                            <span class="testimonial-card__avatar" aria-hidden="true">{{ $item['initial'] }}</span>
                            <div class="testimonial-card__meta">
                                <strong class="testimonial-card__name">{{ $item['name'] }}</strong>
                                <span class="testimonial-card__role">{{ $item['role'] }}</span>
                            </div>
                        </footer>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
