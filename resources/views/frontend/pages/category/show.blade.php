@extends('frontend.layouts.master')

@section('title', $category->name.' - متجر إديو ستور')

@section('content')
    @include('frontend.partials.category-hero', ['category' => $category])

    <main class="container py-4">
        <div class="row g-4">
            @include('frontend.partials.shop-sidebar', [
                'filterAction' => route('frontend.category.show', $category->slug),
                'activeCategorySlug' => $category->slug,
            ])

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
        if (!input) return;
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > 10) val = 10;
        input.value = val;
    }
</script>
@endpush
