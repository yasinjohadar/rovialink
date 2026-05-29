@php
    $initials = \App\Http\Controllers\Frontend\AccountController::userInitials($user->name);
    $newNotifications = $notifications->where('badge', 'جديد')->count();
    $navItems = [
        ['section' => 'overview', 'icon' => 'fa-home', 'label' => 'نظرة عامة', 'badge' => null, 'badgeClass' => 'bg-accent'],
        ['section' => 'orders', 'icon' => 'fa-box', 'label' => 'طلباتي', 'badge' => $stats['orders_total'], 'badgeClass' => 'bg-accent'],
        ['section' => 'wishlist', 'icon' => 'fa-heart', 'label' => 'المفضلة', 'badge' => $stats['wishlist_count'], 'badgeClass' => 'bg-danger'],
        ['section' => 'addresses', 'icon' => 'fa-map-marker-alt', 'label' => 'عناوين الفوترة', 'badge' => null, 'badgeClass' => 'bg-accent'],
        ['section' => 'profile', 'icon' => 'fa-user-edit', 'label' => 'الملف الشخصي', 'badge' => null, 'badgeClass' => 'bg-accent'],
        ['section' => 'security', 'icon' => 'fa-shield-alt', 'label' => 'الأمان', 'badge' => null, 'badgeClass' => 'bg-accent'],
        ['section' => 'notifications', 'icon' => 'fa-bell', 'label' => 'الإشعارات', 'badge' => $newNotifications > 0 ? $newNotifications : null, 'badgeClass' => 'bg-warning text-dark'],
    ];
@endphp
<aside class="col-lg-3">
    <nav class="account-sidebar shop-filters section-fade-up sticky-top" aria-label="قائمة الحساب">
        <div class="shop-filters__card account-sidebar__card">
            <header class="shop-filters__header account-sidebar__profile">
                <div class="shop-filters__title account-sidebar__title">
                    @if($user->photoUrl())
                        <img src="{{ $user->photoUrl() }}"
                             alt="{{ $user->name }}"
                             class="account-sidebar__avatar account-sidebar__avatar--img"
                             width="48"
                             height="48">
                    @else
                        <span class="shop-filters__title-icon account-sidebar__avatar" aria-hidden="true">{{ $initials }}</span>
                    @endif
                    <div class="account-sidebar__identity">
                        <h2 class="shop-filters__heading account-sidebar__name">{{ $user->name }}</h2>
                        <p class="account-sidebar__email en-text">{{ $user->email }}</p>
                    </div>
                </div>
                <span class="account-sidebar__tier badge">
                    <i class="fas fa-crown ms-1" aria-hidden="true"></i>
                    {{ $loyaltyTier }}
                </span>
            </header>

            <div class="shop-filters__section account-sidebar__section">
                <h3 class="shop-filters__section-title">القائمة</h3>
                <ul class="shop-filters__list account-sidebar__list" id="dashboard-nav">
                    @foreach($navItems as $item)
                    <li>
                        <a href="#{{ $item['section'] }}"
                           class="account-sidebar__link{{ $item['section'] === 'overview' ? ' active' : '' }}"
                           data-section="{{ $item['section'] }}">
                            <span class="account-sidebar__indicator" aria-hidden="true"></span>
                            <i class="fas {{ $item['icon'] }} account-sidebar__icon" aria-hidden="true"></i>
                            <span class="shop-filters__option-text">{{ $item['label'] }}</span>
                            @if($item['badge'] !== null && $item['badge'] !== '')
                            <span class="account-sidebar__badge badge {{ $item['badgeClass'] }} en-text">{{ $item['badge'] }}</span>
                            @endif
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="account-sidebar__footer shop-filters__section">
                <a href="{{ route('frontend.shop.index') }}" class="account-sidebar__footer-link">
                    <i class="fas fa-store" aria-hidden="true"></i>
                    <span>العودة للمتجر</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="account-sidebar__logout-form">
                    @csrf
                    <button type="submit" class="account-sidebar__footer-link account-sidebar__footer-link--danger">
                        <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>
</aside>
