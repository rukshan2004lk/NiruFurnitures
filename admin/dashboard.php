<?php
/**
 * NiRu-Furnitures — Admin Dashboard (admin/dashboard.php)
 */
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

// ── Metrics ───────────────────────────────────────────────────
$totalRevenue = dbFetchOne("SELECT SUM(total) AS total FROM orders WHERE status != 'cancelled'")['total'] ?? 0;
$totalOrders = dbFetchOne("SELECT COUNT(*) AS total FROM orders")['total'] ?? 0;
$totalUsers = dbFetchOne("SELECT COUNT(*) AS total FROM users WHERE role = 'customer'")['total'] ?? 0;
$lowStock = dbFetchOne("SELECT COUNT(*) AS total FROM products WHERE stock < 5 AND status = 'active'")['total'] ?? 0;

// ── Revenue Chart (Last 7 Days) ───────────────────────────────
$dates = [];
$revs = [];
$ords = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dates[] = date('M d', strtotime($date));

    $stats = dbFetchOne("SELECT SUM(total) AS r, COUNT(id) AS c FROM orders WHERE DATE(created_at) = ? AND status != 'cancelled'", 's', $date);
    $revs[] = (float) ($stats['r'] ?? 0);
    $ords[] = (int) ($stats['c'] ?? 0);
}

// ── Recent Orders ─────────────────────────────────────────────
$recentOrders = dbFetchAll("SELECT o.id, o.created_at, o.status, o.total, u.name as customer_name 
                            FROM orders o JOIN users u ON u.id = o.user_id 
                            ORDER BY o.created_at DESC LIMIT 5");

$pageTitle = 'Admin Dashboard — NiRu-Furnitures';

include '../includes/admin-navbar.php';
?>

<?php include '../includes/admin-sidebar.php'; ?>

<main class="admin-main">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Dashboard Overview</h1>
            <p class="text-muted small mb-0">Welcome back, <?= e($_SESSION['user_name'] ?? 'Admin') ?>!</p>
        </div>
        <div>
            <a href="../index.php" class="btn btn-outline-primary btn-sm" target="_blank">
                <i class="bi bi-box-arrow-up-right me-1"></i>View Store
            </a>
        </div>
    </div>

    <?= renderFlash() ?>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="admin-stat-card primary">
                <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value"><?= formatPrice((float) $totalRevenue) ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="admin-stat-card success">
                <div class="stat-icon"><i class="bi bi-bag-check"></i></div>
                <div class="stat-label">Total Orders</div>
                <div class="stat-value"><?= number_format($totalOrders) ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="admin-stat-card info">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <div class="stat-label">Total Customers</div>
                <div class="stat-value"><?= number_format($totalUsers) ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="admin-stat-card danger">
                <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="stat-label">Low Stock Alerts</div>
                <div class="stat-value"><?= number_format($lowStock) ?></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="fw-bold mb-0">Revenue (Last 7 Days)</h6>
                </div>
                <div class="card-body">
                    <div style="height:300px">
                        <canvas id="revenueChart" data-labels='<?= json_encode($dates) ?>'
                            data-revenue='<?= json_encode($revs) ?>' data-orders='<?= json_encode($ords) ?>'></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div
                    class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Recent Orders</h6>
                    <a href="orders.php" class="text-decoration-none small">View All</a>
                </div>
                <div class="card-body p-0 mt-3">
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentOrders as $ro): ?>
                            <a href="orders.php?id=<?= $ro['id'] ?>" class="list-group-item list-group-item-action p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span
                                        class="fw-bold text-primary">#<?= str_pad($ro['id'], 6, '0', STR_PAD_LEFT) ?></span>
                                    <?= orderStatusBadge($ro['status']) ?>
                                </div>
                                <div class="d-flex justify-content-between align-items-center small text-muted">
                                    <span><?= e($ro['customer_name']) ?></span>
                                    <span class="fw-medium text-dark"><?= formatPrice((float) $ro['total']) ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                        <?php if (empty($recentOrders)): ?>
                            <div class="p-4 text-center text-muted small">No recent orders found.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/admin-footer.php'; ?>