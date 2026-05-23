<!-- Quick View Modal -->
<div class="modal fade quick-view-modal" id="quickViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body p-0">
                <div class="row g-0 quick-view-modal__row">
                    <div class="col-lg-6 quick-view-gallery-col">
                        <div class="quick-view-gallery quick-view-gallery-pane">
                            <div class="quick-view-gallery-media">
                                <div class="swiper quick-view-swiper">
                                    <div class="swiper-wrapper" id="qv-swiper-wrapper"></div>
                                    <div class="swiper-button-next qv-img-next"></div>
                                    <div class="swiper-button-prev qv-img-prev"></div>
                                </div>
                            </div>
                            <div class="quick-view-thumbs-wrap">
                                <div thumbsSlider="" class="swiper quick-view-thumbs-swiper">
                                    <div class="swiper-wrapper" id="qv-thumbs-wrapper"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 quick-view-details-col">
                        <div class="quick-view-details p-3 p-lg-4 h-100 d-flex flex-column">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-glass text-accent px-3 py-1 rounded-pill" id="qv-category"></span>
                                <span class="badge bg-accent px-2 py-1 rounded-pill en-text" id="qv-brand"></span>
                            </div>
                            <h2 class="fw-bold qv-heading mb-3 lh-base" id="qv-title" style="font-size: 1.5rem;"></h2>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="product-rating"><span class="stars en-text text-warning" id="qv-stars"></span><span class="count en-text" id="qv-reviews"></span></div>
                                <span class="text-success small" id="qv-stock"><i class="fas fa-check-circle me-1"></i></span>
                            </div>
                            <hr class="qv-divider my-2">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <h3 class="fw-bold text-accent m-0 en-text" id="qv-price" style="font-size: 2rem;"></h3>
                                <h5 class="qv-muted text-decoration-line-through opacity-75 m-0 en-text" id="qv-old-price"></h5>
                                <span class="badge bg-danger px-3 py-2" id="qv-discount"></span>
                            </div>
                            <p class="qv-muted lh-lg mb-4" id="qv-description"></p>
                            <a href="#" class="text-accent small mb-3 d-inline-block" id="qv-details-link">عرض التفاصيل الكاملة <i class="fas fa-arrow-left ms-1"></i></a>
                            <div class="mb-4" id="qv-color-section"><h6 class="fw-bold qv-heading mb-3">اللون:</h6><div class="color-options" id="qv-colors"></div></div>
                            <div class="mb-4"><h6 class="fw-bold qv-heading mb-3">الكمية:</h6><div class="quantity-selector"><button type="button" onclick="qvChangeQty(-1)">-</button><input type="number" id="qv-qty" value="1" min="1" max="10"><button type="button" onclick="qvChangeQty(1)">+</button></div></div>
                            <div class="d-flex gap-3 mt-auto"><button class="btn btn-accent py-3 fw-bold flex-grow-1" id="qv-add-cart"><i class="fas fa-cart-plus ms-2"></i> أضف إلى السلة</button><button class="btn btn-glass py-3" style="width: 55px;" id="qv-wishlist"><i class="far fa-heart"></i></button></div>
                            <div class="row g-2 mt-3"><div class="col-4 text-center"><div class="glass-panel qv-feature-tile p-2"><i class="fas fa-download text-accent mb-1 d-block"></i><small class="qv-muted">تسليم رقمي</small></div></div><div class="col-4 text-center"><div class="glass-panel qv-feature-tile p-2"><i class="fas fa-shield-halved text-accent mb-1 d-block"></i><small class="qv-muted">ضمان سنتين</small></div></div><div class="col-4 text-center"><div class="glass-panel qv-feature-tile p-2"><i class="fas fa-undo text-accent mb-1 d-block"></i><small class="qv-muted">إرجاع 30 يوم</small></div></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
