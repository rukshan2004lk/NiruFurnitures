<?php
/**
 * NiRu-Furnitures — Admin Reports (admin/reports.php)
 */
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

// ── Stock Report ──────────────────────────────────────────────
$lowStock = dbFetchAll("SELECT id, name, stock, price, category_id FROM products WHERE stock < 10 AND status = 'active' ORDER BY stock ASC");
$outOfStock = dbFetchAll("SELECT id, name, stock, price, category_id FROM products WHERE stock = 0 ORDER BY name ASC");

// ── Top Customers ─────────────────────────────────────────────
$topCustomers = dbFetchAll("
    SELECT u.id, u.name, u.email, COUNT(o.id) AS order_count, SUM(o.total) AS total_spent 
    FROM users u 
    JOIN orders o ON o.user_id = u.id 
    WHERE o.status != 'cancelled' 
    GROUP BY u.id 
    ORDER BY total_spent DESC 
    LIMIT 10
");

// ── Most Sold Products ────────────────────────────────────────
$topProducts = dbFetchAll("
    SELECT p.id, p.name, p.stock, COALESCE(SUM(oi.qty), 0) AS total_sold 
    FROM products p 
    JOIN order_items oi ON oi.product_id = p.id 
    JOIN orders o ON o.id = oi.order_id 
    WHERE o.status != 'cancelled' 
    GROUP BY p.id 
    ORDER BY total_sold DESC 
    LIMIT 10
");

$pageTitle = 'Reports — Admin';
$extraJs = [SITE_URL . '/assets/js/admin.js'];

include '../includes/admin-navbar.php';
?>

    <?php include '../includes/admin-sidebar.php'; ?>

    <main class="admin-main">
        <h1 class="h3 fw-bold mb-4">Store Reports</h1>

        <div class="row g-4">
            <!-- Inventory Alerts -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Inventory
                            Alerts</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold small text-uppercase text-muted mb-3">Out of Stock</h6>
                        <?php if (empty($outOfStock)): ?>
                            <div class="alert alert-success py-2 small">No out of stock items.</div>
                        <?php else: ?>
                            <ul class="list-group mb-4">
                                <?php foreach ($outOfStock as $p): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="products.php?action=edit&id=<?= $p['id'] ?>"
                                            class="text-decoration-none fw-medium"><?= e($p['name']) ?></a>
                                        <span class="badge bg-danger rounded-pill">0</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <h6 class="fw-bold small text-uppercase text-muted mb-3">Low Stock (Under 10)</h6>
                        <?php if (empty($lowStock)): ?>
                            <div class="alert alert-success py-2 small">No low stock items.</div>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($lowStock as $p): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="products.php?action=edit&id=<?= $p['id'] ?>"
                                            class="text-decoration-none fw-medium"><?= e($p['name']) ?></a>
                                        <span class="badge bg-warning text-dark rounded-pill"><?= $p['stock'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Top Customers -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="fw-bold mb-0"><i class="bi bi-star me-2 text-warning"></i>Top Customers</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small">
                                    <tr>
                                        <th>Customer</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Total Spent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topCustomers as $c): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-medium text-dark"><?= e($c['name']) ?></div>
                                                <div class="small text-muted"><?= e($c['email']) ?></div>
                                            </td>
                                            <td class="text-center"><?= $c['order_count'] ?></td>
                                            <td class="text-end fw-bold text-success">
                                                <?= formatPrice((float) $c['total_spent']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($topCustomers)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-3 text-muted">No customer data available.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="fw-bold mb-0"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Best Selling
                            Products</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small">
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th class="text-end">Units Sold</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topProducts as $p): ?>
                                        <tr>
                                            <td>
                                                <a href="products.php?action=edit&id=<?= $p['id'] ?>"
                                                    class="fw-medium text-dark text-decoration-none"><?= e($p['name']) ?></a>
                                            </td>
                                            <td><?= stockBadge((int) $p['stock']) ?></td>
                                            <td class="text-end fw-bold"><?= $p['total_sold'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($topProducts)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-3 text-muted">No product data available.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

<?php include '../includes/admin-footer.php'; ?>