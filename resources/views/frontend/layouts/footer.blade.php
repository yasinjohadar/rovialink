<footer class="site-footer" dir="rtl">
    <div class="site-footer__bg" aria-hidden="true">
        <div class="site-footer__orb site-footer__orb--1"></div>
        <div class="site-footer__orb site-footer__orb--2"></div>
        <div class="site-footer__grid-pattern"></div>
    </div>

    {{-- شريط العروض / النشرة --}}
    <div class="site-footer__cta">
        <div class="container">
            <div class="footer-cta-card">
                <div class="footer-cta-card__panel" aria-hidden="true"></div>
                <div class="row align-items-center g-4 g-lg-5">
                    <div class="col-lg-5 col-xl-5">
                        <span class="footer-cta-badge"><i class="fas fa-bolt"></i> عروض حصرية</span>
                        <h3 class="footer-cta-title mb-2">انضم لنشرة {{ site_brand_name() }}</h3>
                        <p class="footer-cta-text mb-0">احصل على خصومات أسبوعية وإشعارات المنتجات الجديدة قبل الجميع.</p>
                    </div>
                    <div class="col-lg-7 col-xl-7">
                        <form class="footer-newsletter" action="{{ route('frontend.contact') }}" method="get">
                            <label class="visually-hidden" for="footer-newsletter-email">البريد الإلكتروني</label>
                            <div class="footer-newsletter__inner">
                                <span class="footer-newsletter__icon" aria-hidden="true"><i class="fas fa-envelope"></i></span>
                                <input type="email"
                                       id="footer-newsletter-email"
                                       name="email"
                                       class="footer-newsletter__input"
                                       placeholder="أدخل بريدك الإلكتروني"
                                       required
                                       autocomplete="email">
                                <button type="submit" class="footer-newsletter__btn">
                                    <span>اشترك الآن</span>
                                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                                </button>
                            </div>
                        </form>
                        <p class="footer-newsletter-hint mb-0 mt-3"><i class="fas fa-lock" aria-hidden="true"></i> لن نشارك بريدك مع أي طرف ثالث</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- المحتوى الرئيسي --}}
    <div class="site-footer__main">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('frontend.home') }}" class="footer-brand d-inline-flex align-items-center text-decoration-none mb-3">
                        <span class="footer-brand__icon"><i class="fas fa-store"></i></span>
                        <span class="footer-brand__text">
                            <span class="footer-brand__name">{{ site_brand_name() }}</span>
                            <span class="footer-brand__tagline en-text">Smart Shopping</span>
                        </span>
                    </a>
                    <p class="footer-about">
                        {{ site_footer_text() }}
                    </p>
                    <div class="footer-stats row g-2">
                        <div class="col-4">
                            <div class="footer-stat">
                                <strong>+500</strong>
                                <span>منتج</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="footer-stat">
                                <strong>24/7</strong>
                                <span>دعم</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="footer-stat">
                                <strong>4.9</strong>
                                <span>تقييم</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-2 col-md-3">
                    <h6 class="footer-heading">روابط سريعة</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('frontend.home') }}"><i class="fas fa-chevron-left"></i> الرئيسية</a></li>
                        <li><a href="{{ route('frontend.shop.index') }}"><i class="fas fa-chevron-left"></i> المنتجات</a></li>
                        <li><a href="{{ route('frontend.categories.index') }}"><i class="fas fa-chevron-left"></i> التصنيفات</a></li>
                        <li><a href="{{ route('frontend.blog.index') }}"><i class="fas fa-chevron-left"></i> المدونة</a></li>
                        <li><a href="{{ route('frontend.about') }}"><i class="fas fa-chevron-left"></i> من نحن</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-2 col-md-3">
                    <h6 class="footer-heading">خدمة العملاء</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('frontend.contact') }}"><i class="fas fa-chevron-left"></i> اتصل بنا</a></li>
                        <li><a href="{{ route('frontend.faq') }}"><i class="fas fa-chevron-left"></i> الأسئلة الشائعة</a></li>
                        @auth
                            <li><a href="{{ route('frontend.account') }}"><i class="fas fa-chevron-left"></i> حسابي</a></li>
                        @else
                            <li><a href="{{ route('login') }}"><i class="fas fa-chevron-left"></i> تسجيل الدخول</a></li>
                        @endauth
                        <li><a href="{{ route('frontend.cart.index') }}"><i class="fas fa-chevron-left"></i> السلة</a></li>
                        <li><a href="{{ route('frontend.privacy') }}"><i class="fas fa-chevron-left"></i> الخصوصية</a></li>
                        <li><a href="{{ route('frontend.terms') }}"><i class="fas fa-chevron-left"></i> الشروط</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h6 class="footer-heading">تواصل معنا</h6>
                    <ul class="footer-contact-list">
                        @if(site_contact_email() !== '')
                        <li>
                            <a href="mailto:{{ site_contact_email() }}" class="footer-contact-item">
                                <span class="footer-contact-item__icon"><i class="fas fa-envelope"></i></span>
                                <span>
                                    <small>البريد الإلكتروني</small>
                                    <strong>{{ site_contact_email() }}</strong>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if(site_contact_phone() !== '')
                        <li>
                            <a href="{{ site_contact_phone_href() }}" class="footer-contact-item">
                                <span class="footer-contact-item__icon"><i class="fas fa-phone"></i></span>
                                <span>
                                    <small>الهاتف</small>
                                    <strong class="en-text" dir="ltr">{{ site_contact_phone() }}</strong>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if(site_address() !== '')
                        <li>
                            <div class="footer-contact-item footer-contact-item--static">
                                <span class="footer-contact-item__icon"><i class="fas fa-location-dot"></i></span>
                                <span>
                                    <small>العنوان</small>
                                    <strong>{{ site_address() }}</strong>
                                </span>
                            </div>
                        </li>
                        @endif
                    </ul>
                    <div class="footer-social">
                        <a href="#" class="footer-social__link" aria-label="تويتر" data-social="twitter"><i class="fab fa-x-twitter"></i></a>
                        <a href="#" class="footer-social__link" aria-label="فيسبوك" data-social="facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="footer-social__link" aria-label="إنستغرام" data-social="instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="footer-social__link" aria-label="لينكدإن" data-social="linkedin"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="footer-social__link" aria-label="يوتيوب" data-social="youtube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- مميزات الثقة --}}
    <div class="site-footer__features">
        <div class="container">
            <div class="row g-3">
                <div class="col-6 col-lg-3">
                    <div class="footer-feature">
                        <span class="footer-feature__icon"><i class="fas fa-truck-fast"></i></span>
                        <div>
                            <strong>توصيل سريع</strong>
                            <span>خلال 24–48 ساعة</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="footer-feature">
                        <span class="footer-feature__icon"><i class="fas fa-shield-halved"></i></span>
                        <div>
                            <strong>دفع آمن</strong>
                            <span>تشفير SSL كامل</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="footer-feature">
                        <span class="footer-feature__icon"><i class="fas fa-headset"></i></span>
                        <div>
                            <strong>دعم متواصل</strong>
                            <span>فريق جاهز لمساعدتك</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="footer-feature">
                        <span class="footer-feature__icon"><i class="fas fa-rotate-left"></i></span>
                        <div>
                            <strong>إرجاع سهل</strong>
                            <span>خلال 14 يوماً</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- الشريط السفلي --}}
    <div class="site-footer__bottom">
        <div class="container">
            <div class="footer-bottom-inner">
                <p class="footer-copyright mb-0">
                    &copy; {{ date('Y') }} <strong>{{ site_brand_name() }}</strong>. جميع الحقوق محفوظة.
                </p>
                <div class="footer-payments" aria-label="طرق الدفع">
                    <span class="footer-payment" title="Visa"><i class="fab fa-cc-visa"></i></span>
                    <span class="footer-payment" title="Mastercard"><i class="fab fa-cc-mastercard"></i></span>
                    <span class="footer-payment" title="Apple Pay"><i class="fab fa-cc-apple-pay"></i></span>
                    <span class="footer-payment" title="PayPal"><i class="fab fa-cc-paypal"></i></span>
                    <span class="footer-payment" title="Amex"><i class="fab fa-cc-amex"></i></span>
                </div>
                <nav class="footer-legal" aria-label="روابط قانونية">
                    <a href="{{ route('frontend.privacy') }}">الخصوصية</a>
                    <span class="footer-legal__dot"></span>
                    <a href="{{ route('frontend.terms') }}">الشروط</a>
                    <span class="footer-legal__dot"></span>
                    <a href="{{ route('frontend.faq') }}">المساعدة</a>
                </nav>
            </div>
        </div>
    </div>
</footer>
