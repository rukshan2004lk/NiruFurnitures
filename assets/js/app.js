/**
 * NiRu-Furnitures — app.js
 * Global initialiser: AOS, tooltips, back-to-top,
 * toast helper, AJAX add-to-cart/wishlist.
 */

'use strict';

// ── AOS (Animate On Scroll) ───────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 600,
            easing: 'ease-out-quad',
            once: true,
            offset: 60,
        });
    }

    // Bootstrap Tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });

    // Bootstrap Popovers
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
        new bootstrap.Popover(el);
    });

    initBackToTop();
    initAddToCart();
    initWishlist();
    initQtySelector();
});

// ── Back-to-Top Button ────────────────────────────────────────
function initBackToTop() {
    const btn = document.getElementById('backToTop');
    if (!btn) return;

    window.addEventListener('scroll', () => {
        btn.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });

    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

// ── Toast Notification Helper ─────────────────────────────────
/**
 * showToast(message, type)
 * type: 'success' | 'error' | 'warning' | 'info'
 */
function showToast(message, type = 'success') {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    const icons = {
        success: 'bi-check-circle-fill',
        error:   'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info:    'bi-info-circle-fill',
    };

    const colors = {
        success: 'text-success',
        error:   'text-danger',
        warning: 'text-warning',
        info:    'text-info',
    };

    const id = 'toast_' + Date.now();
    const html = `
    <div id="${id}" class="toast align-items-center border-0 shadow" role="alert" aria-live="assertive">
        <div class="d-flex align-items-center gap-2 p-3">
            <i class="bi ${icons[type] || icons.info} ${colors[type] || colors.info} fs-5"></i>
            <div class="flex-grow-1 fw-medium" style="font-size:.9rem">${message}</div>
            <button type="button" class="btn-close ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>`;

    container.insertAdjacentHTML('beforeend', html);
    const toastEl = document.getElementById(id);
    
    if (typeof bootstrap !== 'undefined') {
        const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    } else {
        toastEl.classList.add('show');
        setTimeout(() => {
            toastEl.classList.remove('show');
            setTimeout(() => toastEl.remove(), 300);
        }, 3500);
        toastEl.querySelector('.btn-close')?.addEventListener('click', () => {
            toastEl.classList.remove('show');
            setTimeout(() => toastEl.remove(), 300);
        });
    }
}

// ── Add to Cart (AJAX) ────────────────────────────────────────
// NOTE: The primary handler is in cart_panel.js (opens the slide-in panel).
// This function is kept as a fallback only when the cart panel is not present.
function initAddToCart() {
    // cart_panel.js attaches its own listener and calls stopPropagation,
    // so this listener only fires if the panel is absent.
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-action="add-to-cart"]');
        if (!btn) return;
        // If the cart panel JS is loaded, it will have already stopped propagation.
        // This guard prevents double-firing during the brief window before panel JS runs.
        if (document.getElementById('cartPanel')) return;

        e.preventDefault();
        const productId = btn.dataset.productId;
        const qty       = parseInt(document.getElementById('productQty')?.value ?? 1);

        btn.disabled = true;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Adding…';

        try {
            const res = await fetch(window.API_BASE || 'api/products.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'add_to_cart', product_id: productId, qty, csrf_token: window.APP_CSRF || '' }),
            });
            const data = await res.json();

            if (data.success) {
                showToast(data.message || 'Added to cart!', 'success');
                updateCartCount(data.cart_count ?? null);
            } else {
                showToast(data.message || 'Could not add to cart.', 'error');
            }
        } catch {
            showToast('Network error. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    });
}

// ── Wishlist Toggle (AJAX) ────────────────────────────────────
function initWishlist() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-action="toggle-wishlist"]');
        if (!btn) return;

        e.preventDefault();
        const productId = btn.dataset.productId;

        try {
            const res = await fetch(window.API_BASE || 'api/products.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'toggle_wishlist', product_id: productId, csrf_token: window.APP_CSRF || '' }),
            });
            const data = await res.json();

            if (data.success) {
                const icon = btn.querySelector('.bi');
                if (icon) {
                    icon.classList.toggle('bi-heart',      !data.wishlisted);
                    icon.classList.toggle('bi-heart-fill',  data.wishlisted);
                }
                btn.classList.toggle('active', data.wishlisted);
                
                if (window.USER_WISHLIST) {
                    const pid = Number(productId);
                    if (data.wishlisted && !window.USER_WISHLIST.includes(pid)) {
                        window.USER_WISHLIST.push(pid);
                    } else if (!data.wishlisted) {
                        window.USER_WISHLIST = window.USER_WISHLIST.filter(id => id !== pid);
                    }
                }
                
                showToast(data.message, data.wishlisted ? 'success' : 'info');
            } else {
                showToast(data.message || 'Please log in first.', 'warning');
            }
        } catch {
            showToast('Network error.', 'error');
        }
    });
}

// ── Cart Count Badge Updater ──────────────────────────────────
function updateCartCount(count) {
    if (count === null) return;
    document.querySelectorAll('.cart-badge').forEach(badge => {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    });
}

// ── Quantity Selector ─────────────────────────────────────────
function initQtySelector() {
    document.querySelectorAll('.qty-selector').forEach(wrap => {
        const input = wrap.querySelector('.qty-input');
        const minus = wrap.querySelector('[data-qty="minus"]');
        const plus  = wrap.querySelector('[data-qty="plus"]');

        if (!input) return;

        minus?.addEventListener('click', () => {
            const min = parseInt(input.min || 1);
            if (parseInt(input.value) > min) input.value = parseInt(input.value) - 1;
            input.dispatchEvent(new Event('change'));
        });

        plus?.addEventListener('click', () => {
            const max = parseInt(input.max || 999);
            if (parseInt(input.value) < max) input.value = parseInt(input.value) + 1;
            input.dispatchEvent(new Event('change'));
        });

        input.addEventListener('change', () => {
            const min = parseInt(input.min || 1);
            const max = parseInt(input.max || 999);
            input.value = Math.max(min, Math.min(max, parseInt(input.value) || min));
        });
    });
}

// ── Expose Globals ────────────────────────────────────────────
window.showToast = showToast;
window.updateCartCount = updateCartCount;
