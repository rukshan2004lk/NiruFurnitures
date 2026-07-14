<?php
/**
 * NiRu-Furnitures — Shopping Cart
 */
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    $cartItems = dbFetchAll(
        "SELECT c.id AS cart_id, c.qty, p.id AS product_id, p.name, p.price, p.sale_price, p.stock, p.images, p.slug
         FROM cart c JOIN products p ON p.id = c.product_id
         WHERE c.user_id = ? ORDER BY c.created_at DESC",
        'i',
        currentUserId()
    );
} else {
    $sid = session_id();
    $cartItems = dbFetchAll(
        "SELECT c.id AS cart_id, c.qty, p.id AS product_id, p.name, p.price, p.sale_price, p.stock, p.images, p.slug
         FROM cart c JOIN products p ON p.id = c.product_id
         WHERE c.session_id = ? ORDER BY c.created_at DESC",
        's',
        $sid
    );
}

$subtotal = 0;
foreach ($cartItems as $item) {
    $price = $item['sale_price'] ?: $item['price'];
    $subtotal += $price * $item['qty'];
}
$shippingCost = $subtotal >= (float) getSetting('free_shipping_above', 15000) ? 0 : (float) getSetting('shipping_cost', 500);
$freeShipAbove = (float) getSetting('free_shipping_above', 15000);
$total = $subtotal + $shippingCost;

$pageTitle = 'Shopping Cart — NiRu-Furnitures';
include 'includes/head.php';
include 'includes/navbar.php';
?>

<style>
    .cart-page {
        padding: 3rem 0 5rem;
        background: #f4f1ef;
    }

    .cart-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.6rem;
        font-weight: 700;
        color: #3d2219;
        margin-bottom: 1.75rem;
    }

    /* Table card */
    .cart-table-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #ded8d1;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(74, 44, 36, .05);
    }

    .cart-table-card .table th {
        background: #f9f5f2;
        font-size: .71rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #9b8076;
        padding: .85rem 1.25rem;
        border-bottom: 1px solid #ede8e3;
    }

    .cart-table-card .table td {
        padding: .95rem 1.25rem;
        border-color: #f0ebe6;
        vertical-align: middle;
    }

    .cart-table-card .table tbody tr:hover td {
        background: #fdfaf8;
    }

    .cart-product-img {
        width: 68px;
        height: 68px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid #ede8e3;
    }

    .cart-product-name {
        font-weight: 600;
        color: #3d2219;
        font-size: .9rem;
        text-decoration: none;
        display: block;
        margin-bottom: .2rem;
    }

    .cart-product-name:hover {
        color: #7d4f3f;
    }

    .cart-remove-btn {
        background: none;
        border: none;
        color: #c07060;
        font-size: .78rem;
        font-weight: 600;
        padding: 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: .3rem;
    }

    .cart-remove-btn:hover {
        color: #a0403a;
    }

    /* Summary card */
    .order-summary-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #ded8d1;
        box-shadow: 0 2px 8px rgba(74, 44, 36, .05);
        overflow: hidden;
    }

    .order-summary-header {
        background: #3d2219;
        color: #fff;
        padding: 1rem 1.5rem;
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: .95rem;
    }

    .order-summary-body {
        padding: 1.5rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: .875rem;
        margin-bottom: .75rem;
    }

    .summary-row .label {
        color: #776965;
    }

    .summary-row .value {
        font-weight: 600;
        color: #3d2219;
    }

    .summary-total .label {
        font-family: 'Poppins', sans-serif;
        font-size: 1.05rem;
        font-weight: 700;
        color: #3d2219;
    }

    .summary-total .value {
        font-family: 'Poppins', sans-serif;
        font-size: 1.3rem;
        font-weight: 800;
        color: #53362e;
    }

    .btn-checkout {
        background: #53362e;
        border: none;
        border-radius: 10px;
        color: #fff;
        font-weight: 700;
        font-size: .95rem;
        padding: .85rem;
        width: 100%;
        transition: background 200ms;
    }

    .btn-checkout:hover {
        background: #3d2219;
    }

    .free-ship-bar {
        background: #f4ede6;
        border-radius: 8px;
        padding: .75rem 1rem;
        font-size: .8rem;
        color: #7d4f3f;
        font-weight: 600;
        margin-top: .85rem;
        text-align: center;
    }

    /* Empty state */
    .cart-empty {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #ded8d1;
        padding: 6rem 2rem;
        text-align: center;
    }

    .cart-empty .empty-icon {
        font-size: 4rem;
        color: #ddc9b8;
        display: block;
        margin-bottom: 1.25rem;
    }

    .cart-empty h3 {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        color: #3d2219;
        margin-bottom: .6rem;
    }

    .cart-empty p {
        color: #776965;
        margin-bottom: 1.75rem;
    }

    .btn-start-shopping {
        background: #53362e;
        border: none;
        border-radius: 10px;
        color: #fff;
        font-weight: 700;
        padding: .8rem 2.5rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        transition: background 200ms;
    }

    .btn-start-shopping:hover {
        background: #3d2219;
        color: #fff;
    }
