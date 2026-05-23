const PRODUCT_ROUTE = '/product/';

document.addEventListener('DOMContentLoaded', () => {
  const themeToggleBottons = document.querySelectorAll('.theme-toggle');
  const htmlTag = document.documentElement;
  
  const savedTheme = localStorage.getItem('lms_theme') || 'dark';
  htmlTag.setAttribute('data-theme', savedTheme);
  updateThemeIcons(savedTheme);

  themeToggleBottons.forEach(btn => {
    btn.addEventListener('click', () => {
      const currentTheme = htmlTag.getAttribute('data-theme');
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      
      htmlTag.setAttribute('data-theme', newTheme);
      localStorage.setItem('lms_theme', newTheme);
      updateThemeIcons(newTheme);
    });
  });

  function updateThemeIcons(theme) {
    themeToggleBottons.forEach(btn => {
      if (theme === 'dark') {
        btn.innerHTML = '<i class="fas fa-sun"></i>';
      } else {
        btn.innerHTML = '<i class="fas fa-moon"></i>';
      }
    });
  }

  updateCartBadge();
  updateWishlistBadge();

  initMainNav();
  initTypingAnimation();
  initCounters();
  initScrollAnimations();

  if(document.getElementById('home-products-swiper')) {
    renderHomeProductsSwiper();
  }
  
  if(document.getElementById('home-products-container')) {
    renderHomeProducts();
  }
  
  const productsContainer = document.getElementById('all-products-container');
  if (productsContainer && productsContainer.dataset.serverRendered !== '1') {
    initProductsPage();
  }

  const categoryProductsSection = document.getElementById('category-products-section');
  if (categoryProductsSection && !categoryProductsSection.dataset.serverRendered) {
    initCategoriesPage();
  }

  if(document.getElementById('cart-items-container') && !document.getElementById('cart-server-rendered')) {
    initCartPage();
  }

  const checkoutItemsEl = document.getElementById('checkout-order-items');
  if(checkoutItemsEl && !checkoutItemsEl.dataset.serverRendered) {
    initCheckoutPage();
  }

  if(document.getElementById('wishlist-items-container')) {
    initWishlistPage();
  }

  const qvModal = document.getElementById('quickViewModal');
  if (qvModal) {
    qvModal.addEventListener('hidden.bs.modal', () => destroyQvSwipers());
  }
});

function getCart() {
  const cart = localStorage.getItem('lms_cart');
  return cart ? JSON.parse(cart) : [];
}

function saveCart(cart) {
  localStorage.setItem('lms_cart', JSON.stringify(cart));
}

function updateCartBadge() {
  const cart = getCart();
  const badges = document.querySelectorAll('.cart-badge');
  badges.forEach(b => {
      b.textContent = cart.reduce((sum, item) => sum + (item.qty || 1), 0);
      if(cart.length > 0) {
          b.style.transform = 'scale(1.2)';
          setTimeout(() => b.style.transform = 'scale(1)', 200);
      } else {
          b.style.transform = 'scale(0)';
      }
  });
}

function getWishlist() {
  const wl = localStorage.getItem('lms_wishlist');
  return wl ? JSON.parse(wl) : [];
}

function saveWishlist(wl) {
  localStorage.setItem('lms_wishlist', JSON.stringify(wl));
}

function updateWishlistBadge() {
  const wl = getWishlist();
  const badges = document.querySelectorAll('.wishlist-badge');
  badges.forEach(b => {
      b.textContent = wl.length;
      if(wl.length > 0) {
          b.style.transform = 'scale(1.2)';
          setTimeout(() => b.style.transform = 'scale(1)', 200);
      } else {
          b.style.transform = 'scale(0)';
      }
  });
}

function addToCart(product) {
  const cart = getCart();
  const exists = cart.find(item => item.id === product.id);
  
  if (exists) {
    exists.qty = (exists.qty || 1) + 1;
    saveCart(cart);
    updateCartBadge();
    showToast('تم زيادة الكمية في السلة', 'success');
  } else {
    product.qty = 1;
    cart.push(product);
    saveCart(cart);
    updateCartBadge();
    showToast('تمت الإضافة للسلة!', 'success');
  }
}

function removeFromCart(id) {
  let cart = getCart();
  cart = cart.filter(item => item.id !== id);
  saveCart(cart);
  updateCartBadge();
  if(document.getElementById('cart-items-container')) {
    initCartPage();
  }
}

function updateCartQty(id, delta) {
  let cart = getCart();
  const item = cart.find(i => i.id === id);
  if (item) {
    item.qty = Math.max(1, (item.qty || 1) + delta);
    saveCart(cart);
    updateCartBadge();
    if(document.getElementById('cart-items-container')) {
      initCartPage();
    }
  }
}

function clearCart() {
  saveCart([]);
  updateCartBadge();
  if(document.getElementById('cart-items-container')) {
    initCartPage();
  }
}

function toggleWishlist(product) {
  let wl = getWishlist();
  const idx = wl.findIndex(item => item.id === product.id);
  
  if (idx > -1) {
    wl.splice(idx, 1);
    showToast('تم الإزالة من المفضلة', 'warning');
  } else {
    wl.push(product);
    showToast('تمت الإضافة للمفضلة!', 'success');
  }
  
  saveWishlist(wl);
  updateWishlistBadge();
  
  const heartBtn = document.querySelector(`.product-action-btn[data-wishlist-id="${product.id}"]`);
  if (heartBtn) {
    heartBtn.classList.toggle('wishlisted', idx === -1);
    heartBtn.innerHTML = idx === -1 ? '<i class="fas fa-heart"></i>' : '<i class="far fa-heart"></i>';
  }
  
  if(document.getElementById('wishlist-items-container')) {
    initWishlistPage();
  }
}

function showToast(title, type='success') {
  let toastContainer = document.getElementById('toast-container');
  if (!toastContainer) {
    toastContainer = document.createElement('div');
    toastContainer.id = 'toast-container';
    toastContainer.style.position = 'fixed';
    toastContainer.style.bottom = '20px';
    toastContainer.style.left = '20px';
    toastContainer.style.zIndex = '9999';
    document.body.appendChild(toastContainer);
  }

  const toast = document.createElement('div');
  toast.className = `toast align-items-center text-white border-0 glass-panel mb-2`;
  toast.setAttribute('role', 'alert');
  toast.setAttribute('aria-live', 'assertive');
  toast.setAttribute('aria-atomic', 'true');
  toast.style.background = type === 'success' ? 'rgba(40, 167, 69, 0.85)' : type === 'warning' ? 'rgba(255, 193, 7, 0.85)' : 'rgba(220, 53, 69, 0.85)';
  
  toast.innerHTML = `
    <div class="d-flex align-items-center px-3 py-2" style="direction: rtl; gap: 10px;">
      <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'times-circle'}"></i>
      <span class="toast-body py-0 px-0 text-white">${title}</span>
      <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  `;
  
  toastContainer.appendChild(toast);
  
  try {
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
  } catch(e) {
    setTimeout(() => toast.remove(), 3500);
  }
}

