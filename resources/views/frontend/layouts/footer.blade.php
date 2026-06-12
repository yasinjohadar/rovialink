<footer class="site-footer site-footer--unified site-footer--rich" dir="rtl">
    <div class="site-footer__bg" aria-hidden="true">
        <div class="site-footer__orb site-footer__orb--1"></div>
        <div class="site-footer__orb site-footer__orb--2"></div>
        <div class="site-footer__orb site-footer__orb--3"></div>
        <div class="site-footer__grid-pattern"></div>
        <span class="site-footer__square site-footer__square--1"></span>
        <span class="site-footer__square site-footer__square--2"></span>
        <span class="site-footer__square site-footer__square--3"></span>
    </div>

    <div class="container site-footer__wrap">
        <div class="site-footer__panel">
            <div class="site-footer__subscribe">
                <div class="site-footer__subscribe-copy">
                    <span class="site-footer__subscribe-badge"><i class="fas fa-envelope-open-text" aria-hidden="true"></i> النشرة البريدية</span>
                    <p class="mb-0">اشترك لتصلك العروض والمنتجات الجديدة أولاً</p>
                </div>
                <form class="site-footer__subscribe-form" action="{{ route('frontend.contact') }}" method="get">
                    <label class="visually-hidden" for="footer-subscribe-email">البريد الإلكتروني</label>
                    <input type="email"
                           id="footer-subscribe-email"
                           name="email"
                           class="site-footer__subscribe-input"
                           placeholder="name@example.com"
                           dir="ltr"
                           required
                           autocomplete="email">
                    <button type="submit" class="site-footer__subscribe-btn">
                        <span>اشترك</span>
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    </button>
                </form>
            </div>

            <div class="row site-footer__grid g-4 g-xl-5">
                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('frontend.home') }}" class="footer-brand d-inline-flex align-items-center text-decoration-none mb-3">
                        @if(site_setting_url(\App\Services\SiteSettingsService::KEY_SITE_LOGO))
                            <img src="{{ site_setting_url(\App\Services\SiteSettingsService::KEY_SITE_LOGO) }}"
                                 alt="{{ site_brand_name() }}"
                                 class="footer-brand__logo"
                                 width="52"
                                 height="52">
                        @else
                            <span class="footer-brand__icon"><i class="fas fa-store"></i></span>
                        @endif
                        <span class="footer-brand__text">
                            <span class="footer-brand__name">{{ site_brand_name() }}</span>
                            <span class="footer-brand__tagline">متجر منتجات رقمية موثوق</span>
                        </span>
                    </a>
                    <p class="footer-about">{{ site_footer_text() }}</p>

                    <div class="footer-stat-chips">
                        <div class="footer-stat-chip">
                            <span class="footer-stat-chip__icon" aria-hidden="true"><i class="fas fa-cube"></i></span>
                            <span class="footer-stat-chip__body">
                                <strong class="en-text">+500</strong>
                                <small>منتج رقمي</small>
                            </span>
                        </div>
                        <div class="footer-stat-chip">
                            <span class="footer-stat-chip__icon" aria-hidden="true"><i class="fas fa-headset"></i></span>
                            <span class="footer-stat-chip__body">
                                <strong class="en-text">24/7</strong>
                                <small>دعم فني</small>
                            </span>
                        </div>
                        <div class="footer-stat-chip">
                            <span class="footer-stat-chip__icon" aria-hidden="true"><i class="fas fa-star"></i></span>
                            <span class="footer-stat-chip__body">
                                <strong class="en-text">4.9</strong>
                                <small>تقييم العملاء</small>
                            </span>
                        </div>
                    </div>

                    <div class="footer-social">
                        <span class="footer-social__label">تابعنا</span>
                        <div class="footer-social__links">
                            <a href="#" class="footer-social__link" aria-label="تويتر" data-social="twitter"><i class="fab fa-x-twitter"></i></a>
                            <a href="#" class="footer-social__link" aria-label="فيسبوك" data-social="facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="footer-social__link" aria-label="إنستغرام" data-social="instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="footer-social__link" aria-label="لينكدإن" data-social="linkedin"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="footer-social__link" aria-label="يوتيوب" data-social="youtube"><i class="fab fa-youtube"></i></a>
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
                    @php
                        $hasContact = site_contact_email() !== '' || site_contact_phone() !== '' || site_address() !== '';
                    @endphp
                    @if($hasContact)
                    <ul class="footer-contact-cards">
                        @if(site_contact_email() !== '')
                        <li>
                            <a href="mailto:{{ site_contact_email() }}" class="footer-contact-card">
                                <span class="footer-contact-card__icon"><i class="fas fa-envelope"></i></span>
                                <span class="footer-contact-card__body">
                                    <small>البريد الإلكتروني</small>
                                    <strong>{{ site_contact_email() }}</strong>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if(site_contact_phone() !== '')
                        <li>
                            <a href="{{ site_contact_phone_href() }}" class="footer-contact-card">
                                <span class="footer-contact-card__icon"><i class="fas fa-phone"></i></span>
                                <span class="footer-contact-card__body">
                                    <small>الهاتف</small>
                                    <strong class="en-text" dir="ltr">{{ site_contact_phone() }}</strong>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if(site_address() !== '')
                        <li>
                            <div class="footer-contact-card footer-contact-card--static">
                                <span class="footer-contact-card__icon"><i class="fas fa-location-dot"></i></span>
                                <span class="footer-contact-card__body">
                                    <small>العنوان</small>
                                    <strong>{{ site_address() }}</strong>
                                </span>
                            </div>
                        </li>
                        @endif
                    </ul>
                    @else
                    <div class="footer-contact-fallback">
                        <p class="footer-contact-fallback__text mb-3">فريقنا جاهز لمساعدتك في أي استفسار حول المنتجات الرقمية والطلبات.</p>
                        <a href="{{ route('frontend.contact') }}" class="footer-contact-fallback__btn">
                            <i class="fas fa-paper-plane" aria-hidden="true"></i>
                            <span>أرسل رسالة</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="site-footer__trust-grid">
                <div class="footer-trust-pill">
                    <span class="footer-trust-pill__icon"><i class="fas fa-bolt" aria-hidden="true"></i></span>
                    <span class="footer-trust-pill__text">
                        <strong>تسليم فوري</strong>
                        <small>فور إتمام الدفع</small>
                    </span>
                </div>
                <div class="footer-trust-pill">
                    <span class="footer-trust-pill__icon"><i class="fas fa-shield-halved" aria-hidden="true"></i></span>
                    <span class="footer-trust-pill__text">
                        <strong>دفع آمن</strong>
                        <small>تشفير SSL كامل</small>
                    </span>
                </div>
                <div class="footer-trust-pill">
                    <span class="footer-trust-pill__icon"><i class="fas fa-headset" aria-hidden="true"></i></span>
                    <span class="footer-trust-pill__text">
                        <strong>دعم 24/7</strong>
                        <small>فريق جاهز لمساعدتك</small>
                    </span>
                </div>
                <div class="footer-trust-pill">
                    <span class="footer-trust-pill__icon"><i class="fas fa-award" aria-hidden="true"></i></span>
                    <span class="footer-trust-pill__text">
                        <strong>ضمان الجودة</strong>
                        <small>منتجات أصلية 100%</small>
                    </span>
                </div>
            </div>

            <div class="site-footer__bar">
                <p class="footer-copyright mb-0">
                    &copy; {{ date('Y') }} <strong>{{ site_brand_name() }}</strong> — جميع الحقوق محفوظة
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
