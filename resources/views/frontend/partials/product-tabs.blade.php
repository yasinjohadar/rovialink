<section class="product-page__tabs section-fade-up">
    <div class="product-page__tabs-nav">
        <ul class="nav nav-tabs product-page-tabs border-0 flex-nowrap overflow-auto" id="productTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active w-100 text-nowrap" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button">الوصف</button>
            </li>
            @if($product->specifications)
            <li class="nav-item" role="presentation">
                <button class="nav-link w-100 text-nowrap" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button">المواصفات</button>
            </li>
            @endif
            <li class="nav-item" role="presentation">
                <button class="nav-link w-100 text-nowrap" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">التقييمات</button>
            </li>
        </ul>
    </div>

    <div class="tab-content product-page__tabs-content" id="productTabContent">
        <div class="tab-pane fade show active" id="desc" role="tabpanel">
            <div class="product-page__tab-panel">
                <h2 class="product-page__tab-title">وصف المنتج</h2>
                <div class="product-page__tab-body product-description-content">
                    @if($product->description)
                        {!! $product->description !!}
                    @elseif($product->short_description)
                        <p class="m-0">{{ $product->short_description }}</p>
                    @else
                        <p class="m-0">لا يوجد وصف تفصيلي لهذا المنتج حالياً.</p>
                    @endif
                </div>
            </div>
        </div>

        @if($product->specifications)
        <div class="tab-pane fade" id="specs" role="tabpanel">
            <div class="product-page__tab-panel">
                <h2 class="product-page__tab-title">المواصفات التقنية</h2>
                <table class="table product-page__specs-table mb-0">
                    <tbody>
                        @foreach(explode("\n", $product->specifications) as $spec)
                            @php
                                $parts = explode(':', trim($spec), 2);
                            @endphp
                            @if(count($parts) === 2)
                            <tr>
                                <th scope="row">{{ trim($parts[0]) }}</th>
                                <td>{{ trim($parts[1]) }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="tab-pane fade" id="reviews" role="tabpanel">
            <div class="product-page__tab-panel">
                @php
                    $approvedReviews = $product->reviews;
                    $avgRating = $product->reviews_avg_rating ?? ($approvedReviews->avg('rating') ?? 0);
                    $totalReviews = $approvedReviews->count();
                    $fiveStars = $approvedReviews->where('rating', 5)->count();
                    $fourStars = $approvedReviews->where('rating', 4)->count();
                    $threeStars = $approvedReviews->where('rating', 3)->count();
                    $fivePercent = $totalReviews > 0 ? ($fiveStars / $totalReviews * 100) : 0;
                    $fourPercent = $totalReviews > 0 ? ($fourStars / $totalReviews * 100) : 0;
                    $threePercent = $totalReviews > 0 ? ($threeStars / $totalReviews * 100) : 0;
                @endphp

                <div class="row mb-4 align-items-center g-3">
                    <div class="col-md-4 text-center">
                        <p class="product-page__rating-big en-text">{{ number_format($avgRating, 1) }}</p>
                        <div class="text-warning fs-5 my-2 en-text">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($avgRating))
                                    <i class="fas fa-star"></i>
                                @elseif($i - 0.5 <= $avgRating)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="product-page__tab-muted m-0">متوسط التقييم</p>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-2">
                            <div class="text-warning en-text product-page__rating-label">5 <i class="fas fa-star small"></i></div>
                            <div class="rating-bar"><div class="rating-fill" style="width: {{ $fivePercent }}%"></div></div>
                            <div class="product-page__tab-muted en-text small product-page__rating-pct">{{ round($fivePercent) }}%</div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="text-warning en-text product-page__rating-label">4 <i class="fas fa-star small"></i></div>
                            <div class="rating-bar"><div class="rating-fill" style="width: {{ $fourPercent }}%"></div></div>
                            <div class="product-page__tab-muted en-text small product-page__rating-pct">{{ round($fourPercent) }}%</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="text-warning en-text product-page__rating-label">3 <i class="fas fa-star small"></i></div>
                            <div class="rating-bar"><div class="rating-fill" style="width: {{ $threePercent }}%"></div></div>
                            <div class="product-page__tab-muted en-text small product-page__rating-pct">{{ round($threePercent) }}%</div>
                        </div>
                    </div>
                </div>

                <hr class="product-page__divider">

                @forelse($approvedReviews->take(5) as $review)
                <div class="product-page__review">
                    <div class="product-page__review-avatar en-text">{{ strtoupper(substr($review->user->name ?? 'U', 0, 2)) }}</div>
                    <div>
                        <h3 class="product-page__review-author">{{ $review->user->name ?? 'مستخدم' }}</h3>
                        <div class="text-warning small en-text mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="product-page__review-text mb-0">{{ $review->comment }}</p>
                    </div>
                </div>
                @empty
                <p class="product-page__tab-muted text-center mb-0">لا توجد تقييمات بعد</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
