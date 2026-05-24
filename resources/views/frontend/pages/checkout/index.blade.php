@extends('frontend.layouts.master')

@section('content')
    @include('frontend.partials.checkout-hero')

    <main class="container py-4 account-dashboard">
        <div class="row g-5">
            <div class="col-lg-7">
                @include('frontend.partials.checkout-form')
            </div>
            <div class="col-lg-5">
                @include('frontend.partials.checkout-summary')
            </div>
        </div>
    </main>
@endsection