</style>

<!-- Hero -->
<section class="page-hero" aria-label="Shopping Cart">
    <div class="container-xl">
        <span class="hero-label">Your Selection</span>
        <h1>Shopping Cart</h1>
        <p>Review your items and proceed to checkout when ready.</p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                <li class="breadcrumb-item active">Shopping Cart</li>
            </ol>
        </nav>
    </div>
</div>

<section class="cart-page">
    <div class="container-xl">
        <?= renderFlash() ?>

        <h1 class="cart-title">
            <i class="bi bi-bag me-2"></i>Shopping Cart
            <span style="font-size:1rem;font-weight:400;color:#776965;margin-left:.5rem"><?= count($cartItems) ?>
                item<?= count($cartItems) !== 1 ? 's' : '' ?></span>
        </h1>

        <?php if ($cartItems): ?>
            <div class="row g-4 align-items-start">

                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="cart-table-card">
                        <table class="table mb-0" id="cartTable">
                            <thead>
                                <tr>
                                    <th style="width:50%">Product</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item):
                                    $images = json_decode($item['images'] ?? '[]', true);
                                    $imgUrl = productImageUrl($images[0] ?? null);
                                    $unitPrice = $item['sale_price'] ?: $item['price'];
                                    $lineTotal = $unitPrice * $item['qty'];
                                    ?>
                                    <tr id="cartRow_<?= $item['cart_id'] ?>">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <a href="product.php?id=<?= $item['product_id'] ?>">
                                                    <img src="<?= $imgUrl ?>" alt="<?= e($item['name']) ?>"
                                                        class="cart-product-img">
                                                </a>
                                                <div>
                                                    <a href="product.php?id=<?= $item['product_id'] ?>"
                                                        class="cart-product-name"><?= e($item['name']) ?></a>
                                                    <?php if ($item['sale_price']): ?>
                                                        <span
                                                            style="font-size:.72rem;font-weight:700;background:#fee2e2;color:#c0392b;padding:2px 7px;border-radius:4px">On
                                                            Sale</span>
                                                    <?php endif; ?>
                                                    <button class="cart-remove-btn mt-2"
                                                        onclick="removeCartItem(<?= $item['cart_id'] ?>)"
                                                        aria-label="Remove item">
                                                        <i class="bi bi-trash"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($item['sale_price']): ?>
                                                <del
                                                    class="text-muted small d-block"><?= formatPrice((float) $item['price']) ?></del>
                                            <?php endif; ?>
                                            <span
                                                style="font-weight:600;color:#3d2219"><?= formatPrice((float) $unitPrice) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <div class="qty-selector mx-auto" style="width:fit-content">
                                                <button class="qty-btn" onclick="updateQty(<?= $item['cart_id'] ?>, -1)"
                                                    aria-label="Decrease"><i class="bi bi-dash"></i></button>
                                                <input type="number" id="qty_<?= $item['cart_id'] ?>" class="qty-input"
                                                    value="<?= $item['qty'] ?>" min="1"
                                                    max="<?= min((int) $item['stock'], 10) ?>"
                                                    onchange="updateQtyDirect(<?= $item['cart_id'] ?>, this.value)"
                                                    aria-label="Quantity">
                                                <button class="qty-btn" onclick="updateQty(<?= $item['cart_id'] ?>, 1)"
                                                    aria-label="Increase"><i class="bi bi-plus"></i></button>
                                            </div>
                                        </td>
                                        <td class="text-end" id="lineTotal_<?= $item['cart_id'] ?>"
                                            style="font-weight:700;color:#3d2219">
                                            <?= formatPrice($lineTotal) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-3 flex-wrap gap-2">
                        <a href="shop.php"
                            style="font-size:.85rem;font-weight:600;color:#7d4f3f;text-decoration:none;display:flex;align-items:center;gap:.4rem">
                            <i class="bi bi-arrow-left"></i> Continue Shopping
                        </a>
                        <button onclick="clearCart()"
                            style="background:none;border:none;color:#c07060;font-size:.83rem;font-weight:600;cursor:pointer">
                            <i class="bi bi-trash me-1"></i>Clear Cart
                        </button>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="order-summary-card">
                        <div class="order-summary-header">
                            <i class="bi bi-receipt me-2"></i>Order Summary
                        </div>
                        <div class="order-summary-body">
                            <div class="summary-row">
                                <span class="label">Subtotal</span>
                                <span class="value" id="summarySubtotal"><?= formatPrice($subtotal) ?></span>
                            </div>
                            <div class="summary-row">
                                <span class="label">Shipping</span>
                                <span class="value" id="summaryShipping">
                                    <?= $shippingCost > 0 ? formatPrice($shippingCost) : '<span style="color:#2e7d32;font-weight:700">Free</span>' ?>
                                </span>
                            </div>
                            <hr style="border-color:#ede8e3;margin:1rem 0">
                            <div class="summary-row summary-total mb-4">
                                <span class="label">Total</span>
                                <span class="value" id="summaryTotal"><?= formatPrice($total) ?></span>
                            </div>

                            <?php if (isLoggedIn()): ?>
                                <button onclick="window.location='checkout.php'" class="btn-checkout">
                                    <i class="bi bi-lock me-2"></i>Proceed to Checkout
                                </button>
                            <?php else: ?>
                                <button onclick="window.location='login.php?next=cart.php'" class="btn-checkout">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login to Checkout
                                </button>
                            <?php endif; ?>

                            <?php if ($shippingCost > 0):
                                $remaining = $freeShipAbove - $subtotal; ?>
                                <div class="free-ship-bar">
                                    <i class="bi bi-truck me-1"></i>
                                    Add <?= formatPrice($remaining) ?> more for <strong>free delivery</strong>
                                </div>
                            <?php else: ?>
                                <div class="free-ship-bar" style="background:#e8f5e9;color:#2e7d32">
                                    <i class="bi bi-truck me-1"></i> You qualify for <strong>free delivery!</strong>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div
                        style="background:#fff;border-radius:14px;border:1px solid #ded8d1;padding:1rem 1.5rem;margin-top:1rem;text-align:center">
                        <p
                            style="font-size:.75rem;font-weight:600;color:#9b8076;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.75rem">
                            We Accept</p>
                        <div class="d-flex justify-content-center gap-3" style="font-size:1.5rem;color:#9b8076">
                            <i class="bi bi-credit-card-2-front" title="Card"></i>
                            <i class="bi bi-bank" title="Bank Transfer"></i>
                            <i class="bi bi-cash-coin" title="Cash on Delivery"></i>
                        </div>
                    </div>
                </div>

            </div>

        <?php else: ?>
            <div class="cart-empty">
                <i class="bi bi-bag-x empty-icon"></i>
                <h3>Your cart is empty</h3>
                <p>Looks like you haven't added anything yet.</p>
                <a href="shop.php" class="btn-start-shopping"><i class="bi bi-bag-heart"></i>Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    const CSRF = '<?= csrfToken() ?>';

    async function postCartApi(body) {
        const res = await fetch('api/products.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ...body, csrf_token: CSRF }),
        });
        return res.json();
    }

    async function updateQty(cartId, delta) {
        const input = document.getElementById('qty_' + cartId);
        const newQty = Math.max(1, parseInt(input.value) + delta);
        input.value = newQty;
        await updateQtyDirect(cartId, newQty);
    }

    async function updateQtyDirect(cartId, qty) {
        const data = await postCartApi({ action: 'update_cart', cart_id: cartId, qty: parseInt(qty) });
        if (data.success) {
            document.getElementById('lineTotal_' + cartId).textContent = data.line_total;
            document.getElementById('summarySubtotal').textContent = data.subtotal;
            document.getElementById('summaryShipping').innerHTML = data.shipping;
            document.getElementById('summaryTotal').textContent = data.total;
            window.updateCartCount?.(data.cart_count);
        } else {
            window.showToast?.(data.message || 'Error updating cart.', 'error');
        }
    }

    async function removeCartItem(cartId) {
        if (!confirm('Remove this item from cart?')) return;
        const data = await postCartApi({ action: 'remove_from_cart', cart_id: cartId });
        if (data.success) {
            const row = document.getElementById('cartRow_' + cartId);
            row.style.opacity = '0';
            row.style.transition = 'opacity 300ms';
            setTimeout(() => { row.remove(); window.showToast?.('Item removed.', 'info'); }, 300);
            document.getElementById('summarySubtotal').textContent = data.subtotal;
            document.getElementById('summaryShipping').innerHTML = data.shipping;
            document.getElementById('summaryTotal').textContent = data.total;
            window.updateCartCount?.(data.cart_count);
        }
    }

    async function clearCart() {
        if (!confirm('Clear all items from cart?')) return;
        const data = await postCartApi({ action: 'clear_cart' });
        if (data.success) location.reload();
    }
</script>

<?php include 'includes/footer.php'; ?>