<?php
/**
 * NiRu-Furnitures — Checkout (checkout.php)
 */
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Redirect to login if not logged in (guest checkout not supported in this MVP)
if (!isLoggedIn()) {
    setFlash('Please log in or create an account to proceed to checkout.', 'info');
    redirect('login.php?redirect=checkout.php');
}

$userId = currentUserId();

// ── Get Cart Items ────────────────────────────────────────────
$cartItems = dbFetchAll("SELECT c.id as cart_id, c.qty, p.id as product_id, p.name, p.price, p.sale_price, p.images, p.stock 
                         FROM cart c 
                         JOIN products p ON p.id = c.product_id 
                         WHERE c.user_id = ?", 'i', $userId);

if (empty($cartItems)) {
    setFlash('Your cart is empty.', 'warning');
    redirect('shop.php');
}

$subtotal = 0;
$validItems = [];
$errors = [];

// Validate stock before checkout
foreach ($cartItems as $item) {
    if ($item['stock'] < $item['qty']) {
        $errors[] = "Only {$item['stock']} items available for {$item['name']}. Please update your cart.";
    } else {
        $price = $item['sale_price'] ?: $item['price'];
        $subtotal += $price * $item['qty'];
        $validItems[] = [
            'product_id' => $item['product_id'],
            'name' => $item['name'],
            'qty' => $item['qty'],
            'price' => $price
        ];
    }
}

if (!empty($errors)) {
    foreach ($errors as $e)
        setFlash($e, 'danger');
    redirect('cart.php');
}

$shippingThreshold = getSetting('free_shipping_above', 15000);
$shippingCost = $subtotal >= $shippingThreshold ? 0 : (float) getSetting('shipping_cost', 500);
$total = $subtotal + $shippingCost;

// ── Handle Order Submission ───────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_name = sanitize($_POST['name'] ?? '');
    $shipping_address = sanitize($_POST['address'] ?? '');
    $shipping_city = sanitize($_POST['city'] ?? '');
    $shipping_zip = sanitize($_POST['zip'] ?? '');
    $payment_method = sanitize($_POST['payment_method'] ?? 'cash_on_delivery');

    if (empty($shipping_name) || empty($shipping_address) || empty($shipping_city)) {
        setFlash('Please fill in all required shipping details.', 'danger');
    } else {
        // Begin Transaction
        global $conn;
        $conn->begin_transaction();

        try {
            // 1. Create Order
            $orderNumber = generateOrderNumber();
            $stmt = $conn->prepare("INSERT INTO orders (user_id, order_number, subtotal, shipping_cost, total, status, payment_method, shipping_name, shipping_address, shipping_city, shipping_zip) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?)");
            $stmt->bind_param('isdddsssss', $userId, $orderNumber, $subtotal, $shippingCost, $total, $payment_method, $shipping_name, $shipping_address, $shipping_city, $shipping_zip);
            $stmt->execute();
            $orderId = $stmt->insert_id;

            // 2. Insert Order Items & Deduct Stock
            $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, qty, unit_price, total_price) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

            foreach ($validItems as $v) {
                $totalPrice = $v['price'] * $v['qty'];
                $stmtItem->bind_param('iisidd', $orderId, $v['product_id'], $v['name'], $v['qty'], $v['price'], $totalPrice);
                $stmtItem->execute();

                $stmtStock->bind_param('ii', $v['qty'], $v['product_id']);
                $stmtStock->execute();
            }

            // 3. Clear Cart
            $stmtClear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmtClear->bind_param('i', $userId);
            $stmtClear->execute();

            $conn->commit();

            redirect("order-success.php?id=$orderId");

        } catch (Exception $e) {
            $conn->rollback();
            setFlash('An error occurred while processing your order. Please try again.', 'danger');
        }
    }
}

$user = dbFetchOne("SELECT * FROM users WHERE id = ?", 'i', $userId);

$pageTitle = 'Checkout — NiRu-Furnitures';
$extraJs = [SITE_URL . '/assets/js/validation.js'];

include 'includes/head.php';
include 'includes/navbar.php';
?>

<!-- Hero -->
<section class="page-hero" aria-label="Checkout">
    <div class="container-xl">
        <span class="hero-label">Final Step</span>
        <h1>Checkout</h1>
        <p>Complete your order details below and we'll take care of the rest.</p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="cart.php">Shopping Cart</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>
    </div>
