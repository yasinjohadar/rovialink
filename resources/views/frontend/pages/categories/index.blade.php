@extends('frontend.layouts.master')

@section('title', 'متجر إديو ستور - التصنيفات')

@section('content')
    @include('frontend.partials.categories-hero')

    <main class="container py-4">
        <div id="toast-container"></div>
        @include('frontend.partials.categories-grid')
    </main>
@endsection

@push('scripts')
<script>
    function qvChangeQty(delta) {
        const input = document.getElementById('qv-qty');
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > 10) val = 10;
        input.value = val;
    }
</script>
@endpush
