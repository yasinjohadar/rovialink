<section class="product-page__related section-fade-up">
    <h2 class="product-page__related-title">منتجات قد تعجبك أيضاً</h2>
    <div class="row g-3">
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
</section>
