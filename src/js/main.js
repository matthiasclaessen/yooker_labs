document.addEventListener('DOMContentLoaded', function () {
  const filter = document.querySelector('#filter');

  if (filter) {
    filter.addEventListener('change', function () {
      const product_kind = document.querySelector('#filter #kind').value;
      const url = '?' + product_kind;

      const data = {
        category: product_kind,
        action: 'brightbyte_default_ajax_filter',
      };

      const xhr = new XMLHttpRequest();
      xhr.open('POST', ajaxurl);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          document.querySelector('.c-products #products').innerHTML = xhr.responseText;
        }
      };
      xhr.send(JSON.stringify(data));
    });
  }
});

/**
 * Shop filter — AJAX product filtering
 * Add this to src/js/main.js or import as a separate module.
 *
 * Depends on:
 *   filter_vars.ajax_url  — localized in archive-product.php
 *   filter_vars.nonce     — localized in archive-product.php
 *   filter_vars.price_min — global min price
 *   filter_vars.price_max — global max price
 *   filter_vars.currency  — currency symbol
 */

document.addEventListener('DOMContentLoaded', () => {
  const grid = document.getElementById('js-product-grid');
  const loading = document.getElementById('js-grid-loading');
  const countEl = document.getElementById('js-result-count');
  const pagination = document.getElementById('js-pagination');
  const activeEl = document.getElementById('js-active-filters');
  const resetBtn = document.getElementById('js-filter-reset');
  const orderby = document.getElementById('js-filter-orderby');
  const priceMin = document.getElementById('js-price-min');
  const priceMax = document.getElementById('js-price-max');
  const priceFill = document.getElementById('js-price-fill');
  const minLabel = document.getElementById('js-price-min-label');
  const maxLabel = document.getElementById('js-price-max-label');
  const filterToggle = document.getElementById('js-filter-toggle');
  const filterBody = document.getElementById('js-filter-body');

  if (!grid || typeof filter_vars === 'undefined') {
    return;
  }

  // State
  let currentPage = 1;
  let debounceTimer = null;
  const DEBOUNCE_MS = 400;

  // Mobile toggle
  if (filterToggle && filterBody) {
    filterToggle.addEventListener('click', () => {
      const isOpen = filterBody.classList.toggle('is-open');
      filterToggle.setAttribute('aria-expanded', isOpen.toString());
    });
  }

  // Price slider
  function updatePriceFill() {
    if (!priceMin || !priceMax || !priceFill) {
      return;
    }

    const min = parseInt(filter_vars.price_min);
    const max = parseInt(filter_vars.price_max);
    const valMin = parseInt(priceMin.value);
    const valMax = parseInt(priceMax.value);
    const range = max - min;

    const leftPct = ((valMin - min) / range) * 100;
    const rightPct = ((max - valMax) / range) * 100;

    priceFill.style.left = leftPct + '%';
    priceFill.style.right = rightPct + '%';

    if (minLabel) {
      minLabel.textContent = valMin;
    }
    if (maxLabel) {
      maxLabel.textContent = valMax;
    }
  }

  if (priceMin && priceMax) {
    // Prevent thumbs crossing
    priceMin.addEventListener('input', () => {
      if (parseInt(priceMin.value) >= parseInt(priceMax.value)) {
        priceMin.value = parseInt(priceMax.value) - 10;
      }
      updatePriceFill();
      debouncedFetch();
    });

    priceMax.addEventListener('input', () => {
      if (parseInt(priceMax.value) <= parseInt(priceMin.value)) {
        priceMax.value = parseInt(priceMin.value) + 10;
      }
      updatePriceFill();
      debouncedFetch();
    });

    updatePriceFill();
  }

  // Checkbox listeners
  document.querySelectorAll('.c-filter__checkbox').forEach(cb => {
    cb.addEventListener('change', () => {
      currentPage = 1;
      debouncedFetch();
    });
  });

  // Sort listener
  if (orderby) {
    orderby.addEventListener('change', () => {
      currentPage = 1;
      fetchProducts();
    });
  }

  // Reset
  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      // Uncheck all checkboxes
      document.querySelectorAll('.c-filter__checkbox').forEach(cb => {
        cb.checked = false;
      });

      // Reset price sliders
      if (priceMin && priceMax) {
        priceMin.value = filter_vars.price_min;
        priceMax.value = filter_vars.price_max;
        updatePriceFill();
      }

      // Reset sort
      if (orderby) {
        orderby.value = 'date';
      }

      currentPage = 1;
      fetchProducts();
    });
  }

  // Debounce helper
  function debouncedFetch() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchProducts, DEBOUNCE_MS);
  }

  // Collect current filter state
  function getFilters() {
    const categories = [...document.querySelectorAll('.js-filter-cat:checked')]
      .map(el => el.value);

    const componentTypes = [...document.querySelectorAll('.js-filter-type:checked')]
      .map(el => el.value);

    const stockEl = document.getElementById('js-filter-stock');
    const inStock = stockEl ? stockEl.checked : false;

    const globalMin = parseInt(filter_vars.price_min);
    const globalMax = parseInt(filter_vars.price_max);
    const pMin = priceMin ? parseInt(priceMin.value) : globalMin;
    const pMax = priceMax ? parseInt(priceMax.value) : globalMax;

    return {
      categories,
      component_types: componentTypes,
      price_min: pMin > globalMin ? pMin : 0,
      price_max: pMax < globalMax ? pMax : 0,
      in_stock: inStock ? '1' : '',
      orderby: orderby ? orderby.value : 'date',
    };
  }

  // Main fetch
  function fetchProducts(page = currentPage) {
    const filters = getFilters();

    // Show loading state
    if (loading) {
      loading.classList.add('is-loading');
    }
    if (grid) {
      grid.classList.add('is-loading');
    }

    // Build form data
    const body = new URLSearchParams({
      action: 'brightbyte_filter_products',
      nonce: filter_vars.nonce,
      orderby: filters.orderby,
      paged: page,
      in_stock: filters.in_stock,
      price_min: filters.price_min,
      price_max: filters.price_max,
    });

    filters.categories.forEach(id => body.append('categories[]', id));
    filters.component_types.forEach(t => body.append('component_types[]', t));

    fetch(filter_vars.ajax_url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString(),
    })
      .then(res => res.json())
      .then(res => {
        if (!res.success) {
          return;
        }

        const { html, total, total_pages, paged } = res.data;
        currentPage = paged;

        // Animate grid out, swap, animate in
        if (grid) {
          grid.style.opacity = '0';
          grid.style.transition = 'opacity .15s ease';
          setTimeout(() => {
            grid.innerHTML = html;
            grid.style.opacity = '1';
            grid.classList.remove('is-loading');

            // Re-bind WooCommerce AJAX add-to-cart on new items
            if (typeof wc_add_to_cart_params !== 'undefined') {
              jQuery(document.body).trigger('wc_fragment_refresh');
            }
          }, 150);
        }

        // Update count
        if (countEl) {
          countEl.textContent = total === 1
            ? `1 product`
            : `${total} products`;
        }

        // Update pagination
        renderPagination(total_pages, paged);

        // Update active filter tags
        renderActiveTags(filters);

        // Scroll to top of grid on page change
        if (page !== currentPage) {
          grid?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      })
      .catch(err => {
        console.error('Filter error:', err);
      })
      .finally(() => {
        if (loading) {
          loading.classList.remove('is-loading');
        }
      });
  }

  // ── Pagination renderer ────────────────────────────────────────────────────
  function renderPagination(totalPages, paged) {
    if (!pagination) {
      return;
    }
    pagination.innerHTML = '';
    if (totalPages <= 1) {
      return;
    }

    // Prev button
    const prev = document.createElement('button');
    prev.textContent = '←';
    prev.disabled = paged === 1;
    prev.setAttribute('aria-label', 'Previous page');
    prev.addEventListener('click', () => fetchProducts(paged - 1));
    pagination.appendChild(prev);

    // Page number buttons
    for (let i = 1; i <= totalPages; i++) {
      // Show first, last, current ±1, and ellipsis
      const isEdge = i === 1 || i === totalPages;
      const isNear = Math.abs(i - paged) <= 1;
      if (!isEdge && !isNear) {
        // Ellipsis — only add once per gap
        const last = pagination.lastElementChild;
        if (last && last.tagName !== 'SPAN') {
          const ellipsis = document.createElement('span');
          ellipsis.textContent = '…';
          ellipsis.style.cssText = 'padding: 0 .25rem; color: var(--woo-muted, #6a7282); align-self: center;';
          pagination.appendChild(ellipsis);
        }
        continue;
      }

      const btn = document.createElement('button');
      btn.textContent = i;
      btn.setAttribute('aria-label', `Page ${i}`);
      if (i === paged) {
        btn.classList.add('is-active');
        btn.setAttribute('aria-current', 'page');
      }
      btn.addEventListener('click', () => fetchProducts(i));
      pagination.appendChild(btn);
    }

    // Next button
    const next = document.createElement('button');
    next.textContent = '→';
    next.disabled = paged === totalPages;
    next.setAttribute('aria-label', 'Next page');
    next.addEventListener('click', () => fetchProducts(paged + 1));
    pagination.appendChild(next);
  }

  // ── Active filter tags renderer ────────────────────────────────────────────
  function renderActiveTags(filters) {
    if (!activeEl) {
      return;
    }
    activeEl.innerHTML = '';

    const currency = filter_vars.currency || '€';

    // Category tags
    document.querySelectorAll('.js-filter-cat:checked').forEach(cb => {
      const label = cb.closest('.c-filter__label')?.querySelector('.c-filter__checkmark')
          ?.nextSibling?.textContent?.trim()
        || cb.closest('li')?.textContent?.trim();
      addTag(label || `Category ${cb.value}`, () => {
        cb.checked = false;
        currentPage = 1;
        fetchProducts();
      });
    });

    // Component type tags
    document.querySelectorAll('.js-filter-type:checked').forEach(cb => {
      const labelEl = cb.closest('.c-filter__label');
      const text = labelEl ? [...labelEl.childNodes]
        .filter(n => n.nodeType === 3)
        .map(n => n.textContent.trim())
        .find(t => t.length > 0) : null;
      addTag(text || cb.value, () => {
        cb.checked = false;
        currentPage = 1;
        fetchProducts();
      });
    });

    // Price tag
    const globalMin = parseInt(filter_vars.price_min);
    const globalMax = parseInt(filter_vars.price_max);
    if (filters.price_min > 0 || (filters.price_max > 0 && filters.price_max < globalMax)) {
      const pMin = priceMin ? parseInt(priceMin.value) : globalMin;
      const pMax = priceMax ? parseInt(priceMax.value) : globalMax;
      addTag(`${currency}${pMin} – ${currency}${pMax}`, () => {
        if (priceMin) {
          priceMin.value = filter_vars.price_min;
        }
        if (priceMax) {
          priceMax.value = filter_vars.price_max;
        }
        updatePriceFill();
        currentPage = 1;
        fetchProducts();
      });
    }

    // In stock tag
    const stockEl = document.getElementById('js-filter-stock');
    if (stockEl && stockEl.checked) {
      addTag('In stock only', () => {
        stockEl.checked = false;
        currentPage = 1;
        fetchProducts();
      });
    }
  }

  function addTag(label, onRemove) {
    const btn = document.createElement('button');
    btn.className = 'c-filter-tag';
    btn.innerHTML = `
            ${escapeHtml(label)}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6"  y1="6" x2="18" y2="18"/>
            </svg>
        `;
    btn.setAttribute('aria-label', `Remove filter: ${label}`);
    btn.addEventListener('click', onRemove);
    activeEl.appendChild(btn);
  }

  function escapeHtml(str) {
    return str
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  // ── Initial load ───────────────────────────────────────────────────────────
  // Trigger count display on page load
  fetchProducts(1);
});