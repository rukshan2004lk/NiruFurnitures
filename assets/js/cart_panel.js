/**
 * NiRu-Furnitures — cart_panel.js
 * Handles the right-side "Add to Cart" slide-in panel.
 *
 * Flow:
 *  1. User clicks any [data-action="add-to-cart"] button
 *  2. Fetch product info via GET api/products.php?action=get_product&id=X
 *  3. Populate panel with name, image, price, stock
 *  4. Open panel with smooth slide-in animation
 *  5. When user clicks "Add to Cart" in panel → POST add_to_cart
 */

'use strict';

(function () {

    const panel   = document.getElementById('cartPanel');
    const overlay = document.getElementById('cartPanelOverlay');
    const closeBtn = document.getElementById('cartPanelClose');

    if (!panel || !overlay) return; // guard: panel HTML not present

    // Panel elements
    const cpImg      = document.getElementById('cpProductImg');
    const cpCat      = document.getElementById('cpProductCategory');
    const cpName     = document.getElementById('cpProductName');
    const cpPrice    = document.getElementById('cpProductPrice');
    const cpStock    = document.getElementById('cpProductStock');
    const cpQtyInput = document.getElementById('cpQtyInput');
    const cpQtyMinus = document.getElementById('cpQtyMinus');
    const cpQtyPlus  = document.getElementById('cpQtyPlus');
    const cpConfirm  = document.getElementById('cpConfirmBtn');

    let currentProductId = null;

    // ── Open / Close ──────────────────────────────────────────
    function openPanel() {
        panel.classList.add('open');
        overlay.classList.add('open');
        overlay.setAttribute('aria-hidden', 'false');
        panel.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        // Focus close btn for accessibility
        setTimeout(() => closeBtn?.focus(), 350);
    }

    function closePanel() {
        panel.classList.remove('open');
        overlay.classList.remove('open');
        overlay.setAttribute('aria-hidden', 'true');
        panel.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    closeBtn?.addEventListener('click', closePanel);
    overlay.addEventListener('click', closePanel);

    // Escape key closes panel
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && panel.classList.contains('open')) closePanel();
    });

    // ── Skeleton loading state ────────────────────────────────
    function setLoading(isLoading) {
        if (isLoading) {
            cpImg.src = '';
            cpImg.style.display = 'none';
            cpImg.parentElement.classList.add('cp-skeleton');
            cpCat.textContent = '';
            cpName.textContent = 'Loading…';
            cpName.classList.add('cp-skeleton');
            cpPrice.textContent = '';
            cpStock.textContent = '';
            cpConfirm.disabled = true;
        } else {
            cpImg.style.display = '';
            cpImg.parentElement.classList.remove('cp-skeleton');
            cpName.classList.remove('cp-skeleton');
            cpConfirm.disabled = false;
        }
    }

    // ── Populate panel with product data ──────────────────────
    function populatePanel(p) {
        cpImg.src = p.image_url || '';
        cpImg.alt = p.name || '';
        cpCat.textContent = p.category_name || '';

        cpName.textContent = p.name || '';

        // Price
        const price    = parseFloat(p.price) || 0;
        const salePrc  = parseFloat(p.sale_price) || 0;
        const hasSale  = salePrc > 0 && salePrc < price;
        const display  = hasSale ? salePrc : price;

        if (hasSale) {
            const savePct = Math.round((1 - salePrc / price) * 100);
            cpPrice.innerHTML =
                `Rs. ${numFmt(display)}<span class="cp-price-orig">Rs. ${numFmt(price)}</span>` +
                `<span class="cp-price-save">Save ${savePct}%</span>`;
        } else {
            cpPrice.innerHTML = `Rs. ${numFmt(display)}`;
        }

        // Stock
        const stock = parseInt(p.stock) || 0;
        if (stock > 0) {
            cpStock.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i>${stock} in stock`;
            cpStock.style.color = '#669960';
            cpConfirm.disabled = false;
        } else {
            cpStock.innerHTML = `<i class="bi bi-x-circle-fill me-1"></i>Out of stock`;
            cpStock.style.color = '#c0392b';
            cpConfirm.disabled = true;
        }

        // Reset qty
        cpQtyInput.value = 1;
        cpQtyInput.max = Math.min(stock, 10);
    }

    function numFmt(n) {
        return Number(n).toLocaleString('en-IN');
    }

    // ── Fetch product info ────────────────────────────────────
    async function loadProduct(productId) {
        setLoading(true);

        try {
            const base = window.API_BASE || 'api/products.php';
            const res  = await fetch(`${base}?action=get_product&id=${productId}`);
            const data = await res.json();

            if (data.success && data.product) {
                populatePanel(data.product);
            } else {
                showToastSafe(data.message || 'Product not found.', 'error');
                closePanel();
            }
        } catch {
            showToastSafe('Network error. Please try again.', 'error');
            closePanel();
        } finally {
            setLoading(false);
        }
    }

    // ── Qty controls ──────────────────────────────────────────
    cpQtyMinus?.addEventListener('click', () => {
        const min = parseInt(cpQtyInput.min || 1);
        if (parseInt(cpQtyInput.value) > min) cpQtyInput.value = parseInt(cpQtyInput.value) - 1;
    });

    cpQtyPlus?.addEventListener('click', () => {
        const max = parseInt(cpQtyInput.max || 10);
        if (parseInt(cpQtyInput.value) < max) cpQtyInput.value = parseInt(cpQtyInput.value) + 1;
    });

    cpQtyInput?.addEventListener('change', () => {
        const min = parseInt(cpQtyInput.min || 1);
        const max = parseInt(cpQtyInput.max || 10);
        cpQtyInput.value = Math.max(min, Math.min(max, parseInt(cpQtyInput.value) || min));
    });

    // ── Confirm (Add to Cart) button ──────────────────────────
    cpConfirm?.addEventListener('click', async () => {
        if (!currentProductId) return;

        const qty = parseInt(cpQtyInput.value || 1);
        cpConfirm.disabled = true;
        const originalHtml = cpConfirm.innerHTML;
        cpConfirm.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding…';

        try {
            const base = window.API_BASE || 'api/products.php';
            const res  = await fetch(base, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action:      'add_to_cart',
                    product_id:  currentProductId,
                    qty:         qty,
                    csrf_token:  window.APP_CSRF || '',
                }),
            });
            const data = await res.json();

            if (data.success) {
                showToastSafe(data.message || 'Added to cart!', 'success');
                if (typeof window.updateCartCount === 'function') {
                    window.updateCartCount(data.cart_count ?? null);
                }
                closePanel();

                // Animate the cart icon in navbar
                document.querySelectorAll('.navbar-icon-btn .bi-bag, .navbar-icon-btn .bi-cart3').forEach(icon => {
                    icon.closest('.navbar-icon-btn')?.classList.add('cart-bump');
                    setTimeout(() => icon.closest('.navbar-icon-btn')?.classList.remove('cart-bump'), 600);
                });
            } else {
                showToastSafe(data.message || 'Could not add to cart.', 'error');
            }
        } catch {
            showToastSafe('Network error. Please try again.', 'error');
        } finally {
            cpConfirm.disabled = false;
            cpConfirm.innerHTML = originalHtml;
        }
    });

    // ── Intercept all [data-action="add-to-cart"] clicks ─────
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action="add-to-cart"]');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const productId = btn.dataset.productId;
        if (!productId) return;

        currentProductId = productId;

        // Copy qty from on-page qty input if present (product detail page)
        const pageQty = document.getElementById('productQty');
        if (pageQty) {
            cpQtyInput.value = parseInt(pageQty.value || 1);
        } else {
            cpQtyInput.value = 1;
        }

        openPanel();
        loadProduct(productId);
    });

    // ── Helper: safe showToast ────────────────────────────────
    function showToastSafe(msg, type) {
        if (typeof window.showToast === 'function') {
            window.showToast(msg, type);
        }
    }

    // ── Cart bump animation style injection ───────────────────
    const style = document.createElement('style');
    style.textContent = `
        @keyframes cartBump {
            0%   { transform: scale(1); }
            30%  { transform: scale(1.35) rotate(-8deg); }
            60%  { transform: scale(1.2) rotate(5deg); }
            100% { transform: scale(1) rotate(0deg); }
        }
        .cart-bump i { animation: cartBump .5s ease; }
    `;
    document.head.appendChild(style);

})();
