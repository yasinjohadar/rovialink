<hr class="border-secondary border-opacity-25 my-5">
<div class="mb-5 section-fade-up product-page-related">
    <h3 class="fw-bold mb-4 text-accent">منتجات قد تعجبك أيضاً</h3>
    <div class="row g-4">
        @forelse($relatedProducts as $related)
            @include('frontend.partials.product-card', [
                'product' => $related,
                'columnClass' => 'col-6 col-md-6 col-lg-3',
            ])
        @empty
        <div class="col-12 text-center py-4">
            <p class="text-secondary">لا توجد منتجات مشابهة حالياً</p>
        </div>
        @endforelse
    </div>
</div>
