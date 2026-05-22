<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'المتجر')</title>
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.rtl.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    <style>
        body { padding-top: 1rem; }
        .store-header { margin-bottom: 1.5rem; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg store-header">
            <a class="navbar-brand" href="{{ route('store.products.index') }}">المتجر</a>
            <div class="ms-auto">
                <a class="btn btn-outline-primary" href="{{ route('store.cart.index') }}">السلة</a>
            </div>
        </nav>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
