/**
 * NiRu-Furnitures — filter.js
 * Handles shop page AJAX filtering with the new pill/tab UI.
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const filterForm   = document.getElementById('filterForm');
    const productGrid  = document.getElementById('productGrid');
    const filterLoader = document.getElementById('filterLoader');
    const resultCount  = document.getElementById('resultCount');
    const sortInput    = document.getElementById('sortInput');
    const priceMax     = document.getElementById('priceMax');
    const priceMaxLbl  = document.getElementById('priceMaxLabel');

    if (!filterForm || !productGrid) return;

    // ── Price slider label sync ───────────────────────────────
    priceMax?.addEventListener('input', () => {
        const v = parseInt(priceMax.value);
        if (priceMaxLbl) priceMaxLbl.textContent = 'Rs. ' + v.toLocaleString() + (v >= 500000 ? '+' : '');
        updateSliderTrack(priceMax);
    });

    function updateSliderTrack(slider) {
        const pct = ((slider.value - slider.min) / (slider.max - slider.min)) * 100;
        slider.style.background = `linear-gradient(to right, #3d2219 0%, #3d2219 ${pct}%, #ddd6d0 ${pct}%)`;
    }
    if (priceMax) updateSliderTrack(priceMax);

    // ── Sort tabs ─────────────────────────────────────────────
    document.querySelectorAll('.sort-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.sort-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            if (sortInput) sortInput.value = tab.dataset.sort;
            applyFilters();
        });
    });

    // ── Category pills — single-select behaviour ──────────────
    const catAllLabel = document.getElementById('catAllLabel');
    const catAllCb    = document.getElementById('catAll');
    const catCbs      = filterForm.querySelectorAll('input[name="category[]"]');

    function updateAllLabel() {
        const anyChecked = [...catCbs].some(cb => cb.checked);
        if (catAllLabel) catAllLabel.classList.toggle('active', !anyChecked);
        if (catAllCb)    catAllCb.checked = !anyChecked;
    }

    catCbs.forEach(cb => {
        const label = cb.closest('.cat-pill-label');
        cb.addEventListener('change', () => {
            // Single-select: uncheck all others
            catCbs.forEach(other => {
                if (other !== cb) {
                    other.checked = false;
                    other.closest('.cat-pill-label')?.classList.remove('active');
                }
            });
            label?.classList.toggle('active', cb.checked);
            updateAllLabel();
            applyFilters();
        });
    });

    // "All" pill click — clear all categories
    catAllCb?.addEventListener('change', () => {
        catCbs.forEach(cb => { cb.checked = false; cb.closest('.cat-pill-label')?.classList.remove('active'); });
        if (catAllLabel) catAllLabel.classList.add('active');
        applyFilters();
    });
    catAllLabel?.addEventListener('click', (e) => {
        if (e.target === catAllLabel || e.target.tagName === 'SPAN') {
            catCbs.forEach(cb => { cb.checked = false; cb.closest('.cat-pill-label')?.classList.remove('active'); });
            if (catAllCb) catAllCb.checked = true;
            if (catAllLabel) catAllLabel.classList.add('active');
            applyFilters();
        }
    });

    // ── Price slider triggers filter on mouseup/touchend ─────
    priceMax?.addEventListener('change', () => applyFilters());

    // ── Form submit ───────────────────────────────────────────
    filterForm.addEventListener('submit', e => { e.preventDefault(); applyFilters(); });

    // ── Core fetch function ───────────────────────────────────
    async function applyFilters() {
        const params = new URLSearchParams(new FormData(filterForm));
        // Sort from hidden input
        if (sortInput) params.set('sort', sortInput.value);
        // Search query from URL
        const urlQ = new URLSearchParams(window.location.search).get('q');
        if (urlQ) params.set('q', urlQ);
        params.set('action', 'filter');
        // Reset to page 1
        params.set('page', '1');

        // Update browser URL
        window.history.pushState({}, '', `${window.location.pathname}?${params}`);
        showLoader();

        let data = null;
        try {
            const url = `${window.location.origin}/Furniture/api/products.php?${params}`;
            const res = await fetch(url);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const text = await res.text();
            try { data = JSON.parse(text); }
            catch(e) { console.error('[filter] bad JSON:', text.substring(0, 400)); throw new Error('Invalid server response'); }
        } catch (err) {
            console.error('[filter] fetch error:', err);
            productGrid.innerHTML = `
                <div class="no-results-box" style="grid-column:1/-1">
                    <i class="bi bi-wifi-off" style="font-size:2.5rem;color:#ddc9b8;display:block;margin-bottom:.8rem"></i>
                    <h5 style="font-family:Poppins,sans-serif;color:#3d2219">Could not load products</h5>
                    <p style="color:#9b8076;font-size:.85rem">${err.message}</p>
                    <button onclick="location.reload()" class="btn-view">Refresh</button>
                </div>`;
            hideLoader();
            return;
        }

        renderProducts(data.products || []);
        if (resultCount) resultCount.textContent = `${data.total ?? 0} product${data.total !== 1 ? 's' : ''} found`;
        renderPagination(data.total, data.per_page, data.page, params);
        hideLoader();
    }

    // ── Render helpers ────────────────────────────────────────
    function renderProducts(products) {
        if (!products.length) {
            productGrid.innerHTML = `
                <div class="no-results-box" style="grid-column:1/-1">
                    <i class="bi bi-inbox" style="font-size:3rem;color:#ddc9b8;display:block;margin-bottom:.8rem"></i>
                    <h5 style="font-family:Poppins,sans-serif;color:#3d2219">No products found</h5>
                    <p style="color:#9b8076">Try different filters or search terms.</p>
                    <a href="shop.php" class="btn-view" style="display:inline-flex;align-items:center;gap:.4rem">
                        <i class="bi bi-x-circle"></i> Clear Filters
                    </a>
                </div>`;
            return;
        }

        productGrid.innerHTML = products.map(p => {
            const price    = Number(p.sale_price || p.price);
            const origPrice = p.sale_price ? Number(p.price) : null;
            const rating   = p.avg_rating ? Number(p.avg_rating).toFixed(1) : '4.5';
            const desc     = esc(p.description ? p.description.replace(/<[^>]*>/g,'').substring(0, 70) : '');

                    const isWishlisted = window.USER_WISHLIST && window.USER_WISHLIST.includes(Number(p.id));
                    return `
            <div class="p-card" data-aos="fade-up">
                <div class="p-card-img-wrap">
                    <a href="product.php?id=${p.id}">
                        <img src="${p.image_url}" alt="${esc(p.name)}" class="product-card-img" loading="lazy">
                    </a>
                    ${p.featured ? '<span class="eco-badge">Eco-Choice</span>' : ''}
                    <button class="p-wishlist-btn ${isWishlisted ? 'active' : ''}" data-action="toggle-wishlist" data-product-id="${p.id}" aria-label="Wishlist">
                        <i class="bi ${isWishlisted ? 'bi-heart-fill' : 'bi-heart'}"></i>
                    </button>
                </div>
                <div class="p-card-body">
                    <div class="p-card-meta">
                        <h3 class="p-card-name">${esc(p.name)}</h3>
                        <span class="p-card-rating">★ ${rating}</span>
                    </div>
                    ${desc ? `<p class="p-card-desc">${desc}</p>` : ''}
                    <div class="p-card-footer">
                        <div>
                            <span class="p-card-price">
                                Rs. ${price.toLocaleString()}
                                ${origPrice ? `<span class="price-original">Rs. ${origPrice.toLocaleString()}</span>` : ''}
                            </span>
                        </div>
                        <a href="product.php?id=${p.id}" class="btn-view">View Details</a>
                    </div>
                </div>
            </div>`;
        }).join('');

        if (typeof AOS !== 'undefined') AOS.refresh();
    }

    function renderPagination(total, perPage, currentPage, params) {
        const wrap = document.getElementById('paginationWrap');
        if (!wrap) return;
        const pages = Math.ceil(total / perPage);
        if (pages <= 1) { wrap.innerHTML = ''; return; }

        let html = '<div class="shop-pagination">';
        // Prev
        if (currentPage > 1) {
            const p2 = new URLSearchParams(params); p2.set('page', currentPage - 1);
            html += `<button class="pg-btn arrow" data-page="${currentPage - 1}">&#8249;</button>`;
        } else {
            html += `<span class="pg-btn arrow" style="opacity:.35;cursor:default">&#8249;</span>`;
        }
        // Pages
        for (let i = 1; i <= pages; i++) {
            html += `<button class="pg-btn ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
        }
        // Next
        if (currentPage < pages) {
            html += `<button class="pg-btn arrow" data-page="${currentPage + 1}">&#8250;</button>`;
        } else {
            html += `<span class="pg-btn arrow" style="opacity:.35;cursor:default">&#8250;</span>`;
        }
        html += '</div>';
        wrap.innerHTML = html;

        wrap.querySelectorAll('[data-page]').forEach(btn => {
            btn.addEventListener('click', () => {
                const pageInput = document.getElementById('filterPageInput');
                if (pageInput) pageInput.value = btn.dataset.page;
                applyFilters();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    }

    function showLoader() {
        filterLoader?.classList.remove('d-none');
        productGrid.style.opacity = '.45';
        productGrid.style.pointerEvents = 'none';
    }
    function hideLoader() {
        filterLoader?.classList.add('d-none');
        productGrid.style.opacity = '1';
        productGrid.style.pointerEvents = '';
    }

    function esc(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
});