// ============================================
// Products Data
// ============================================
const productsData = [
  { id: 1, title: 'سماعات لاسلكية بتقنية إلغاء الضوضاء', category: 'electronics', categoryName: 'إلكترونيات', brand: 'Sony', rating: 4.8, reviews: 3240, oldPrice: 299, newPrice: 149, badge: 'الأكثر مبيعاً', badgeType: 'hot', imgIcon: 'fa-headphones', stock: 'in-stock', stockText: 'متوفر', colors: ['#1a1a1a', '#ffffff', '#2563eb'], images: ['https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&q=80', 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=800&q=80', 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=800&q=80', 'https://images.unsplash.com/photo-1524678606370-a47ad25cb82a?w=800&q=80'] },
  { id: 2, title: 'ساعة ذكية بشاشة AMOLED متطورة', category: 'electronics', categoryName: 'إلكترونيات', brand: 'Samsung', rating: 4.9, reviews: 2100, oldPrice: 450, newPrice: 299, badge: 'جديد', badgeType: 'new', imgIcon: 'fa-clock', stock: 'in-stock', stockText: 'متوفر', colors: ['#1a1a1a', '#c0c0c0', '#ef4444'], images: ['https://images.unsplash.com/photo-1546868871-af0de0ae72be?w=800&q=80', 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&q=80', 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=800&q=80', 'https://images.unsplash.com/photo-1508685096489-7aacd834514e?w=800&q=80'] },
  { id: 3, title: 'حقيبة ظهر عصرية مقاومة للماء', category: 'accessories', categoryName: 'إكسسوارات', brand: 'Nike', rating: 4.7, reviews: 1560, oldPrice: 120, newPrice: 79, badge: '', badgeType: '', imgIcon: 'fa-bag-shopping', stock: 'in-stock', stockText: 'متوفر', colors: ['#1a1a1a', '#3b82f6', '#22c55e'], images: ['https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&q=80', 'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?w=800&q=80', 'https://images.unsplash.com/photo-1581605405669-fcdf81165afa?w=800&q=80', 'https://images.unsplash.com/photo-1547949003-9755b055940b?w=800&q=80'] },
  { id: 4, title: 'نظارة شمسية بولارايزد فاخرة', category: 'accessories', categoryName: 'إكسسوارات', brand: 'Ray-Ban', rating: 4.9, reviews: 890, oldPrice: 250, newPrice: 189, badge: 'الأعلى تقييماً', badgeType: 'hot', imgIcon: 'fa-glasses', stock: 'low-stock', stockText: 'كمية محدودة', colors: ['#1a1a1a', '#854d0e'], images: ['https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=800&q=80', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=800&q=80', 'https://images.unsplash.com/photo-1473496169904-658ba7c44d8a?w=800&q=80', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=800&q=80'] },
  { id: 5, title: 'حذاء رياضي خفيف الوزن للجري', category: 'sports', categoryName: 'رياضة', brand: 'Nike', rating: 4.6, reviews: 4500, oldPrice: 180, newPrice: 119, badge: '', badgeType: '', imgIcon: 'fa-shoe-prints', stock: 'in-stock', stockText: 'متوفر', colors: ['#1a1a1a', '#ffffff', '#ef4444'], images: ['https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80', 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=800&q=80', 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=800&q=80', 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=800&q=80'] },
  { id: 6, title: 'عطر فاخر برائحة خشبية مميزة', category: 'beauty', categoryName: 'الجمال', brand: 'Dior', rating: 4.8, reviews: 2800, oldPrice: 350, newPrice: 249, badge: 'موصى به', badgeType: 'new', imgIcon: 'fa-spray-can-sparkles', stock: 'in-stock', stockText: 'متوفر', colors: [], images: ['https://images.unsplash.com/photo-1541643600914-78b084683601?w=800&q=80', 'https://images.unsplash.com/photo-1523293182086-7651a899d37f?w=800&q=80', 'https://images.unsplash.com/photo-1594035910387-fea081ac46b0?w=800&q=80', 'https://images.unsplash.com/photo-1588405748880-12d1d2a59f75?w=800&q=80'] },
  { id: 7, title: 'لابتوب احترافي للأعمال والتصميم', category: 'electronics', categoryName: 'إلكترونيات', brand: 'Apple', rating: 4.9, reviews: 5600, oldPrice: 1999, newPrice: 1599, badge: 'خصم 20%', badgeType: 'hot', imgIcon: 'fa-laptop', stock: 'in-stock', stockText: 'متوفر', colors: ['#c0c0c0', '#1a1a1a'], images: ['https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&q=80', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&q=80', 'https://images.unsplash.com/photo-1531297484001-80022131f5a1?w=800&q=80', 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=800&q=80'] },
  { id: 8, title: 'طقم أواني مطبخ من الستانلس ستيل', category: 'home', categoryName: 'المنزل', brand: 'Tefal', rating: 4.5, reviews: 1200, oldPrice: 280, newPrice: 199, badge: '', badgeType: '', imgIcon: 'fa-utensils', stock: 'in-stock', stockText: 'متوفر', colors: [], images: ['https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&q=80', 'https://images.unsplash.com/photo-1584568694244-149588856520?w=800&q=80', 'https://images.unsplash.com/photo-1556909172-54557c7e4fb7?w=800&q=80', 'https://images.unsplash.com/photo-1585515320310-259814833e62?w=800&q=80'] },
  { id: 9, title: 'جاكيت شتوي أنيق مبطن بالفرو', category: 'fashion', categoryName: 'أزياء', brand: 'Zara', rating: 4.7, reviews: 980, oldPrice: 220, newPrice: 159, badge: 'موسمي', badgeType: 'new', imgIcon: 'fa-vest', stock: 'low-stock', stockText: 'كمية محدودة', colors: ['#1a1a1a', '#4a3728', '#1e3a5f'], images: ['https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800&q=80', 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800&q=80', 'https://images.unsplash.com/photo-1544022613-e87ca75a784a?w=800&q=80', 'https://images.unsplash.com/photo-1539533018447-63fcce2678e3?w=800&q=80'] },
  { id: 10, title: 'جهاز تنظيف بالبخار متعدد الاستخدامات', category: 'home', categoryName: 'المنزل', brand: 'Karcher', rating: 4.6, reviews: 750, oldPrice: 180, newPrice: 129, badge: '', badgeType: '', imgIcon: 'fa-broom', stock: 'in-stock', stockText: 'متوفر', colors: [], images: ['https://images.unsplash.com/photo-1558317374-067fb5f30001?w=800&q=80', 'https://images.unsplash.com/photo-1585421514738-01798e348b10?w=800&q=80', 'https://images.unsplash.com/photo-1563453392212-326796577464?w=800&q=80', 'https://images.unsplash.com/photo-1527515637462-cff94eebd21f?w=800&q=80'] },
  { id: 11, title: 'كاميرا احترافية بدقة 4K', category: 'electronics', categoryName: 'إلكترونيات', brand: 'Canon', rating: 4.8, reviews: 1800, oldPrice: 1200, newPrice: 899, badge: 'احترافي', badgeType: 'hot', imgIcon: 'fa-camera', stock: 'in-stock', stockText: 'متوفر', colors: ['#1a1a1a'], images: ['https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80', 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=800&q=80', 'https://images.unsplash.com/photo-1510127034890-ba27508e9f1f?w=800&q=80', 'https://images.unsplash.com/photo-1495707902641-75cac588d2e9?w=800&q=80'] },
  { id: 12, title: 'سجادة يوغا مضادة للانزلاق', category: 'sports', categoryName: 'رياضة', brand: 'Lululemon', rating: 4.5, reviews: 620, oldPrice: 60, newPrice: 39, badge: '', badgeType: '', imgIcon: 'fa-person-running', stock: 'in-stock', stockText: 'متوفر', colors: ['#7c3aed', '#2563eb', '#22c55e'], images: ['https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=800&q=80', 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=800&q=80', 'https://images.unsplash.com/photo-1575052814086-f385e2e2ad1b?w=800&q=80', 'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=800&q=80'] }
];

function renderStars(rating) {
  const full = Math.floor(rating);
  const half = rating % 1 >= 0.5 ? 1 : 0;
  const empty = 5 - full - half;
  let html = '';
  for (let i = 0; i < full; i++) html += '<i class="fas fa-star"></i>';
  if (half) html += '<i class="fas fa-star-half-alt"></i>';
  for (let i = 0; i < empty; i++) html += '<i class="far fa-star"></i>';
  return html;
}

function renderProductCard(product, view = 'grid') {
  const badgeHTML = product.badge ? `<span class="product-badge ${product.badgeType || ''}">${product.badge}</span>` : '';
  const stockHTML = `<span class="product-stock-badge ${product.stock}">${product.stockText}</span>`;
  const discount = Math.round((1 - product.newPrice / product.oldPrice) * 100);
  const isWishlisted = getWishlist().some(w => w.id === product.id);
  const productJson = JSON.stringify(product).replace(/'/g, "&#39;");
  
  const imageCount = product.images && product.images.length > 0 ? product.images.length : 0;
  const imageCountHTML = imageCount > 1 ? `<div class="image-count-badge"><i class="fas fa-images"></i> ${imageCount}</div>` : '';
  
  let imageHTML;
  if (product.images && product.images.length > 0) {
    imageHTML = `<img src="${product.images[0]}" alt="${product.title}" class="w-100 h-100 object-fit-cover">`;
  } else {
    imageHTML = `<i class="fas ${product.imgIcon} fa-4x opacity-50"></i>`;
  }
  
  if (view === 'grid') {
    return `
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="glass-card h-100 d-flex flex-column product-card">
            <div class="product-img text-white text-center">
                ${badgeHTML}
                ${stockHTML}
                ${imageHTML}
                ${imageCountHTML}
                <div class="product-actions-overlay">
                    <button class="product-action-btn ${isWishlisted ? 'wishlisted' : ''}" data-wishlist-id="${product.id}" onclick='toggleWishlist(${productJson})' title="أضف للمفضلة">
                        <i class="${isWishlisted ? 'fas' : 'far'} fa-heart"></i>
                    </button>
                    <button class="product-action-btn" onclick='addToCart(${productJson})' title="أضف للسلة">
                        <i class="fas fa-cart-plus"></i>
                    </button>
                    <button class="product-action-btn" onclick='openQuickView(${productJson})' title="عرض سريع">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="p-3 d-flex flex-column flex-grow-1">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-white bg-opacity-10 text-accent px-2 py-1">${product.categoryName}</span>
                    <span class="text-secondary small en-text">${product.brand}</span>
                </div>
                <h5 class="fw-bold mb-2 d-flex flex-grow-1">
                    <a href="${PRODUCT_ROUTE}${product.slug}" class="text-white text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem;">
                        ${product.title}
                    </a>
                </h5>
                <div class="product-rating mb-2">
                    <span class="stars en-text small">${renderStars(product.rating)}</span>
                    <span class="count en-text">(${product.reviews.toLocaleString()})</span>
                </div>
                <hr class="border-secondary mt-auto mb-2">
                <div class="product-card-footer d-flex justify-content-between align-items-center gap-2">
                    <div class="product-price">
                        <span class="current-price">$${product.newPrice}</span>
                        <span class="original-price ms-2">$${product.oldPrice}</span>
                    </div>
                    <button class="btn btn-sm btn-accent rounded-circle" style="width:35px; height:35px; padding:0" onclick='addToCart(${productJson})'>
                        <i class="fas fa-cart-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>`;
  } else {
    return `
    <div class="col-12">
        <div class="glass-card d-flex flex-column flex-md-row gap-3 product-card">
            <div class="product-img list-view-img text-white text-center flex-shrink-0" style="width: 280px; height: 200px">
                ${badgeHTML}
                ${stockHTML}
                ${imageHTML}
                ${imageCountHTML}
            </div>
            <div class="p-3 d-flex flex-column flex-grow-1 w-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-white bg-opacity-10 text-accent px-2 py-1">${product.categoryName}</span>
                    <span class="text-secondary small en-text">${product.brand}</span>
                </div>
                <h4 class="fw-bold mb-2"><a href="${PRODUCT_ROUTE}${product.slug}" class="text-white text-decoration-none">${product.title}</a></h4>
                <div class="product-rating mb-2">
                    <span class="stars en-text">${renderStars(product.rating)}</span>
                    <span class="count en-text">(${product.reviews.toLocaleString()} تقييم)</span>
                </div>
                <p class="text-secondary small d-none d-md-block mb-3">منتج عالي الجودة من علامة ${product.brand} التجارية. يتميز بتصميم عصري وأداء ممتاز يلبي جميع احتياجاتك.</p>
                <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-secondary border-opacity-25">
                    <div class="product-price">
                        <span class="current-price fs-4">$${product.newPrice}</span>
                        <span class="original-price mx-2">$${product.oldPrice}</span>
                        <span class="discount-percent ms-2">-${discount}%</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="product-action-btn" onclick='toggleWishlist(${productJson})'>
                            <i class="${isWishlisted ? 'fas' : 'far'} fa-heart"></i>
                        </button>
                        <button class="btn btn-sm btn-glass rounded-pill px-3" onclick='openQuickView(${productJson})'>
                            <i class="fas fa-eye me-1"></i> عرض سريع
                        </button>
                        <button class="btn btn-accent px-4 py-2 text-nowrap" onclick='addToCart(${productJson})'><i class="fas fa-cart-plus ms-2"></i> أضف للسلة</button>
                    </div>
                </div>
            </div>
        </div>
    </div>`;
  }
}

function renderProductSwiperSlide(product) {
  const badgeHTML = product.badge ? `<span class="product-badge ${product.badgeType || ''}">${product.badge}</span>` : '';
  const stockHTML = `<span class="product-stock-badge ${product.stock}">${product.stockText}</span>`;
  const discount = Math.round((1 - product.newPrice / product.oldPrice) * 100);
  const isWishlisted = getWishlist().some(w => w.id === product.id);
  const productJson = JSON.stringify(product).replace(/'/g, "&#39;");
  
  const imageCount = product.images && product.images.length > 0 ? product.images.length : 0;
  const imageCountHTML = imageCount > 1 ? `<div class="image-count-badge"><i class="fas fa-images"></i> ${imageCount}</div>` : '';
  
  let imageHTML;
  if (product.images && product.images.length > 0) {
    imageHTML = `<img src="${product.images[0]}" alt="${product.title}" class="w-100 h-100 object-fit-cover">`;
  } else {
    imageHTML = `<i class="fas ${product.imgIcon} fa-4x opacity-50"></i>`;
  }
  
  return `
  <div class="swiper-slide">
      <div class="glass-card h-100 d-flex flex-column product-card">
          <div class="product-img text-white text-center">
              ${badgeHTML}
              ${stockHTML}
              ${imageHTML}
              ${imageCountHTML}
              <div class="product-actions-overlay">
                  <button class="product-action-btn ${isWishlisted ? 'wishlisted' : ''}" onclick='toggleWishlist(${productJson})' title="أضف للمفضلة">
                      <i class="${isWishlisted ? 'fas' : 'far'} fa-heart"></i>
                  </button>
                  <button class="product-action-btn" onclick='addToCart(${productJson})' title="أضف للسلة">
                      <i class="fas fa-cart-plus"></i>
                  </button>
                  <button class="product-action-btn" onclick='openQuickView(${productJson})' title="عرض سريع">
                      <i class="fas fa-eye"></i>
                  </button>
              </div>
          </div>
          <div class="p-3 d-flex flex-column flex-grow-1">
              <div class="d-flex justify-content-between align-items-start mb-2">
                  <span class="badge bg-white bg-opacity-10 text-accent px-2 py-1">${product.categoryName}</span>
                  <span class="text-secondary small en-text">${product.brand}</span>
              </div>
              <h5 class="fw-bold mb-2 d-flex flex-grow-1">
                  <a href="${PRODUCT_ROUTE}${product.slug}" class="text-white text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem;">
                      ${product.title}
                  </a>
              </h5>
              <div class="product-rating mb-2">
                  <span class="stars en-text small">${renderStars(product.rating)}</span>
                  <span class="count en-text">(${product.reviews.toLocaleString()})</span>
              </div>
              <hr class="border-secondary mt-auto mb-2">
              <div class="product-card-footer d-flex justify-content-between align-items-center gap-2">
                  <div class="product-price">
                      <span class="current-price">$${product.newPrice}</span>
                      <span class="original-price ms-2">$${product.oldPrice}</span>
                  </div>
                  <button class="btn btn-sm btn-accent rounded-circle" style="width:35px; height:35px; padding:0" onclick='addToCart(${productJson})'>
                      <i class="fas fa-cart-plus"></i>
                  </button>
              </div>
          </div>
      </div>
  </div>`;
}

function renderHomeProductsSwiper() {
  const container = document.getElementById('home-products-swiper');
  let html = '';
  
  productsData.forEach(product => {
    html += renderProductSwiperSlide(product);
  });
  
  container.innerHTML = html;
}

function renderHomeProducts() {
  const container = document.getElementById('home-products-container');
  let html = '';
  
  productsData.slice(0, 8).forEach(product => {
    html += renderProductCard(product, 'grid');
  });
  
  container.innerHTML = html;
}

function initProductsPage() {
  const container = document.getElementById('all-products-container');
  const countSpan = document.getElementById('products-count');
  const searchInput = document.getElementById('search-input');
  const priceRange = document.getElementById('price-range');
  const priceVal = document.getElementById('price-val');
  const sortSelect = document.getElementById('sort-select');
  const checkboxes = document.querySelectorAll('.filter-checkbox');
  const resetBtn = document.getElementById('reset-filters');
  const viewBtns = document.querySelectorAll('.toggle-view');
  
  let currentView = 'grid';
  
  function renderFilteredProducts() {
    let filtered = [...productsData];
    
    const term = searchInput.value.toLowerCase();
    if(term) {
      filtered = filtered.filter(p => p.title.toLowerCase().includes(term) || p.brand.toLowerCase().includes(term));
    }
    
    const maxPrice = parseInt(priceRange.value);
    filtered = filtered.filter(p => p.newPrice <= maxPrice);
    
    const activeCats = Array.from(document.querySelectorAll('.filter-checkbox:checked')).map(cb => cb.value);
    if(activeCats.length > 0) {
      filtered = filtered.filter(p => activeCats.includes(p.category));
    }
    
    const sortVal = sortSelect.value;
    if(sortVal === 'price-asc') filtered.sort((a,b) => a.newPrice - b.newPrice);
    if(sortVal === 'price-desc') filtered.sort((a,b) => b.newPrice - a.newPrice);
    if(sortVal === 'newest') filtered.sort((a,b) => b.id - a.id);
    if(sortVal === 'popular') filtered.sort((a,b) => b.reviews - a.reviews);
    if(sortVal === 'rating') filtered.sort((a,b) => b.rating - a.rating);
    
    countSpan.textContent = filtered.length;
    
    if(filtered.length === 0) {
      container.innerHTML = `<div class="col-12 text-center py-5">
        <i class="fas fa-search fa-3x text-secondary mb-3 opacity-50"></i>
        <h5 class="text-white">لم يتم العثور على منتجات</h5>
        <p class="text-secondary">حاول تغيير معايير البحث أو التصفية</p>
      </div>`;
      return;
    }
    
    let html = '';
    filtered.forEach(product => {
      html += renderProductCard(product, currentView);
    });
    
    container.innerHTML = html;
  }

  searchInput.addEventListener('input', renderFilteredProducts);
  sortSelect.addEventListener('change', renderFilteredProducts);
  checkboxes.forEach(cb => cb.addEventListener('change', renderFilteredProducts));
  
  priceRange.addEventListener('input', (e) => {
    priceVal.textContent = `$${e.target.value}`;
    renderFilteredProducts();
  });
  
  resetBtn.addEventListener('click', () => {
    searchInput.value = '';
    priceRange.value = 2000;
    priceVal.textContent = '$2000';
    sortSelect.value = 'popular';
    checkboxes.forEach(cb => cb.checked = false);
    renderFilteredProducts();
  });
  
  viewBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
       viewBtns.forEach(b => b.classList.remove('active'));
       e.currentTarget.classList.add('active');
       currentView = e.currentTarget.getAttribute('data-view');
       renderFilteredProducts();
    });
  });

  setTimeout(renderFilteredProducts, 100);
}

function initCategoriesPage() {
  const catCards = document.querySelectorAll('.category-card-interactive');
  const catProductsSection = document.getElementById('category-products-section');
  const catNameSpan = document.getElementById('current-category-name');
  const dynamicContainer = document.getElementById('cat-dynamic-products');
  const filterTabs = document.querySelectorAll('#cat-filter-tabs .nav-link');
  
  let currentCategory = 'electronics';
  let currentBrand = 'all';

  catCards.forEach(card => {
    card.addEventListener('click', () => {
       currentCategory = card.getAttribute('data-cat');
       const catTitle = card.querySelector('h4').textContent;
       catNameSpan.textContent = catTitle;
       
       catProductsSection.classList.remove('d-none');
       catProductsSection.classList.remove('visible');
       catProductsSection.classList.add('section-fade-up');
       
       setTimeout(() => {
           catProductsSection.classList.add('visible');
           catProductsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
       }, 50);

       filterTabs.forEach(t => t.classList.remove('active', 'border-accent'));
       filterTabs[0].classList.add('active');
       currentBrand = 'all';

       renderCatProducts();
    });
  });

  filterTabs.forEach(tab => {
     tab.addEventListener('click', (e) => {
         filterTabs.forEach(t => t.classList.remove('active', 'border-accent'));
         e.currentTarget.classList.add('active');
         currentBrand = e.currentTarget.getAttribute('data-filter');
         
         dynamicContainer.style.opacity = '0';
         setTimeout(() => {
             renderCatProducts();
             dynamicContainer.style.opacity = '1';
         }, 300);
     });
  });

  function renderCatProducts() {
     let filtered = productsData.filter(p => p.category === currentCategory);
     
     if(currentBrand !== 'all') {
         filtered = filtered.filter(p => p.brand === currentBrand);
     }
     
     if(filtered.length === 0) {
        dynamicContainer.innerHTML = `<div class="col-12 text-center py-5">
            <h5 class="text-white">لم يتم العثور على منتجات حالياً في هذا القسم.</h5>
        </div>`;
        return;
     }

     let html = '';
     filtered.forEach(product => {
        html += renderProductCard(product, 'grid');
     });
     dynamicContainer.innerHTML = html;
     dynamicContainer.style.transition = 'opacity 0.3s ease';
  }
}

// ============================================
// Cart Page
// ============================================
function initCartPage() {
  const container = document.getElementById('cart-items-container');
  const emptyMsg  = document.getElementById('empty-cart-msg');
  const countSpan = document.getElementById('cart-page-count');
  const clearBtn  = document.getElementById('clear-cart-btn');
  const checkoutBtn = document.getElementById('checkout-btn');
  const sumOld    = document.getElementById('summary-old-total');
  const sumDisc   = document.getElementById('summary-discount');
  const sumTotal  = document.getElementById('summary-total');

  const cart = getCart();
  if (countSpan) countSpan.textContent = cart.reduce((s, i) => s + (i.qty || 1), 0);

  if (cart.length === 0) {
    container.innerHTML = '';
    if (emptyMsg) { emptyMsg.classList.remove('d-none'); container.appendChild(emptyMsg); }
    if (clearBtn) clearBtn.style.display = 'none';
    if (checkoutBtn) checkoutBtn.classList.add('disabled');
    if (sumOld) sumOld.textContent = '$0';
    if (sumDisc) sumDisc.textContent = '-$0';
    if (sumTotal) sumTotal.textContent = '$0';
    return;
  }

  if (emptyMsg) emptyMsg.classList.add('d-none');
  if (clearBtn) clearBtn.style.display = 'inline-block';
  if (checkoutBtn) checkoutBtn.classList.remove('disabled');

  let oldTotal = 0, newTotal = 0, html = '';
  cart.forEach(item => {
    const qty = item.qty || 1;
    oldTotal += (item.oldPrice || item.newPrice) * qty;
    newTotal += item.newPrice * qty;
    
    let itemImageHTML;
    if (item.images && item.images.length > 0) {
      itemImageHTML = `<img src="${item.images[0]}" alt="${item.title}" class="w-100 h-100 object-fit-cover" style="border-radius: 12px;">`;
    } else {
      itemImageHTML = `<i class="fas ${item.imgIcon || 'fa-box'} fa-2x mt-3 opacity-50"></i>`;
    }
    
    html += `
    <div class="glass-card p-3 d-flex flex-column flex-md-row gap-3 align-items-center">
      <div class="product-img text-white text-center flex-shrink-0 rounded-3" style="width:120px;height:90px;">
        ${itemImageHTML}
      </div>
      <div class="flex-grow-1 text-center text-md-start">
        <h6 class="fw-bold text-white mb-1">${item.title}</h6>
        <div class="text-secondary small"><i class="fas fa-tag text-accent me-1"></i>${item.brand || ''}</div>
        <div class="mt-2">
          <div class="cart-item-qty d-inline-flex">
            <button onclick="updateCartQty(${item.id}, -1)">-</button>
            <span>${qty}</span>
            <button onclick="updateCartQty(${item.id}, 1)">+</button>
          </div>
        </div>
      </div>
      <div class="text-center d-flex flex-column align-items-end justify-content-between py-1">
        <span class="text-accent fw-bold fs-5 en-text">$${(item.newPrice * qty).toFixed(2)}</span>
        <button class="btn btn-sm text-danger bg-transparent border-0 p-0 mt-2 text-decoration-underline small" onclick="removeFromCart(${item.id})">
          <i class="fas fa-trash-alt me-1"></i>إزالة
        </button>
      </div>
    </div>`;
  });

  container.innerHTML = html;

  if (sumOld) sumOld.textContent = '$' + oldTotal.toFixed(2);
  if (sumDisc) sumDisc.textContent = '-$' + (oldTotal - newTotal).toFixed(2);
  if (sumTotal) sumTotal.textContent = '$' + newTotal.toFixed(2);

  const applyBtn   = document.getElementById('apply-coupon');
  const couponInp  = document.getElementById('coupon-input');
  const couponMsg  = document.getElementById('coupon-msg');
  if (applyBtn) {
    const fresh = applyBtn.cloneNode(true);
    applyBtn.parentNode.replaceChild(fresh, applyBtn);
    fresh.addEventListener('click', () => {
      const val = couponInp.value.trim().toUpperCase();
      if (val === 'SAVE20') {
        sumTotal.textContent = '$' + (newTotal * 0.8).toFixed(2);
        couponMsg.textContent = 'تم تطبيق خصم 20%!';
        couponMsg.className = 'text-success small mb-4';
      } else if (val === '') {
        sumTotal.textContent = '$' + newTotal.toFixed(2);
        couponMsg.className = 'd-none';
      } else {
        couponMsg.textContent = 'كود الخصم غير صالح';
        couponMsg.className = 'text-danger small mb-4';
        sumTotal.textContent = '$' + newTotal.toFixed(2);
      }
    });
  }

  if (clearBtn) {
    const freshClear = clearBtn.cloneNode(true);
    clearBtn.parentNode.replaceChild(freshClear, clearBtn);
    freshClear.addEventListener('click', clearCart);
  }
}

// ============================================
// Checkout Page
// ============================================
function initCheckoutPage() {
  const cart = getCart();
  const itemsEl   = document.getElementById('checkout-order-items');
  const subtotalEl = document.getElementById('checkout-subtotal');
  const discEl    = document.getElementById('checkout-discount');
  const totalEl   = document.getElementById('checkout-total');
  if (!itemsEl) return;

  if (cart.length === 0) {
    itemsEl.innerHTML = '<p class="text-secondary text-center">لا توجد عناصر في السلة. <a href="products.html" class="text-accent">تصفح المنتجات</a></p>';
    return;
  }

  let old = 0, fresh = 0, html = '';
  cart.forEach(item => {
    const qty = item.qty || 1;
    old += (item.oldPrice || item.newPrice) * qty;
    fresh += item.newPrice * qty;
    html += `<div class="d-flex justify-content-between align-items-center text-secondary small border-bottom border-secondary border-opacity-25 pb-2 mb-2">
      <span class="text-white">${item.title} <span class="en-text text-secondary">x${qty}</span></span>
      <span class="en-text text-accent fw-bold">$${(item.newPrice * qty).toFixed(2)}</span>
    </div>`;
  });
  itemsEl.innerHTML = html;
  if (subtotalEl) subtotalEl.textContent = '$' + old.toFixed(2);
  if (discEl) discEl.textContent = '-$' + (old - fresh).toFixed(2);
  if (totalEl) totalEl.textContent = '$' + fresh.toFixed(2);
}

// ============================================
// Wishlist Page
// ============================================
function initWishlistPage() {
  const container = document.getElementById('wishlist-items-container');
  const emptyMsg = document.getElementById('empty-wishlist-msg');
  const countSpan = document.getElementById('wishlist-count');
  
  const wl = getWishlist();
  if (countSpan) countSpan.textContent = wl.length;
  
  if (wl.length === 0) {
    container.innerHTML = '';
    if (emptyMsg) { emptyMsg.classList.remove('d-none'); container.appendChild(emptyMsg); }
    return;
  }
  
  if (emptyMsg) emptyMsg.classList.add('d-none');
  
  let html = '';
  wl.forEach(product => {
    const imageCount = product.images && product.images.length > 0 ? product.images.length : 0;
    const imageCountHTML = imageCount > 1 ? `<div class="image-count-badge"><i class="fas fa-images"></i> ${imageCount}</div>` : '';
    
    let imageHTML;
    if (product.images && product.images.length > 0) {
      imageHTML = `<img src="${product.images[0]}" alt="${product.title}" class="w-100 h-100 object-fit-cover">`;
    } else {
      imageHTML = `<i class="fas ${product.imgIcon} fa-4x opacity-50"></i>`;
    }
    
    html += `
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="glass-card h-100 d-flex flex-column product-card wishlist-item position-relative">
            <button class="wishlist-remove-btn" onclick='toggleWishlist(${JSON.stringify(product).replace(/'/g, "&#39;")})'>
                <i class="fas fa-times"></i>
            </button>
            <div class="product-img text-white text-center">
                ${imageHTML}
                ${imageCountHTML}
            </div>
            <div class="p-3 d-flex flex-column flex-grow-1">
                <span class="badge bg-white bg-opacity-10 text-accent px-2 py-1 mb-2">${product.categoryName}</span>
                <h5 class="fw-bold mb-2 d-flex flex-grow-1">
                    <a href="${PRODUCT_ROUTE}${product.slug}" class="text-white text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem;">
                        ${product.title}
                    </a>
                </h5>
                <div class="product-rating mb-2">
                    <span class="stars en-text small">${renderStars(product.rating)}</span>
                    <span class="count en-text">(${product.reviews.toLocaleString()})</span>
                </div>
                <hr class="border-secondary mt-auto mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="product-price">
                        <span class="current-price">$${product.newPrice}</span>
                        <span class="original-price ms-2">$${product.oldPrice}</span>
                    </div>
                    <button class="btn btn-sm btn-accent rounded-circle" style="width:35px; height:35px; padding:0" onclick='addToCart(${JSON.stringify(product).replace(/'/g, "&#39;")})'>
                        <i class="fas fa-cart-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>`;
  });
  
  container.innerHTML = html;
}

// ============================================
// Card Live Preview Functions
// ============================================
function updateCardPreview() {
  const numEl    = document.getElementById('card-number-display');
  const holderEl = document.getElementById('card-holder-display');
  const expEl    = document.getElementById('card-exp-display');
  const cvvEl    = document.getElementById('card-cvv-display');
  const typeEl   = document.getElementById('card-type-icon');

  const num    = document.getElementById('card-number')?.value || '';
  const holder = document.getElementById('card-name')?.value || '';
  const exp    = document.getElementById('card-expiry')?.value || '';
  const cvv    = document.getElementById('card-cvv')?.value || '';

  if (numEl) numEl.textContent = num.padEnd(19, '\u2022').replace(/(.{4})/g, '$1 ').trim() || '\u2022\u2022\u2022\u2022 \u2022\u2022\u2022\u2022 \u2022\u2022\u2022\u2022 \u2022\u2022\u2022\u2022';
  if (holderEl) holderEl.textContent = holder.toUpperCase() || 'FULL NAME';
  if (expEl) expEl.textContent = exp || 'MM/YY';
  if (cvvEl) cvvEl.textContent = cvv ? '\u2022'.repeat(cvv.length) : '\u2022\u2022\u2022';

  if (typeEl) {
    const first = num.replace(/\s/g,'')[0];
    if (first === '4') typeEl.innerHTML = '<i class="fab fa-cc-visa fa-2x text-white opacity-75"></i>';
    else if (first === '5') typeEl.innerHTML = '<i class="fab fa-cc-mastercard fa-2x text-white opacity-75"></i>';
    else if (first === '3') typeEl.innerHTML = '<i class="fab fa-cc-amex fa-2x text-white opacity-75"></i>';
    else typeEl.innerHTML = '<i class="fab fa-cc-visa fa-2x text-white opacity-75"></i>';
  }
}

function formatCardNumber(input) {
  let v = input.value.replace(/\D/g, '').substring(0, 16);
  input.value = v.match(/.{1,4}/g)?.join(' ') || v;
}

function formatExpiry(input) {
  let v = input.value.replace(/\D/g, '').substring(0, 4);
  if (v.length > 2) v = v.substring(0, 2) + '/' + v.substring(2);
  input.value = v;
}

function flipCard(show) {
  const preview = document.getElementById('card-preview');
  if (preview) preview.classList.toggle('flipped', show);
}

function submitOrder() {
  const firstName = document.getElementById('first-name')?.value.trim();
  const email     = document.getElementById('email')?.value.trim();
  const cardNum   = document.getElementById('card-number')?.value.replace(/\s/g,'');
  const cardName  = document.getElementById('card-name')?.value.trim();
  const expiry    = document.getElementById('card-expiry')?.value.trim();
  const cvv       = document.getElementById('card-cvv')?.value.trim();

  if (!firstName || !email || cardNum.length < 16 || !cardName || expiry.length < 5 || cvv.length < 3) {
    showToast('يرجى تعبئة جميع الحقول بشكل صحيح', 'danger');
    return;
  }

  const btn = document.getElementById('submit-order');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin ms-2"></i> جارٍ المعالجة...';
  }

  setTimeout(() => {
    clearCart();
    const successEl = document.getElementById('order-success');
    if (successEl) successEl.classList.remove('d-none');
    if (btn) btn.classList.add('d-none');
    showToast('تم تأكيد طلبك بنجاح!', 'success');
    const checkoutItems = document.getElementById('checkout-order-items');
    if (checkoutItems) checkoutItems.innerHTML = '<p class="text-success text-center fw-bold"><i class="fas fa-check-circle me-2"></i>تم الدفع بنجاح!</p>';
  }, 2000);
}

// ============================================
// Animations
// ============================================
function initTypingAnimation() {
  const typingElement = document.querySelector('.typing-text');
  if(!typingElement) return;
  
  const texts = JSON.parse(typingElement.getAttribute('data-text')) || [];
  let textIndex = 0;
  let charIndex = 0;
  let isDeleting = false;
  
  function type() {
    const currentText = texts[textIndex];
    if(isDeleting) {
      typingElement.textContent = currentText.substring(0, charIndex - 1);
      charIndex--;
    } else {
      typingElement.textContent = currentText.substring(0, charIndex + 1);
      charIndex++;
    }
    
    let typeSpeed = isDeleting ? 50 : 100;
    
    if(!isDeleting && charIndex === currentText.length) {
      typeSpeed = 2000;
      isDeleting = true;
    } else if (isDeleting && charIndex === 0) {
      isDeleting = false;
      textIndex = (textIndex + 1) % texts.length;
      typeSpeed = 500;
    }
    
    setTimeout(type, typeSpeed);
  }
  
  setTimeout(type, 1000);
}

function initCounters() {
  const counters = document.querySelectorAll('.counter');
  const duration = 2000;
  
  const observerOptions = { threshold: 0.5 };
  
  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if(entry.isIntersecting) {
        const target = entry.target;
        const finalValue = parseInt(target.getAttribute('data-target'));
        const startTime = performance.now();
        
        function updateCounter(currentTime) {
          const elapsedTime = currentTime - startTime;
          if(elapsedTime < duration) {
            const currentValue = Math.floor((elapsedTime / duration) * finalValue);
            target.innerText = currentValue >= 1000 ? (currentValue/1000).toFixed(1) + 'K+' : currentValue;
            requestAnimationFrame(updateCounter);
          } else {
            target.innerText = finalValue >= 1000 ? (finalValue/1000).toFixed(1) + 'K+' : finalValue;
          }
        }
        
        requestAnimationFrame(updateCounter);
        observer.unobserve(target);
      }
    });
  }, observerOptions);
  
  counters.forEach(counter => observer.observe(counter));
}

let fadeIntersectionObserver = null;

function initMainNav() {
  const nav = document.getElementById('siteMainNav');
  if (!nav) {
    return;
  }

  const updateScrolled = () => {
    nav.classList.toggle('is-scrolled', window.scrollY > 12);
  };

  updateScrolled();
  window.addEventListener('scroll', updateScrolled, { passive: true });

  const searchDesktop = document.getElementById('mainNavSearch');
  const searchMobile = document.getElementById('mainNavSearchMobile');

  document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      const desktopWrap = searchDesktop?.closest('.main-nav__search');
      const useDesktop = desktopWrap && window.getComputedStyle(desktopWrap).display !== 'none';
      const target = useDesktop ? searchDesktop : searchMobile;
      target?.focus();
      if (target?.value) {
        target.select();
      }
    }
  });
}

function getFadeIntersectionObserver() {
  if (!fadeIntersectionObserver) {
    fadeIntersectionObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          fadeIntersectionObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });
  }

  return fadeIntersectionObserver;
}

