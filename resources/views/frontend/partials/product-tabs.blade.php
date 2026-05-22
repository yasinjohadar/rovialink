<section class="product-page-bottom mt-5 section-fade-up">
    <div class="glass-panel p-0 overflow-hidden mb-0">
        <ul class="nav nav-tabs course-tabs border-0 flex-nowrap overflow-auto" id="productTab" role="tablist">
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

    <div class="tab-content" id="productTabContent">
        <div class="tab-pane fade show active section-fade-up" id="desc" role="tabpanel">
            <div class="glass-panel p-4">
                <h4 class="fw-bold text-accent mb-4">وصف المنتج</h4>
                <div class="text-secondary lh-lg product-description-content">
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
            <div class="glass-panel p-4">
                <h4 class="fw-bold text-accent mb-4">المواصفات التقنية</h4>
                <table class="table table-borderless text-secondary">
                    <tbody>
                        @foreach(explode("\n", $product->specifications) as $spec)
                            @php
                                $parts = explode(':', trim($spec), 2);
                            @endphp
                            @if(count($parts) === 2)
                            <tr>
                                <td class="fw-bold text-white" style="width: 200px;">{{ trim($parts[0]) }}</td>
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
            <div class="glass-panel p-4">
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

                <div class="row mb-4 align-items-center">
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <h1 class="display-1 fw-bold text-accent en-text m-0">{{ number_format($avgRating, 1) }}</h1>
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
                        <p class="text-secondary m-0">متوسط التقييم</p>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-2">
                            <div class="text-warning en-text" style="width: 80px">5 <i class="fas fa-star small"></i></div>
                            <div class="rating-bar"><div class="rating-fill" style="width: {{ $fivePercent }}%"></div></div>
                            <div class="text-secondary en-text small" style="width: 30px">{{ round($fivePercent) }}%</div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="text-warning en-text" style="width: 80px">4 <i class="fas fa-star small"></i></div>
                            <div class="rating-bar"><div class="rating-fill" style="width: {{ $fourPercent }}%"></div></div>
                            <div class="text-secondary en-text small" style="width: 30px">{{ round($fourPercent) }}%</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="text-warning en-text" style="width: 80px">3 <i class="fas fa-star small"></i></div>
                            <div class="rating-bar"><div class="rating-fill" style="width: {{ $threePercent }}%"></div></div>
                            <div class="text-secondary en-text small" style="width: 30px">{{ round($threePercent) }}%</div>
                        </div>
                    </div>
                </div>

                <hr class="border-secondary border-opacity-25 mb-4">

                @forelse($approvedReviews->take(5) as $review)
                <div class="d-flex gap-3 mb-4">
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
                        <span class="text-white fw-bold en-text">{{ strtoupper(substr($review->user->name ?? 'U', 0, 2)) }}</span>
                    </div>
                    <div>
                        <h6 class="fw-bold text-white m-0 mb-1">{{ $review->user->name ?? 'مستخدم' }}</h6>
                        <div class="text-warning small en-text mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="text-secondary mb-0">{{ $review->comment }}</p>
                    </div>
                </div>
                @empty
                <p class="text-secondary text-center mb-0">لا توجد تقييمات بعد</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
