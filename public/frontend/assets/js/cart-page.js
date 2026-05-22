/**
 * Server-rendered cart page: AJAX updates without full page reload.
 */
(function () {
  if (!document.getElementById('cart-server-rendered') || !window.CART_ROUTES) {
    return;
  }

  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  let busy = false;

  function urlWithId(template, id) {
    return template.replace('__ID__', encodeURIComponent(id));
  }

  function setBusy(state) {
    busy = state;
    const root = document.getElementById('cart-server-rendered');
    if (root) {
      root.classList.toggle('cart-is-busy', state);
      root.querySelectorAll('[data-cart-qty], [data-cart-remove], [data-cart-clear]').forEach((el) => {
        el.disabled = state;
      });
    }
    const couponBtn = document.querySelector('#cart-coupon-form button');
    if (couponBtn) couponBtn.disabled = state;
  }

  function updateHeaderBadge(count) {
    document.querySelectorAll('.cart-badge').forEach((badge) => {
      badge.textContent = count;
      if (count > 0) {
        badge.style.transform = 'scale(1.2)';
        setTimeout(() => { badge.style.transform = 'scale(1)'; }, 200);
      } else {
        badge.style.transform = 'scale(0)';
      }
    });
  }

  function showCouponError(message) {
    const el = document.getElementById('cart-coupon-error');
    if (!el) return;
    const span = el.querySelector('span');
    if (message) {
      if (span) span.textContent = message;
      el.classList.remove('d-none');
    } else {
      el.classList.add('d-none');
      if (span) span.textContent = '';
    }
    document.querySelectorAll('.cart-coupon-error-server').forEach((n) => n.remove());
  }

  function applyCartPayload(data) {
    if (data.html?.items) {
      const rendered = document.getElementById('cart-server-rendered');
      if (rendered) {
        rendered.outerHTML = data.html.items;
      }
    }
    if (data.html?.summary) {
      const summary = document.getElementById('cart-summary-root');
      if (summary) {
        summary.outerHTML = data.html.summary;
      }
    }
    if (typeof data.cart_count === 'number') {
      updateHeaderBadge(data.cart_count);
    }
  }

  async function cartRequest(url, options = {}) {
    if (busy) return null;
    setBusy(true);
    try {
      const res = await fetch(url, {
        credentials: 'same-origin',
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrf,
          ...(options.headers || {}),
        },
        ...options,
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) {
        const msg = data.message
          || (data.errors && (data.errors.coupon?.[0] || data.errors.quantity?.[0] || data.errors.cart?.[0]))
          || 'تعذر تحديث السلة';
        if (typeof showToast === 'function') {
          showToast(msg, 'error');
        }
        if (data.errors?.coupon) {
          showCouponError(data.errors.coupon[0]);
        }
        return null;
      }
      applyCartPayload(data);
      if (typeof showToast === 'function' && data.message) {
        showToast(data.message, 'success');
      }
      showCouponError('');
      return data;
    } catch (e) {
      if (typeof showToast === 'function') {
        showToast('تعذر الاتصال بالخادم', 'error');
      }
      return null;
    } finally {
      setBusy(false);
    }
  }

  function patchQuantity(rowId, quantity) {
    const body = new FormData();
    body.append('_token', csrf);
    body.append('_method', 'PATCH');
    body.append('quantity', String(quantity));
    return cartRequest(urlWithId(window.CART_ROUTES.update, rowId), {
      method: 'POST',
      body,
    });
  }

  function removeItem(rowId) {
    const body = new FormData();
    body.append('_token', csrf);
    body.append('_method', 'DELETE');
    return cartRequest(urlWithId(window.CART_ROUTES.destroy, rowId), {
      method: 'POST',
      body,
    });
  }

  function clearCart() {
    const body = new FormData();
    body.append('_token', csrf);
    body.append('_method', 'DELETE');
    return cartRequest(window.CART_ROUTES.clear, {
      method: 'POST',
      body,
    });
  }

  function applyCoupon(code) {
    const body = new FormData();
    body.append('_token', csrf);
    body.append('coupon', code);
    return cartRequest(window.CART_ROUTES.applyCoupon, {
      method: 'POST',
      body,
    });
  }

  function removeCoupon() {
    const body = new FormData();
    body.append('_token', csrf);
    return cartRequest(window.CART_ROUTES.removeCoupon, {
      method: 'POST',
      body,
    });
  }

  document.addEventListener('click', (e) => {
    const root = document.getElementById('cart-server-rendered');
    if (!root) return;

    const qtyBtn = e.target.closest('[data-cart-qty]');
    if (qtyBtn && root.contains(qtyBtn)) {
      e.preventDefault();
      const rowId = qtyBtn.dataset.rowId;
      const quantity = parseInt(qtyBtn.dataset.quantity, 10);
      if (rowId && quantity >= 1) {
        patchQuantity(rowId, quantity);
      }
      return;
    }

    const removeBtn = e.target.closest('[data-cart-remove]');
    if (removeBtn && root.contains(removeBtn)) {
      e.preventDefault();
      const rowId = removeBtn.dataset.rowId;
      if (rowId) {
        removeItem(rowId);
      }
      return;
    }

    const clearBtn = e.target.closest('[data-cart-clear]');
    if (clearBtn && root.contains(clearBtn)) {
      e.preventDefault();
      if (confirm('هل تريد إفراغ السلة بالكامل؟')) {
        clearCart();
      }
      return;
    }

    const removeCouponBtn = e.target.closest('[data-cart-remove-coupon]');
    if (removeCouponBtn) {
      e.preventDefault();
      removeCoupon();
    }
  });

  document.addEventListener('submit', (e) => {
    const form = e.target.closest('#cart-coupon-form');
    if (!form) return;
    e.preventDefault();
    const input = form.querySelector('[name="coupon"]');
    const code = input?.value?.trim();
    if (!code) {
      showCouponError('يرجى إدخال كود الخصم');
      return;
    }
    applyCoupon(code);
  });
})();