/**
 * Observe .section-fade-up elements inside root (or document) for scroll reveal.
 */
function observeFadeElements(root = document) {
  const scope = root && root.querySelectorAll ? root : document;
  const elements = scope.querySelectorAll('.section-fade-up:not(.visible)');

  if (!elements.length) {
    return;
  }

  const observer = getFadeIntersectionObserver();
  elements.forEach((el) => observer.observe(el));
}

function initScrollAnimations() {
  observeFadeElements(document);
}

window.observeFadeElements = observeFadeElements;

// ============================================
// Quick View Modal
// ============================================
function qvChangeQty(delta) {
  const input = document.getElementById('qv-qty');
  if (!input) return;
  let val = parseInt(input.value) + delta;
  if (val < 1) val = 1;
  if (val > 10) val = 10;
  input.value = val;
}

function formatQvPrice(amount) {
  const n = parseFloat(amount);
  if (Number.isNaN(n)) return '';
  return n.toLocaleString('ar-SA', { minimumFractionDigits: 0, maximumFractionDigits: 2 }) + ' ر.س';
}

async function openQuickView(product) {
  if (typeof product === 'string') {
    try {
      const res = await fetch(PRODUCT_ROUTE + product + '/quick-view-data', {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      });
      if (!res.ok) throw new Error('not found');
      product = await res.json();
    } catch (e) {
      window.location.href = PRODUCT_ROUTE + product;
      return;
    }
  }

  populateQuickViewModal(product);
}