</div>

<section class="checkout-section">
    <div class="container-xl">
        <?= renderFlash() ?>

        <form method="POST" action="checkout.php" class="row g-4 needs-validation" novalidate>
            <!-- Left Col: Shipping & Payment -->
            <div class="col-lg-8">
                <div class="checkout-card">
                    <h5>Shipping Details</h5>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="<?= e($user['name'] ?? '') ?>"
                                required>
                            <div class="invalid-feedback">Name is required.</div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-medium">Street Address <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="address"
                                value="<?= e($user['address'] ?? '') ?>" placeholder="House number and street name"
                                required>
                            <div class="invalid-feedback">Address is required.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="city" value="<?= e($user['city'] ?? '') ?>"
                                required>
                            <div class="invalid-feedback">City is required.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Postal / Zip Code</label>
                            <input type="text" class="form-control" name="zip"
                                value="<?= e($user['zip_code'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email Address</label>
                            <input type="email" class="form-control" value="<?= e($user['email'] ?? '') ?>" disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Phone Number</label>
                            <input type="tel" class="form-control" value="<?= e($user['phone'] ?? '') ?>" disabled>
                            <div class="form-text small">Update in your <a href="dashboard.php"
                                    target="_blank">Dashboard</a>.</div>
                        </div>
                    </div>
                </div><!-- /checkout-card -->

                <div class="checkout-card">
                    <h5>Payment Method</h5>

                    <label class="checkout-payment-option">
                        <input class="form-check-input" type="radio" name="payment_method" value="cash_on_delivery" checked>
                        <span>
                            <span class="d-block fw-bold" style="color:#3d2219">Cash on Delivery (COD)</span>
                            <small style="color:#776965" class="d-block mt-1">Pay with cash upon delivery.</small>
                        </span>
                    </label>
                    <label class="checkout-payment-option" style="opacity:.5">
                        <input class="form-check-input" type="radio" name="payment_method" value="credit_card" disabled>
                        <span>
                            <span class="d-block fw-bold" style="color:#3d2219">Credit / Debit Card (Coming Soon)</span>
                            <small style="color:#776965" class="d-block mt-1">Secure online payment via Stripe / PayPal.</small>
                        </span>
                    </label>
                </div><!-- /checkout-card -->
            </div>

            <!-- Right Col: Order Summary -->
            <div class="col-lg-4">
                <div class="checkout-summary-card">
                    <h5>Your Order</h5>

                    <div class="mb-3">
                        <?php foreach ($cartItems as $item):
                            $imgs = json_decode($item['images'] ?? '[]', true);
                            $price = $item['sale_price'] ?: $item['price'];
                            ?>
                            <div class="checkout-summary-item">
                                <img src="<?= productImageUrl($imgs[0] ?? null) ?>" class="checkout-summary-img" alt="<?= e($item['name']) ?>">
                                <div class="flex-grow-1">
                                    <div style="font-weight:600;color:#3d2219;font-size:.875rem" class="text-truncate"><?= e($item['name']) ?></div>
                                    <div style="color:#9b8076;font-size:.78rem">Qty: <?= $item['qty'] ?></div>
                                </div>
                                <div style="font-weight:700;color:#3d2219;font-size:.875rem;white-space:nowrap"><?= formatPrice($price * $item['qty']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="checkout-summary-row">
                        <span class="label">Subtotal</span>
                        <span><?= formatPrice($subtotal) ?></span>
                    </div>
                    <div class="checkout-summary-row">
                        <span class="label">Shipping</span>
                        <?php if ($shippingCost > 0): ?>
                            <span><?= formatPrice($shippingCost) ?></span>
                        <?php else: ?>
                            <span style="color:#2e7d32;font-weight:700">Free</span>
                        <?php endif; ?>
                    </div>

                    <div class="checkout-summary-total">
                        <span>Total</span>
                        <span><?= formatPrice($total) ?></span>
                    </div>

                    <button type="submit" class="btn-place-order">
                        <i class="bi bi-bag-check me-1"></i> Place Order
                    </button>

                    <p class="text-center mt-3" style="font-size:.78rem;color:#9b8076">
                        <i class="bi bi-shield-check me-1" style="color:#2e7d32"></i>
                        Your data is protected and secure.
                    </p>
                </div>
            </div>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>