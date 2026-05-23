<section class="newsletter-block section-fade-up py-5">
    <div class="container pb-5">
        <div class="newsletter-block__card">
            <div class="newsletter-block__glow" aria-hidden="true"></div>
            <div class="newsletter-block__inner">
                <div class="newsletter-block__icon" aria-hidden="true">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <h2 class="newsletter-block__title">انضم لنشرتنا البريدية</h2>
                <p class="newsletter-block__desc">اشترك ليصلك آخر العروض والخصومات الحصرية مباشرة إلى بريدك.</p>
                <form class="newsletter-block__form" action="#" method="post">
                    @csrf
                    <label class="visually-hidden" for="homepage-newsletter-email">البريد الإلكتروني</label>
                    <input type="email"
                           id="homepage-newsletter-email"
                           name="email"
                           class="newsletter-block__input"
                           placeholder="أدخل بريدك الإلكتروني"
                           required
                           autocomplete="email">
                    <button type="submit" class="btn btn-accent newsletter-block__btn">اشترك الآن</button>
                </form>
            </div>
        </div>
    </div>
</section>