function populateQuickViewModal(product) {
  const modalEl = document.getElementById('quickViewModal');
  if (!modalEl) return;

  const titleEl = document.getElementById('qv-title');
  const catEl = document.getElementById('qv-category');
  const brandEl = document.getElementById('qv-brand');
  const priceEl = document.getElementById('qv-price');
  const oldPriceEl = document.getElementById('qv-old-price');
  const stockEl = document.getElementById('qv-stock');
  const discountEl = document.getElementById('qv-discount');
  const starsEl = document.getElementById('qv-stars');
  const reviewsEl = document.getElementById('qv-reviews');
  const badgeEl = document.getElementById('qv-badge');
  const descEl = document.getElementById('qv-description');
  const detailsLink = document.getElementById('qv-details-link');
  const colorContainer = document.getElementById('qv-colors');
  const colorSection = document.getElementById('qv-color-section');

  if (titleEl) titleEl.textContent = product.title;
  if (catEl) catEl.textContent = product.categoryName || '';
  if (brandEl) brandEl.textContent = product.brand || '';
  if (priceEl) priceEl.textContent = formatQvPrice(product.newPrice);
  if (oldPriceEl) {
    const showOld = product.hasDiscount || (product.oldPrice && product.oldPrice > product.newPrice);
    if (showOld) {
      oldPriceEl.textContent = formatQvPrice(product.oldPrice);
      oldPriceEl.style.display = '';
    } else {
      oldPriceEl.textContent = '';
      oldPriceEl.style.display = 'none';
    }
  }
  if (descEl) descEl.textContent = product.description || '';
  if (detailsLink) {
    detailsLink.href = product.productUrl || (PRODUCT_ROUTE + (product.slug || ''));
  }
  if (stockEl) {
    const inStock = product.inStock !== false;
    stockEl.className = inStock ? 'text-success small' : 'text-danger small';
    stockEl.innerHTML = '<i class="fas ' + (inStock ? 'fa-check-circle' : 'fa-times-circle') + ' me-1"></i> ' + (product.stockText || '');
  }

  const hasDiscount = product.hasDiscount || (product.oldPrice > product.newPrice);
  const discount = hasDiscount ? Math.round((1 - product.newPrice / product.oldPrice) * 100) : 0;
  if (discountEl) {
    if (discount > 0) {
      discountEl.textContent = '-' + discount + '%';
      discountEl.style.display = '';
    } else {
      discountEl.style.display = 'none';
    }
  }

  if (starsEl) starsEl.innerHTML = renderStars(product.rating);
  if (reviewsEl) reviewsEl.textContent = '(' + product.reviews.toLocaleString() + ')';

  if (badgeEl) {
    if (product.badge) {
      badgeEl.textContent = product.badge;
      badgeEl.style.display = 'inline-block';
      badgeEl.className = 'product-badge ' + (product.badgeType || '');
    } else {
      badgeEl.style.display = 'none';
    }
  }

  populateQvGallery(modalEl, product);

  if (colorContainer && colorSection) {
    if (product.colors && product.colors.length > 0) {
      colorSection.style.display = 'block';
      colorContainer.innerHTML = product.colors.map((c, i) => 
        '<div class="color-option ' + (i === 0 ? 'active' : '') + '" style="background: ' + c + ';" onclick="this.parentElement.querySelectorAll(\'.color-option\').forEach(o=>o.classList.remove(\'active\'));this.classList.add(\'active\')"></div>'
      ).join('');
    } else {
      colorSection.style.display = 'none';
    }
  }

  const qtyEl = document.getElementById('qv-qty');
  if (qtyEl) qtyEl.value = 1;

  const addCartBtn = document.getElementById('qv-add-cart');
  if (addCartBtn) {
    addCartBtn.disabled = product.inStock === false;
    addCartBtn.onclick = async function() {
      const qty = parseInt(document.getElementById('qv-qty').value, 10) || 1;
      const cartUrl = (window.FRONTEND_ROUTES && window.FRONTEND_ROUTES.cartStore) || '/cart';
      const token = document.querySelector('meta[name="csrf-token"]')?.content;

      if (product.id && token) {
        const formData = new FormData();
        formData.append('product_id', product.id);
        formData.append('quantity', qty);
        try {
          const res = await fetch(cartUrl, {
            method: 'POST',
            body: formData,
            headers: {
              'X-CSRF-TOKEN': token,
              Accept: 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
            },
          });
          if (res.ok) {
            showToast('تمت الإضافة للسلة!', 'success');
          } else {
            showToast('تعذرت الإضافة للسلة', 'error');
            return;
          }
        } catch (err) {
          showToast('تعذرت الإضافة للسلة', 'error');
          return;
        }
      } else {
        for (let i = 0; i < qty; i++) addToCart(product);
        showToast('تمت الإضافة للسلة!', 'success');
      }

      const modal = bootstrap.Modal.getInstance(modalEl);
      if (modal) modal.hide();
    };
  }

  const isWishlisted = getWishlist().some(w => w.id === product.id);
  const wishlistBtn = document.getElementById('qv-wishlist');
  if (wishlistBtn) {
    const icon = wishlistBtn.querySelector('i');
    icon.className = isWishlisted ? 'fas fa-heart' : 'far fa-heart';
    icon.style.color = isWishlisted ? '#ef4444' : '';
    wishlistBtn.onclick = function() {
      toggleWishlist(product);
      icon.classList.toggle('far');
      icon.classList.toggle('fas');
      icon.style.color = icon.classList.contains('fas') ? '#ef4444' : '';
    };
  }

  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

  const onQvShown = () => {
    initQvSwipers(modalEl);
    modalEl.removeEventListener('shown.bs.modal', onQvShown);
  };
  modalEl.addEventListener('shown.bs.modal', onQvShown);

  modal.show();
}

