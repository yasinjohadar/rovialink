/**
 * Shop / category catalog: AJAX filters without full page reload.
 */
(function () {
  const form = document.getElementById('shop-filters-form');
  if (!form) return;

  let searchTimer = null;
  let priceTimer = null;
  let activeController = null;
  let isPopState = false;

  function getResultsRoot() {
    return document.getElementById('shop-results-root');
  }

  function buildParamsFromForm() {
    const params = new URLSearchParams();

    new FormData(form).forEach((value, key) => {
      const trimmed = String(value).trim();
      if (trimmed === '') {
        return;
      }
      if (key === 'min_price' && (trimmed === '0' || Number(trimmed) === 0)) {
        return;
      }
      params.set(key, value);
    });

    const sortSelect = document.getElementById('sort-select');
    if (sortSelect && sortSelect.value) {
      params.set('sort', sortSelect.value);
    }

    return params;
  }

  function buildFetchUrl(baseUrl, params) {
    const url = new URL(baseUrl, window.location.origin);
    params.forEach((value, key) => {
      url.searchParams.set(key, value);
    });
    return url;
  }

  function setLoading(loading) {
    const root = getResultsRoot();
    if (root) {
      root.classList.toggle('shop-results-loading', loading);
    }
  }

  function revealInjectedResults() {
    const root = getResultsRoot();
    if (!root) {
      return;
    }

    root.querySelectorAll('.section-fade-up').forEach((el) => {
      el.classList.add('visible');
    });

    if (typeof window.observeFadeElements === 'function') {
      window.observeFadeElements(root);
    }
  }

  function applyResultsHtml(html) {
    const root = getResultsRoot();
    if (!root) {
      return;
    }
    root.outerHTML = html;
    revealInjectedResults();
  }

  function updateBrowserUrl(url, replace) {
    if (isPopState) return;
    const state = { catalogAjax: true };
    if (replace) {
      history.replaceState(state, '', url);
    } else {
      history.pushState(state, '', url);
    }
  }

  async function fetchCatalog(url, { replaceHistory = false, scrollToResults = true } = {}) {
    if (activeController) {
      activeController.abort();
    }
    activeController = new AbortController();

    setLoading(true);

    try {
      const res = await fetch(url, {
        signal: activeController.signal,
        credentials: 'same-origin',
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      });

      const data = await res.json().catch(() => ({}));

      if (!res.ok) {
        const msg = data.message || 'تعذر تحميل المنتجات';
        if (typeof showToast === 'function') {
          showToast(msg, 'error');
        }
        return;
      }

      if (data.redirect) {
        window.location.href = data.redirect;
        return;
      }

      if (!data.html?.results) {
        if (typeof showToast === 'function') {
          showToast('تعذر عرض نتائج المنتجات', 'error');
        }
        return;
      }

      applyResultsHtml(data.html.results);

      updateBrowserUrl(url, replaceHistory);

      if (scrollToResults) {
        const root = getResultsRoot();
        if (root) {
          root.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    } catch (err) {
      if (err.name === 'AbortError') return;
      if (typeof showToast === 'function') {
        showToast('تعذر الاتصال بالخادم', 'error');
      }
    } finally {
      setLoading(false);
      activeController = null;
    }
  }

  function fetchFromForm(options = {}) {
    const params = buildParamsFromForm();
    const url = buildFetchUrl(form.action, params).toString();
    return fetchCatalog(url, options);
  }

  function fetchFromUrl(url, options = {}) {
    return fetchCatalog(url, options);
  }

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    fetchFromForm();
  });

  form.querySelectorAll('input[name="category"], input[name="brand"]').forEach((input) => {
    input.addEventListener('change', () => fetchFromForm());
  });

  const priceRange = form.querySelector('#price-range');
  if (priceRange) {
    priceRange.addEventListener('input', () => {
      clearTimeout(priceTimer);
      priceTimer = setTimeout(() => fetchFromForm({ scrollToResults: false }), 400);
    });
    priceRange.addEventListener('change', () => fetchFromForm({ scrollToResults: false }));
  }

  const searchInput = form.querySelector('#search-input');
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(() => fetchFromForm({ scrollToResults: false }), 500);
    });
  }

  const sortSelect = document.getElementById('sort-select');
  if (sortSelect) {
    sortSelect.addEventListener('change', () => fetchFromForm());
  }

  const resetLink = document.getElementById('shop-filters-reset');
  if (resetLink) {
    resetLink.addEventListener('click', (e) => {
      e.preventDefault();
      form.reset();
      if (sortSelect) sortSelect.value = 'popular';
      const priceVal = document.getElementById('price-val');
      if (priceRange && priceVal) {
        priceRange.value = priceRange.max;
        priceVal.textContent = priceRange.max;
      }
      fetchFromUrl(form.action, { replaceHistory: false });
    });
  }

  document.addEventListener('click', (e) => {
    const link = e.target.closest('#shop-results-root .catalog-pagination__btn[href], #shop-results-root .pagination a');
    if (!link || !link.href) return;
    e.preventDefault();
    fetchFromUrl(link.href);
  });

  window.addEventListener('popstate', () => {
    isPopState = true;
    fetchFromUrl(window.location.href, { replaceHistory: true, scrollToResults: false }).finally(() => {
      isPopState = false;
    });
  });

  if (window.location.search) {
    history.replaceState({ catalogAjax: true }, '', window.location.href);
  }

  revealInjectedResults();
})();
