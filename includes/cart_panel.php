<?php
/**
 * NiRu-Furnitures — Right-side Add to Cart Panel
 * Included globally in footer.php
 */
?>
<!-- ═══ Cart Panel Overlay ═══ -->
<div id="cartPanelOverlay" class="cart-panel-overlay" aria-hidden="true"></div>

<!-- ═══ Cart Panel ═══ -->
<aside id="cartPanel" class="cart-panel" role="dialog" aria-modal="true" aria-label="Add to Cart">
    <!-- Header -->
    <div class="cart-panel-header">
        <div class="cart-panel-title">
            <i class="bi bi-bag-heart-fill"></i>
            <span>Add to Cart</span>
        </div>
        <button id="cartPanelClose" class="cart-panel-close" aria-label="Close panel">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Product Preview -->
    <div class="cart-panel-product">
        <div class="cart-panel-img-wrap">
            <img id="cpProductImg" src="" alt="" loading="lazy">
        </div>
        <div class="cart-panel-product-info">
            <span id="cpProductCategory" class="cp-cat-tag"></span>
            <h3 id="cpProductName" class="cp-product-name"></h3>
            <div id="cpProductPrice" class="cp-product-price"></div>
            <div id="cpProductStock" class="cp-stock-info"></div>
        </div>
    </div>

    <!-- Divider -->
    <hr class="cart-panel-divider">

    <!-- Qty Selector -->
    <div class="cart-panel-qty-row">
        <label class="cp-qty-label">Quantity</label>
        <div class="cp-qty-selector">
            <button type="button" class="cp-qty-btn" id="cpQtyMinus" aria-label="Decrease quantity">
                <i class="bi bi-dash"></i>
            </button>
            <input type="number" id="cpQtyInput" class="cp-qty-input" value="1" min="1" max="10" aria-label="Quantity">
            <button type="button" class="cp-qty-btn" id="cpQtyPlus" aria-label="Increase quantity">
                <i class="bi bi-plus"></i>
            </button>
        </div>
    </div>

    <!-- Trust Badges -->
    <div class="cp-trust-row">
        <span class="cp-trust-pill"><i class="bi bi-truck"></i> Free Delivery</span>
        <span class="cp-trust-pill"><i class="bi bi-arrow-counterclockwise"></i> 14-Day Returns</span>
        <span class="cp-trust-pill"><i class="bi bi-shield-check"></i> 2-Yr Warranty</span>
    </div>

    <!-- Footer Buttons -->
    <div class="cart-panel-footer">
        <button id="cpConfirmBtn" class="cp-btn-confirm" type="button">
            <i class="bi bi-bag-plus me-2"></i>Add to Cart
        </button>
        <a href="cart.php" class="cp-btn-view-cart">
            <i class="bi bi-cart3 me-2"></i>View Cart
        </a>
    </div>
</aside>

<style>
/* ── Cart Panel ─────────────────────────────────────────── */
.cart-panel-overlay {
    position: fixed;
    inset: 0;
    background: rgba(30, 18, 9, 0.45);
    backdrop-filter: blur(3px);
    z-index: 1080;
    opacity: 0;
    pointer-events: none;
    transition: opacity .3s ease;
}
.cart-panel-overlay.open {
    opacity: 1;
    pointer-events: all;
}

.cart-panel {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: 420px;
    max-width: 100vw;
    background: #fdf9f6;
    z-index: 1090;
    display: flex;
    flex-direction: column;
    box-shadow: -8px 0 40px rgba(61, 34, 25, .18);
    transform: translateX(100%);
    transition: transform .35s cubic-bezier(.4, 0, .2, 1);
    border-left: 1.5px solid #ede4db;
}
.cart-panel.open {
    transform: translateX(0);
}

/* Header */
.cart-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.35rem 1.5rem 1.1rem;
    border-bottom: 1.5px solid #ede4db;
    background: #fff;
    flex-shrink: 0;
}
.cart-panel-title {
    display: flex;
    align-items: center;
    gap: .6rem;
    font-family: 'Poppins', sans-serif;
    font-size: 1.1rem;
    font-weight: 800;
    color: #3d2219;
}
.cart-panel-title i {
    font-size: 1.25rem;
    color: #9b6b55;
}
.cart-panel-close {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 1.5px solid #e2d9d3;
    background: #fdf9f6;
    color: #5a3e38;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .18s;
    flex-shrink: 0;
}
.cart-panel-close:hover {
    background: #f5ede6;
    border-color: #c0826b;
    color: #3d2219;
    transform: rotate(90deg);
}

