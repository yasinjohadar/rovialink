@php
    $headerCartCount = collect(session('cart', []))->sum('quantity');
    $headerWishlistCount = auth()->check() ? auth()->user()->wishlists()->count() : 0;
    $headerSearchQuery = request()->routeIs('frontend.shop.*') ? request('search', '') : '';
    $siteName = site_brand_name();
    $siteLogoUrl = site_setting_url(\App\Services\SiteSettingsService::KEY_SITE_LOGO);
@endphp

<div class="top-bar d-none d-md-block">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex gap-4 align-items-center">
            <span class="top-bar-item"><i class="fas fa-envelope me-2"></i>support@ediostore.com</span>
            <span class="top-bar-item"><i class="fas fa-phone me-2"></i>+971 50 123 4567</span>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="#" class="top-bar-social" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" class="top-bar-social" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="top-bar-social" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" class="top-bar-social" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" class="top-bar-social" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg main-nav glass-nav" id="siteMainNav" aria-label="التنقل الرئيسي">
    <div class="container main-nav__container">
        <div class="main-nav__topbar">
            <div class="main-nav__topbar-actions d-lg-none">
                <a href="{{ route('frontend.cart.index') }}" class="main-nav__icon-btn main-nav__cart-mobile {{ request()->routeIs('frontend.cart.*', 'frontend.checkout.*') ? 'is-active' : '' }}" aria-label="سلة التسوق">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" data-initial-count="{{ $headerCartCount }}">{{ $headerCartCount }}</span>
                </a>

                <button class="navbar-toggler main-nav__toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="فتح القائمة">
                    <span class="main-nav__toggler-bar"></span>
                    <span class="main-nav__toggler-bar"></span>
                    <span class="main-nav__toggler-bar"></span>
                </button>
            </div>

            <a class="navbar-brand main-nav__brand" href="{{ url('/') }}">
                @if($siteLogoUrl)
                    <img src="{{ $siteLogoUrl }}" alt="{{ $siteName }}" class="main-nav__brand-logo" width="40" height="40">
                @else
                    <span class="main-nav__brand-icon" aria-hidden="true"><i class="fas fa-store"></i></span>
                @endif
                <span class="main-nav__brand-text">
                    <span class="main-nav__brand-name">{{ $siteName }}</span>
                </span>
            </a>

            <div class="main-nav__topbar-spacer d-lg-none" aria-hidden="true"></div>
        </div>

        <div class="collapse navbar-collapse main-nav__collapse" id="mainNav" data-bs-scroll="false">
            <ul class="navbar-nav main-nav__menu mx-lg-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }}" href="{{ route('frontend.home') }}">الرئيسية</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.shop.*') ? 'active' : '' }}" href="{{ route('frontend.shop.index') }}">المنتجات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.categories.*') ? 'active' : '' }}" href="{{ route('frontend.categories.index') }}">التصنيفات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.blog.*') ? 'active' : '' }}" href="{{ route('frontend.blog.index') }}">المدونة</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.about') ? 'active' : '' }}" href="{{ route('frontend.about') }}">من نحن</a>
                </li>
            </ul>

            <form class="main-nav__search d-none d-lg-flex" action="{{ route('frontend.shop.index') }}" method="GET" role="search">
                <label class="visually-hidden" for="mainNavSearch">بحث المنتجات</label>
                <div class="main-nav__search-field">
                    <i class="fas fa-search main-nav__search-icon" aria-hidden="true"></i>
                    <input
                        type="search"
                        id="mainNavSearch"
                        name="search"
                        class="main-nav__search-input"
                        placeholder="ابحث عن منتج، مفتاح، أو برنامج..."
                        value="{{ $headerSearchQuery }}"
                        autocomplete="off"
                    >
                    <button type="submit" class="main-nav__search-submit" aria-label="بحث">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </div>
            </form>

            <div class="main-nav__actions">
                <button type="button" class="main-nav__icon-btn theme-toggle" aria-label="تبديل الوضع الليلي">
                    <i class="fas fa-sun"></i>
                </button>

                <a href="{{ route('frontend.wishlist') }}" class="main-nav__icon-btn" aria-label="المفضلة">
                    <i class="fas fa-heart"></i>
                    <span class="wishlist-badge" data-initial-count="{{ $headerWishlistCount }}">{{ $headerWishlistCount }}</span>
                </a>

                <a href="{{ route('frontend.cart.index') }}" class="main-nav__icon-btn main-nav__cart-desktop d-none d-lg-inline-flex {{ request()->routeIs('frontend.cart.*', 'frontend.checkout.*') ? 'is-active' : '' }}" aria-label="سلة التسوق">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" data-initial-count="{{ $headerCartCount }}">{{ $headerCartCount }}</span>
                </a>

                @auth
                    @php
                        $headerUser = auth()->user();
                        $headerInitials = \App\Http\Controllers\Frontend\AccountController::userInitials($headerUser->name);
                    @endphp
                    <div class="dropdown main-nav__user">
                        <a href="#" class="main-nav__user-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            @if($headerUser->photoUrl())
                                <img src="{{ $headerUser->photoUrl() }}" alt="" class="main-nav__avatar main-nav__avatar--img" width="36" height="36">
                            @else
                                <span class="main-nav__avatar">{{ $headerInitials }}</span>
                            @endif
                            <span class="main-nav__user-name d-none d-xl-inline">حسابي</span>
                            <i class="fas fa-chevron-down main-nav__user-chevron"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end main-nav__dropdown">
                            <li><a class="dropdown-item" href="{{ route('frontend.account') }}"><i class="fas fa-tachometer-alt"></i> لوحة التحكم</a></li>
                            <li><a class="dropdown-item" href="{{ route('frontend.account') }}#orders"><i class="fas fa-box"></i> طلباتي</a></li>
                            <li><a class="dropdown-item" href="{{ route('frontend.account') }}#wishlist"><i class="fas fa-heart"></i> المفضلة</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item dropdown-item--danger">
                                        <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-accent btn-sm main-nav__login-btn">
                        <i class="fas fa-user ms-1"></i> تسجيل الدخول
                    </a>
                @endauth
            </div>

            <form class="main-nav__search main-nav__search--mobile d-lg-none w-100" action="{{ route('frontend.shop.index') }}" method="GET" role="search">
                <label class="visually-hidden" for="mainNavSearchMobile">بحث المنتجات</label>
                <div class="main-nav__search-field">
                    <i class="fas fa-search main-nav__search-icon" aria-hidden="true"></i>
                    <input
                        type="search"
                        id="mainNavSearchMobile"
                        name="search"
                        class="main-nav__search-input"
                        placeholder="ابحث عن منتج..."
                        value="{{ $headerSearchQuery }}"
                        autocomplete="off"
                    >
                    <button type="submit" class="main-nav__search-submit" aria-label="بحث">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</nav>