function destroyQvSwipers() {
  if (window.qvMainSwiper) {
    try { window.qvMainSwiper.destroy(true, true); } catch (e) { /* noop */ }
    window.qvMainSwiper = null;
  }
  if (window.qvThumbsSwiper) {
    try { window.qvThumbsSwiper.destroy(true, true); } catch (e) { /* noop */ }
    window.qvThumbsSwiper = null;
  }
}

function populateQvGallery(modalEl, product) {
  const mainWrapper = modalEl.querySelector('#qv-swiper-wrapper');
  const thumbsWrapper = modalEl.querySelector('#qv-thumbs-wrapper');
  const mainSwiperEl = modalEl.querySelector('.quick-view-swiper');
  const thumbsSwiperEl = modalEl.querySelector('.quick-view-thumbs-swiper');
  const nextBtn = modalEl.querySelector('.qv-img-next');
  const prevBtn = modalEl.querySelector('.qv-img-prev');

  if (!mainWrapper || !mainSwiperEl) return;

  destroyQvSwipers();
  mainSwiperEl.classList.remove('qv-static-gallery');
  mainWrapper.style.transform = '';

  const images = product.images && product.images.length ? product.images : [];
  const badgeHTML = product.badge ? `<span class="product-badge ${product.badgeType || ''}">${product.badge}</span>` : '';
  const slideImg = (src) =>
    `<div class="qv-slide-frame position-relative">${badgeHTML}<img src="${src}" alt="${product.title}" class="qv-slide-img"></div>`;

  if (images.length === 0) {
    mainWrapper.innerHTML = `<div class="swiper-slide"><div class="qv-slide-frame qv-slide-frame--placeholder position-relative">${badgeHTML}<i class="fas ${product.imgIcon || 'fa-image'} fa-5x qv-placeholder-icon opacity-50"></i></div></div>`;
    if (thumbsWrapper) thumbsWrapper.innerHTML = '';
    modalEl._qvSlideCount = 1;
    modalEl._qvUseSwiper = false;
    if (thumbsSwiperEl) thumbsSwiperEl.style.display = 'none';
    if (nextBtn) nextBtn.style.display = 'none';
    if (prevBtn) prevBtn.style.display = 'none';
    return;
  }

  if (images.length === 1) {
    mainSwiperEl.classList.add('qv-static-gallery');
    mainWrapper.innerHTML = `<div class="qv-static-image w-100">${slideImg(images[0])}</div>`;
    if (thumbsWrapper) thumbsWrapper.innerHTML = '';
    modalEl._qvSlideCount = 1;
    modalEl._qvUseSwiper = false;
    if (thumbsSwiperEl) thumbsSwiperEl.style.display = 'none';
    if (nextBtn) nextBtn.style.display = 'none';
    if (prevBtn) prevBtn.style.display = 'none';
    return;
  }

  let mainHtml = '';
  let thumbsHtml = '';
  images.forEach((img) => {
    mainHtml += `<div class="swiper-slide">${slideImg(img)}</div>`;
    thumbsHtml += `<div class="swiper-slide"><div class="qv-thumb"><img src="${img}" alt="thumb" class="w-100 h-100 object-fit-cover"></div></div>`;
  });

  mainWrapper.innerHTML = mainHtml;
  if (thumbsWrapper) thumbsWrapper.innerHTML = thumbsHtml;

  modalEl._qvSlideCount = images.length;
  modalEl._qvUseSwiper = true;
  mainSwiperEl.style.display = 'block';
  if (thumbsSwiperEl) thumbsSwiperEl.style.display = 'block';
  if (nextBtn) nextBtn.style.display = '';
  if (prevBtn) prevBtn.style.display = '';
}

