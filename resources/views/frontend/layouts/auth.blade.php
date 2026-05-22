<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="dark">
<head>
    @include('frontend.layouts.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="auth-page">
    @yield('content')
    @include('frontend.layouts.footer-scripts')
    @stack('scripts')
</body>
</html>
