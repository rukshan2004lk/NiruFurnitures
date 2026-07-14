<?php
/**
 * NiRu-Furnitures — Admin Sales (admin/sales.php)
 */
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

// Optional Date Filter
$startDate = sanitize($_GET['start_date'] ?? date('Y-m-01')); // Default to 1st of current month
$endDate = sanitize($_GET['end_date'] ?? date('Y-m-t'));  // Default to last day of current month

// ── Daily Sales within Range ──────────────────────────────────
$sql = "SELECT DATE(created_at) as sale_date, COUNT(id) as order_count, SUM(total) as daily_revenue 
        FROM orders 
        WHERE DATE(created_at) >= ? AND DATE(created_at) <= ? AND status != 'cancelled' 
        GROUP BY DATE(created_at) 
        ORDER BY sale_date DESC";
$dailySales = dbFetchAll($sql, 'ss', $startDate, $endDate);

$totalRev = 0;
$totalOrd = 0;
foreach ($dailySales as $ds) {
    $totalRev += $ds['daily_revenue'];
    $totalOrd += $ds['order_count'];
}

$pageTitle = 'Sales — Admin';
$extraJs = [SITE_URL . '/assets/js/admin.js'];

include '../includes/admin-navbar.php';
?>

    <?php include '../includes/admin-sidebar.php'; ?>

    <main class="admin-main">
        <h1 class="h3 fw-bold mb-4">Sales Report</h1>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4 p-3">
            <form method="GET" action="sales.php" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-medium small text-muted">Start Date</label>
                    <input type="date" class="form-control" name="start_date" value="<?= e($startDate) ?>"
                        max="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium small text-muted">End Date</label>
                    <input type="date" class="form-control" name="end_date" value="<?= e($endDate) ?>"
                        max="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="sales.php" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card p-4 border-0 shadow-sm text-center">
                    <div class="text-muted small text-uppercase fw-bold mb-1">Period Revenue</div>
                    <div class="display-5 fw-bold text-success"><?= formatPrice((float) $totalRev) ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-4 border-0 shadow-sm text-center">
                    <div class="text-muted small text-uppercase fw-bold mb-1">Period Orders</div>
                    <div class="display-5 fw-bold text-primary"><?= number_format($totalOrd) ?></div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h6 class="fw-bold mb-0">Daily Breakdown</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Date</th>
                                <th class="text-center">Orders</th>
                                <th class="text-end pe-4">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dailySales as $ds): ?>
                                <tr>
                                    <td class="fw-medium text-dark"><?= formatDate($ds['sale_date']) ?></td>
                                    <td class="text-center"><?= $ds['order_count'] ?></td>
                                    <td class="text-end fw-bold pe-4"><?= formatPrice((float) $ds['daily_revenue']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($dailySales)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">No sales data for the selected
                                        period.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

<?php include '../includes/admin-footer.php'; ?>