/* Product Preview */
.cart-panel-product {
    display: flex;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    background: #fff;
    border-bottom: 1.5px solid #ede4db;
    flex-shrink: 0;
}
.cart-panel-img-wrap {
    width: 100px;
    height: 100px;
    border-radius: 12px;
    overflow: hidden;
    background: #f4f1ef;
    border: 1.5px solid #ede4db;
    flex-shrink: 0;
}
.cart-panel-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .35s ease;
}
.cart-panel-img-wrap:hover img { transform: scale(1.05); }

.cart-panel-product-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: .35rem;
}
.cp-cat-tag {
    display: inline-block;
    font-size: .65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #9b6b55;
    background: #f4ede6;
    border-radius: 99px;
    padding: 2px 10px;
}
.cp-product-name {
    font-family: 'Poppins', sans-serif;
    font-size: .95rem;
    font-weight: 700;
    color: #3d2219;
    margin: 0;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cp-product-price {
    font-family: 'Poppins', sans-serif;
    font-size: 1.1rem;
    font-weight: 800;
    color: #53362e;
}
.cp-product-price .cp-price-orig {
    font-size: .8rem;
    font-weight: 400;
    color: #b0a09a;
    text-decoration: line-through;
    margin-left: .35rem;
}
.cp-price-save {
    display: inline-block;
    font-size: .65rem;
    font-weight: 800;
    background: #fee2e2;
    color: #c0392b;
    border-radius: 99px;
    padding: 2px 8px;
    margin-left: .3rem;
    vertical-align: middle;
}
.cp-stock-info {
    font-size: .75rem;
    color: #669960;
    font-weight: 600;
}

/* Divider */
.cart-panel-divider {
    margin: 0;
    border-color: #ede4db;
    flex-shrink: 0;
}

/* Qty Row */
.cart-panel-qty-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.1rem 1.5rem;
    flex-shrink: 0;
}
.cp-qty-label {
    font-size: .85rem;
    font-weight: 700;
    color: #4a3028;
}
.cp-qty-selector {
    display: flex;
    align-items: center;
    border: 1.5px solid #ddd5cc;
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
}
.cp-qty-btn {
    width: 38px;
    height: 38px;
    border: none;
    background: transparent;
    color: #5a3e38;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s;
}
.cp-qty-btn:hover { background: #f5ede6; }
.cp-qty-input {
    width: 50px;
    border: none;
    text-align: center;
    font-size: .9rem;
    font-weight: 700;
    color: #3d2219;
    background: transparent;
    outline: none;
    padding: 0;
    -moz-appearance: textfield;
}
.cp-qty-input::-webkit-outer-spin-button,
.cp-qty-input::-webkit-inner-spin-button { -webkit-appearance: none; }

/* Trust pills */
.cp-trust-row {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
    padding: 0 1.5rem 1rem;
    flex-shrink: 0;
}
.cp-trust-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    background: #f4f1ef;
    border: 1px solid #ddd5cc;
    border-radius: 99px;
    padding: 4px 12px;
    font-size: .72rem;
    font-weight: 600;
    color: #776965;
}
.cp-trust-pill i { color: #9b6b55; }

/* Footer Buttons */
.cart-panel-footer {
    padding: 1.1rem 1.5rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: .7rem;
    border-top: 1.5px solid #ede4db;
    margin-top: auto;
    background: #fff;
    flex-shrink: 0;
}
.cp-btn-confirm {
    width: 100%;
    background: #3d2219;
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: .85rem 1.5rem;
    font-size: .95rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .2s, transform .15s;
}
.cp-btn-confirm:hover { background: #5a3228; transform: translateY(-1px); }
.cp-btn-confirm:active { transform: translateY(0); }
.cp-btn-confirm:disabled {
    background: #b0a09a;
    cursor: not-allowed;
    transform: none;
}
.cp-btn-view-cart {
    width: 100%;
    background: transparent;
    color: #5a3e38;
    border: 1.5px solid #ddd5cc;
    border-radius: 12px;
    padding: .75rem 1.5rem;
    font-size: .88rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all .18s;
}
.cp-btn-view-cart:hover {
    border-color: #9b6b55;
    background: #fdf8f5;
    color: #3d2219;
}

/* Loading skeleton shimmer */
@keyframes cpShimmer {
    0% { background-position: -300px 0; }
    100% { background-position: 300px 0; }
}
.cp-skeleton {
    background: linear-gradient(90deg, #f0e8e2 25%, #e8ddd6 50%, #f0e8e2 75%);
    background-size: 600px 100%;
    animation: cpShimmer 1.4s infinite linear;
    border-radius: 6px;
}

/* Responsive */
@media (max-width: 480px) {
    .cart-panel { width: 100vw; }
}
</style>
