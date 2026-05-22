<div class="page-hero">
    <div class="page-hero-content container">
        <div class="page-hero-icon"><i class="fas fa-tachometer-alt"></i></div>
        <h1 class="page-hero-title">لوحة التحكم</h1>
        <p class="page-hero-subtitle">مرحباً {{ $user->name }}! تابع طلباتك وإعدادات حسابك من مكان واحد.</p>
        <nav class="page-hero-breadcrumb" aria-label="breadcrumb">
            <a href="{{ route('frontend.home') }}">الرئيسية</a>
            <i class="fas fa-chevron-left sep"></i>
            <span class="current">حسابي</span>
        </nav>
    </div>
    <div class="page-hero-wave">
        <svg viewBox="0 0 1440 65" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,30 C360,65 1080,0 1440,30 L1440,65 L0,65 Z" style="fill:var(--page-bg)"/>
        </svg>
    </div>
</div>
