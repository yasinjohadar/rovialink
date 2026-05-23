@extends('frontend.layouts.master')

@section('content')
    @include('frontend.partials.shop-hero')

    <main class="container shop-page py-3 py-md-4">
        <div class="row g-3 g-lg-4 shop-page__layout">
            @include('frontend.partials.shop-sidebar')

            <div class="col-lg-9">
                @include('frontend.partials.shop-results')
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script src="{{ asset('frontend/assets/js/shop-filters.js') }}"></script>
<script>
    document.querySelectorAll('.color-option').forEach(opt => {
        opt.addEventListener('click', () => {
            document.querySelectorAll('.color-option').forEach(o => o.classList.remove('active'));
            opt.classList.add('active');
        });
    });

    function qvChangeQty(delta) {
        const input = document.getElementById('qv-qty');
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > 10) val = 10;
        input.value = val;
    }
</script>
@endpush
