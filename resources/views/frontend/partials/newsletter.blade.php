<section class="newsletter-block section-fade-up py-5">
    <div class="container">
        <div class="newsletter-block__card">
            <span class="elegant-card__shine" aria-hidden="true"></span>

            <div class="newsletter-block__decor" aria-hidden="true">
                <div class="newsletter-block__grid"></div>
                <div class="newsletter-block__glow newsletter-block__glow--1"></div>
                <div class="newsletter-block__glow newsletter-block__glow--2"></div>
                <span class="newsletter-block__square newsletter-block__square--1"></span>
                <span class="newsletter-block__square newsletter-block__square--2"></span>
                <span class="newsletter-block__square newsletter-block__square--3"></span>
                <span class="newsletter-block__square newsletter-block__square--4"></span>
            </div>

            <div class="row g-4 g-xl-5 align-items-center newsletter-block__row">
                <div class="col-lg-6 newsletter-block__copy text-center text-lg-start">
                    <span class="newsletter-block__badge">
                        <i class="fas fa-crown" aria-hidden="true"></i>
                        عضوية مميزة
                    </span>
                    <h2 class="newsletter-block__title">
                        انضم إلى
                        <span class="newsletter-block__title-accent">نشرتنا البريدية</span>
                    </h2>
                    <p class="newsletter-block__desc">
                        كن أول من يعرف — عروض حصرية، إطلاقات جديدة، وخصومات لا تُعرض إلا لمشتركينا.
                    </p>
                    <ul class="newsletter-block__perks list-unstyled mb-0">
                        <li>
                            <span class="newsletter-block__perk-icon"><i class="fas fa-gift" aria-hidden="true"></i></span>
                            خصومات حصرية للمشتركين فقط
                        </li>
                        <li>
                            <span class="newsletter-block__perk-icon"><i class="fas fa-rocket" aria-hidden="true"></i></span>
                            وصول مبكر لأحدث المنتجات
                        </li>
                        <li>
                            <span class="newsletter-block__perk-icon"><i class="fas fa-bell" aria-hidden="true"></i></span>
                            تنبيهات فورية بأفضل العروض
                        </li>
                    </ul>
                </div>

                <div class="col-lg-6">
                    <div class="newsletter-block__form-panel">
                        <div class="newsletter-block__icon-ring" aria-hidden="true">
                            <span class="newsletter-block__icon">
                                <i class="fas fa-envelope-open-text"></i>
                            </span>
                        </div>
                        <p class="newsletter-block__form-lead">أدخل بريدك وابدأ رحلتك معنا</p>
                        <form class="newsletter-block__form" action="#" method="post">
                            @csrf
                            <label class="visually-hidden" for="homepage-newsletter-email">البريد الإلكتروني</label>
                            <div class="newsletter-block__field">
                                <span class="newsletter-block__field-icon" aria-hidden="true">
                                    <i class="fas fa-at"></i>
                                </span>
                                <input type="email"
                                       id="homepage-newsletter-email"
                                       name="email"
                                       class="newsletter-block__input"
                                       placeholder="name@example.com"
                                       required
                                       autocomplete="email"
                                       dir="ltr">
                            </div>
                            <button type="submit" class="btn btn-accent newsletter-block__btn">
                                <span>اشترك الآن</span>
                                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                            </button>
                        </form>
                        <p class="newsletter-block__hint mb-0">
                            <i class="fas fa-shield-alt" aria-hidden="true"></i>
                            بياناتك محمية — لا رسائل مزعجة، إلغاء الاشتراك في أي وقت
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
