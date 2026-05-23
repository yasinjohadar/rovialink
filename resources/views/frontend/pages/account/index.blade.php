@extends('frontend.layouts.master')

@section('content')
    @include('frontend.partials.account-hero', ['user' => $user])

    <div class="container py-4">
        @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="row g-4 account-dashboard">
            @include('frontend.pages.account.partials.sidebar')

            <div class="col-lg-9 account-dashboard__main">
                @include('frontend.pages.account.partials.overview')
                @include('frontend.pages.account.partials.orders')
                @include('frontend.pages.account.partials.wishlist')
                @include('frontend.pages.account.partials.addresses')
                @include('frontend.pages.account.partials.profile')
                @include('frontend.pages.account.partials.security')
                @include('frontend.pages.account.partials.notifications')
            </div>
        </div>
    </div>

    @include('frontend.pages.account.partials.address-modal')
@endsection

@push('scripts')
<script src="{{ asset('frontend/assets/js/account-dashboard.js') }}"></script>
@endpush
