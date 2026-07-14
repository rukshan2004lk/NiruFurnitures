<?php
/**
 * NiRu-Furnitures — Order Success (order-success.php)
 */
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$orderId = (int) ($_GET['id'] ?? 0);
$userId = currentUserId();

// Verify order belongs to user
$order = dbFetchOne("SELECT * FROM orders WHERE id = ? AND user_id = ?", 'ii', $orderId, $userId);

if (!$order) {
    redirect('dashboard.php');
}

$pageTitle = 'Order Successful — NiRu-Furnitures';
include 'includes/head.php';
include 'includes/navbar.php';
?>

<div class="order-success-page">
    <div class="container-xl">
        <div class="order-success-card" data-aos="fade-up">

            <!-- Success Icon -->
            <div class="order-success-icon">
                <i class="bi bi-check-lg"></i>
            </div>

            <h1>Thank You For Your Order!</h1>
            <p>Your order has been placed successfully. We'll start processing it right away.</p>

            <!-- Order Meta -->
            <div class="order-success-meta">
                <div class="order-success-meta-item">
                    <div class="label">Order Number</div>
                    <div class="value">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></div>
                </div>
                <div class="order-success-meta-item">
                    <div class="label">Total Amount</div>
                    <div class="value"><?= formatPrice((float) $order['total']) ?></div>
                </div>
                <div class="order-success-meta-item">
                    <div class="label">Payment</div>
                    <div class="value" style="font-size:.9rem"><?= e(ucwords(str_replace('_', ' ', $order['payment_method']))) ?></div>
                </div>
            </div>

            <p class="order-success-note">
                We've sent a confirmation to your email with your order details.<br>
                You can track the status of your order in your dashboard.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="dashboard.php" class="btn-order-outline">
                    <i class="bi bi-receipt"></i> View Order History
                </a>
                <a href="shop.php" class="btn-order-primary">
                    <i class="bi bi-bag"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Confetti Effect -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        var duration = 3 * 1000;
        var animationEnd = Date.now() + duration;
        var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0, colors: ['#7d4f3f', '#c4a484', '#f5ddc8', '#3d2219', '#ddc9b8'] };

        function randomInRange(min, max) { return Math.random() * (max - min) + min; }

        var interval = setInterval(function () {
            var timeLeft = animationEnd - Date.now();
            if (timeLeft <= 0) return clearInterval(interval);

            var particleCount = 50 * (timeLeft / duration);
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
        }, 250);
    });
</script>

<?php include 'includes/footer.php'; ?>