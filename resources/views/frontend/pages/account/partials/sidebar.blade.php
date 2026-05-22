@php
    $initials = \App\Http\Controllers\Frontend\AccountController::userInitials($user->name);
@endphp
<aside class="col-lg-3">
    <div class="glass-panel p-0 sticky-top section-fade-up" style="top: 100px; z-index: 10; overflow: hidden;">
        <div class="p-4 text-center border-bottom border-secondary border-opacity-25">
            <div class="position-relative d-inline-block mb-3">
                @if($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="rounded-circle mx-auto" style="width:80px;height:80px;object-fit:cover;">
                @else
                    <div class="rounded-circle bg-accent d-flex align-items-center justify-content-center mx-auto" style="width:80px;height:80px;font-size:2rem;">
                        <span class="fw-bold">{{ $initials }}</span>
                    </div>
                @endif
            </div>
            <h5 class="fw-bold text-white mb-1">{{ $user->name }}</h5>
            <p class="text-secondary small mb-2">{{ $user->email }}</p>
            <span class="badge bg-accent px-3 py-1 rounded-pill">{{ $loyaltyTier }} <i class="fas fa-crown ms-1"></i></span>
        </div>

        <div class="p-3">
            <ul class="nav flex-column gap-1" id="dashboard-nav">
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-3 px-3 py-2 rounded-3 active" href="#overview" data-section="overview">
                        <i class="fas fa-home text-accent" style="width:20px;text-align:center;"></i>
                        <span>نظرة عامة</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-3 px-3 py-2 rounded-3" href="#orders" data-section="orders">
                        <i class="fas fa-box text-accent" style="width:20px;text-align:center;"></i>
                        <span>طلباتي</span>
                        <span class="badge bg-accent ms-auto en-text">{{ $stats['orders_total'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-3 px-3 py-2 rounded-3" href="#wishlist" data-section="wishlist">
                        <i class="fas fa-heart text-accent" style="width:20px;text-align:center;"></i>
                        <span>المفضلة</span>
                        <span class="badge bg-danger ms-auto en-text">{{ $stats['wishlist_count'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-3 px-3 py-2 rounded-3" href="#addresses" data-section="addresses">
                        <i class="fas fa-map-marker-alt text-accent" style="width:20px;text-align:center;"></i>
                        <span>عناوين الفوترة</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-3 px-3 py-2 rounded-3" href="#profile" data-section="profile">
                        <i class="fas fa-user-edit text-accent" style="width:20px;text-align:center;"></i>
                        <span>الملف الشخصي</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-3 px-3 py-2 rounded-3" href="#security" data-section="security">
                        <i class="fas fa-shield-alt text-accent" style="width:20px;text-align:center;"></i>
                        <span>الأمان</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-3 px-3 py-2 rounded-3" href="#notifications" data-section="notifications">
                        <i class="fas fa-bell text-accent" style="width:20px;text-align:center;"></i>
                        <span>الإشعارات</span>
                        @if($notifications->where('badge', 'جديد')->count() > 0)
                        <span class="badge bg-warning text-dark ms-auto en-text">{{ $notifications->where('badge', 'جديد')->count() }}</span>
                        @endif
                    </a>
                </li>
            </ul>

            <hr class="border-secondary border-opacity-25 my-3">

            <a href="{{ route('frontend.shop.index') }}" class="nav-link text-white d-flex align-items-center gap-3 px-3 py-2 rounded-3">
                <i class="fas fa-store" style="width:20px;text-align:center;"></i>
                <span>العودة للمتجر</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="nav-link text-danger d-flex align-items-center gap-3 px-3 py-2 rounded-3 border-0 bg-transparent w-100 text-start">
                    <i class="fas fa-sign-out-alt" style="width:20px;text-align:center;"></i>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </div>
    </div>
</aside>
