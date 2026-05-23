<div class="bg-gradient-opacity pt-5 pb-3 mt-5">
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 m-0 text-secondary">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}" class="text-decoration-none text-secondary">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('frontend.shop.index') }}" class="text-decoration-none text-secondary">المنتجات</a></li>
                @if($product->category)
                <li class="breadcrumb-item"><a href="{{ route('frontend.category.show', $product->category->slug) }}" class="text-decoration-none text-secondary">{{ $product->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active text-white" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<main class="container py-4">
    <div id="toast-container"></div>
    <div class="row g-5">
        <div class="col-lg-6">
            <div class="section-fade-up">
                <div class="product-gallery">
                    <div class="glass-card p-3">
                        <div class="swiper product-image-swiper mb-3">
                            <div class="swiper-wrapper" id="product-swiper-wrapper">
                                @forelse($product->images as $image)
                                <div class="swiper-slide">
                                    <div class="product-img text-white text-center rounded-3" style="height: 400px;">
                                        <img src="{{ product_image_url($image->path, $product->id) }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover" style="border-radius: 12px;">
                                    </div>
                                </div>
                                @empty
                                <div class="swiper-slide">
                                    <div class="product-img text-white text-center rounded-3" style="height: 400px;">
                                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover" style="border-radius: 12px;">
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            <div class="swiper-button-next product-img-next"></div>
                            <div class="swiper-button-prev product-img-prev"></div>
                        </div>
                        @if($product->images->count() > 1)
                        <div thumbsSlider="" class="swiper product-thumbs-swiper">
                            <div class="swiper-wrapper" id="product-thumbs-wrapper">
                                @foreach($product->images as $image)
                                <div class="swiper-slide">
                                    <div class="product-img text-white text-center rounded-3" style="height: 80px; cursor: pointer;">
                                        <img src="{{ product_image_url($image->path, $product->id) }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover" style="border-radius: 8px;">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="section-fade-up product-info">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($product->category)
                    <span class="badge bg-glass text-accent px-3 py-2 rounded-pill">{{ $product->category->name }}</span>
                    @endif
                    @if($product->is_bestseller)
                    <span class="badge bg-danger px-3 py-2 rounded-pill">الأكثر مبيعاً</span>
                    @elseif($product->is_new)
                    <span class="badge bg-success px-3 py-2 rounded-pill">جديد</span>
                    @endif
                </div>
                <h1 class="product-title mb-3">{{ $product->name }}</h1>
                
                <div class="product-meta-info">
                    @if($product->brand)
                    <div class="product-meta-item">
                        <i class="fas fa-tag"></i>
                        <span class="en-text">{{ $product->brand->name }}</span>
                    </div>
                    @endif
                    <div class="product-meta-item">
                        <span class="stars en-text text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($product->reviews_avg_rating ?? 0))
                                    <i class="fas fa-star"></i>
                                @elseif($i - 0.5 <= ($product->reviews_avg_rating ?? 0))
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </span>
                        <span class="en-text text-secondary">({{ $product->reviews->count() }} تقييم)</span>
                    </div>
                    <div class="product-meta-item">
                        <i class="fas fa-download"></i>
                        <span class="{{ $product->in_stock ? 'text-success' : 'text-danger' }}">
                            {{ $product->in_stock ? 'متاح للشراء' : 'غير متاح' }}
                        </span>
                    </div>
                </div>

                <hr class="border-secondary border-opacity-25">

                <div class="d-flex align-items-center gap-3 mb-4">
                    <h2 class="fw-bold text-accent m-0 en-text">{{ $product->price }} ر.س</h2>
                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                    <h4 class="text-secondary text-decoration-line-through opacity-75 m-0 en-text">{{ $product->compare_at_price }} ر.س</h4>
                    <span class="badge bg-danger">خصم {{ $product->discount_percentage }}%</span>
                    @endif
                </div>

                <p class="text-secondary lh-lg mb-4">{{ $product->short_description ?? $product->description }}</p>

                @if($product->colors)
                <div class="mb-4">
                    <h6 class="fw-bold text-white mb-3">اللون:</h6>
                    <div class="color-options" id="color-options">
                        @foreach(explode(',', $product->colors) as $index => $color)
                        <div class="color-option {{ $index === 0 ? 'active' : '' }}" style="background: {{ trim($color) }};" data-color="{{ trim($color) }}"></div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="mb-4">
                    <h6 class="fw-bold text-white mb-3">الكمية:</h6>
                    <div class="quantity-selector">
                        <button onclick="changeQty(-1)">-</button>
                        <input type="number" id="qty-input" value="1" min="1" max="99">
                        <button onclick="changeQty(1)">+</button>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-3 mb-4 align-items-stretch">
                    <form method="POST" action="{{ route('frontend.cart.store') }}" class="flex-grow-1 d-flex" id="product-add-to-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" id="cart-qty-input" value="1">
                        <button type="submit" class="btn btn-accent py-3 fw-bold fs-5 shadow flex-grow-1 w-100" @disabled(! $product->in_stock)>
                            <i class="fas fa-cart-plus ms-2"></i> أضف إلى السلة
                        </button>
                    </form>
                    <button type="button" class="btn btn-glass py-3 fw-bold text-white" style="width: 55px;" id="wishlist-btn" data-product-id="{{ $product->id }}">
                        <i class="far fa-heart"></i>
                    </button>
                </div>

                <hr class="border-secondary border-opacity-25 my-4">

                @if($product->features)
                <h6 class="fw-bold text-white mb-3">مميزات المنتج:</h6>
                <ul class="list-unstyled text-secondary small lh-lg m-0">
                    @foreach(explode("\n", $product->features) as $feature)
                    <li class="mb-2 d-flex align-items-center gap-3"><i class="fas fa-check text-accent w-20px text-center"></i> {{ trim($feature) }}</li>
                    @endforeach
                </ul>
                @endif

                <hr class="border-secondary border-opacity-25 my-4">
                <div class="d-flex gap-3">
                    <a href="#" class="text-decoration-none text-secondary small fw-bold"><i class="fas fa-share-alt me-2"></i> مشاركة المنتج</a>
                    @if($product->is_digital ?? true)
                        <span class="text-secondary small fw-bold"><i class="fas fa-download me-2"></i> منتج رقمي — تسليم فوري</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
