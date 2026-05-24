<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="dark">
<head>
    @include('frontend.layouts.head')
</head>
<body>

    @include('frontend.layouts.main-header')

    <div id="toast-container"></div>
    
    <main>
        @yield('content')
    </main>

    @include('frontend.layouts.footer')

    @include('frontend.partials.quick-view-modal')

    @include('frontend.partials.store-chat-widget')

    @include('frontend.layouts.footer-scripts')
</body>
</html>