function initQvSwipers(modalEl) {
  if (!modalEl._qvUseSwiper) return;

  const mainSwiperEl = modalEl.querySelector('.quick-view-swiper');
  const thumbsSwiperEl = modalEl.querySelector('.quick-view-thumbs-swiper');
  const nextBtn = modalEl.querySelector('.qv-img-next');
  const prevBtn = modalEl.querySelector('.qv-img-prev');
  const slideCount = modalEl._qvSlideCount || 1;

  if (!mainSwiperEl || slideCount < 2) return;

  destroyQvSwipers();

  const isRtl = document.documentElement.getAttribute('dir') === 'rtl';
  const useLoop = slideCount >= 3;

  if (thumbsSwiperEl) {
    window.qvThumbsSwiper = new Swiper(thumbsSwiperEl, {
      spaceBetween: 10,
      slidesPerView: Math.min(4, slideCount),
      freeMode: true,
      watchSlidesProgress: true,
      rtl: isRtl,
    });
  }

  window.qvMainSwiper = new Swiper(mainSwiperEl, {
    spaceBetween: 0,
    slidesPerView: 1,
    centeredSlides: false,
    loop: useLoop,
    rtl: isRtl,
    observer: true,
    observeParents: true,
    watchOverflow: true,
    navigation: {
      nextEl: nextBtn,
      prevEl: prevBtn,
    },
    thumbs: window.qvThumbsSwiper ? { swiper: window.qvThumbsSwiper } : undefined,
  });

  requestAnimationFrame(() => {
    window.qvMainSwiper?.update();
    window.qvThumbsSwiper?.update();
  });
}
