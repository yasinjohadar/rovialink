<div class="top-bar d-none d-md-block">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex gap-4 align-items-center">
            <span class="top-bar-item"><i class="fas fa-envelope me-2"></i>support@ediostore.com</span>
            <span class="top-bar-item"><i class="fas fa-phone me-2"></i>+971 50 123 4567</span>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="#" class="top-bar-social"><i class="fab fa-twitter"></i></a>
            <a href="#" class="top-bar-social"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="top-bar-social"><i class="fab fa-instagram"></i></a>
            <a href="#" class="top-bar-social"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" class="top-bar-social"><i class="fab fa-youtube"></i></a>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg main-nav glass-nav py-1">
    <div class="container">
        <a class="navbar-brand text-white fw-bold fs-4 position-relative" href="{{ url('/') }}">
            <i class="fas fa-store me-2" style="color: var(--accent-color);"></i>
            <span class="ms-2">إديو</span><span style="color: var(--accent-color);">ستور</span>
        </a>

        <button class="navbar-toggler btn-glass border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars text-white"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 pe-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }} ps-3" href="{{ route('frontend.home') }}">الرئيسية</a>
                </li>
                <li class="nav-item"><a class="nav-link px-3" href="{{ route('frontend.shop.index') }}">المنتجات</a></li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.categories.index') ? 'active' : '' }} px-3" href="{{ route('frontend.categories.index') }}">التصنيفات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.blog.*') ? 'active' : '' }} px-3" href="{{ route('frontend.blog.index') }}">المدونة</a>
                </li>
                <li class="nav-item"><a class="nav-link px-3" href="#">من نحن</a></li>
            </ul>

            <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0 me-lg-4">
                <button class="theme-toggle" aria-label="Toggle Dark Mode">
                    <i class="fas fa-sun"></i>
                </button>

                <a href="{{ route('frontend.wishlist') }}" class="text-white text-decoration-none position-relative">
                    <i class="fas fa-heart fs-5"></i>
                    <span class="wishlist-badge">{{ auth()->check() ? auth()->user()->wishlists()->count() : 0 }}</span>
                </a>

                @php $headerCartCount = collect(session('cart', []))->sum('quantity'); @endphp
                <a href="{{ route('frontend.cart.index') }}" class="text-white text-decoration-none position-relative {{ request()->routeIs('frontend.cart.*', 'frontend.checkout.*') ? 'opacity-100' : '' }}">
                    <i class="fas fa-shopping-cart fs-5 {{ request()->routeIs('frontend.cart.*', 'frontend.checkout.*') ? 'text-accent' : '' }}"></i>
                    <span class="cart-badge">{{ $headerCartCount }}</span>
                </a>

                @auth
                    @php
                        $headerUser = auth()->user();
                        $headerInitials = \App\Http\Controllers\Frontend\AccountController::userInitials($headerUser->name);
                    @endphp
                    <div class="dropdown">
                        <a href="#" class="text-white text-decoration-none d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="rounded-circle bg-accent d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                <span class="fw-bold small">{{ $headerInitials }}</span>
                            </div>
                            <span class="d-none d-lg-inline small">{{ Str::limit($headerUser->name, 20) }}</span>
                            <i class="fas fa-chevron-down small"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end glass-panel border-0 mt-2" style="min-width: 200px;">
                            <li><a class="dropdown-item text-white" href="{{ route('frontend.account') }}"><i class="fas fa-tachometer-alt me-2 text-accent"></i> لوحة التحكم</a></li>
                            <li><a class="dropdown-item text-white" href="{{ route('frontend.account') }}#orders"><i class="fas fa-box me-2 text-accent"></i> طلباتي</a></li>
                            <li><a class="dropdown-item text-white" href="{{ route('frontend.account') }}#wishlist"><i class="fas fa-heart me-2 text-accent"></i> المفضلة</a></li>
                            <li><hr class="dropdown-divider border-secondary"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start">
                                        <i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-glass btn-sm px-4">تسجيل الدخول</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
