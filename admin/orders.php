<?php
/**
 * NiRu-Furnitures — Admin Orders (admin/orders.php)
 */
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

$id = (int) ($_GET['id'] ?? 0);

// ── Update Order Status ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = (int) $_POST['order_id'];
    $status = sanitize($_POST['status']);

    if (in_array($status, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
        dbExecute("UPDATE orders SET status = ? WHERE id = ?", 'si', $status, $order_id);
        setFlash('Order status updated.', 'success');
    }
    redirect("orders.php?id=$order_id");
}

$pageTitle = 'Orders — Admin';
$extraJs = [SITE_URL . '/assets/js/admin.js'];

include '../includes/admin-navbar.php';
?>

    <?php include '../includes/admin-sidebar.php'; ?>

    <main class="admin-main">
        <?= renderFlash() ?>

        <?php if ($id > 0):
            // ── VIEW ORDER DETAILS ──
            $o = dbFetchOne("SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone FROM orders o JOIN users u ON u.id = o.user_id WHERE o.id = ?", 'i', $id);
            if (!$o)
                redirect('orders.php');

            $items = dbFetchAll("SELECT oi.*, p.name, p.images FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?", 'i', $id);
            ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-1">Order #<?= str_pad($o['id'], 6, '0', STR_PAD_LEFT) ?></h1>
                    <p class="text-muted small mb-0">Placed on <?= formatDate($o['created_at'], true) ?></p>
                </div>
                <a href="orders.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Orders
                </a>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- Items -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                            <h6 class="fw-bold mb-0">Order Items</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0">
                                    <thead class="text-muted small border-bottom">
                                        <tr>
                                            <th class="ps-0">Product</th>
                                            <th class="text-center">Price</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end pe-0">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item):
                                            $imgs = json_decode($item['images'] ?? '[]', true);
                                            ?>
                                            <tr class="border-bottom">
                                                <td class="ps-0 py-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="<?= productImageUrl($imgs[0] ?? null) ?>" width="48"
                                                            height="48" class="rounded" style="object-fit:cover">
                                                        <a href="../product.php?id=<?= $item['product_id'] ?>" target="_blank"
                                                            class="fw-medium text-dark text-decoration-none"><?= e($item['name']) ?></a>
                                                    </div>
                                                </td>
                                                <td class="text-center"><?= formatPrice((float) $item['unit_price']) ?></td>
                                                <td class="text-center"><?= $item['qty'] ?></td>
                                                <td class="text-end pe-0 fw-medium">
                                                    <?= formatPrice($item['unit_price'] * $item['qty']) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end text-muted pt-4">Subtotal</td>
                                            <td class="text-end fw-medium pt-4">
                                                <?= formatPrice((float) $o['subtotal']) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end text-muted">Shipping</td>
                                            <td class="text-end fw-medium"><?= formatPrice((float) $o['shipping_cost']) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold fs-5">Total</td>
                                            <td class="text-end fw-bold fs-5 text-primary">
                                                <?= formatPrice((float) $o['total']) ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Status Update -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                            <h6 class="fw-bold mb-0">Order Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                Current Status: <?= orderStatusBadge($o['status']) ?>
                            </div>
                            <form method="POST">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="order_id" value="<?= $id ?>">
                                <div class="input-group">
                                    <select class="form-select" name="status">
                                        <option value="pending" <?= $o['status'] === 'pending' ? 'selected' : '' ?>>Pending
                                        </option>
                                        <option value="processing" <?= $o['status'] === 'processing' ? 'selected' : '' ?>>
                                            Processing</option>
                                        <option value="shipped" <?= $o['status'] === 'shipped' ? 'selected' : '' ?>>Shipped
                                        </option>
                                        <option value="delivered" <?= $o['status'] === 'delivered' ? 'selected' : '' ?>>
                                            Delivered
                                        </option>
                                        <option value="cancelled" <?= $o['status'] === 'cancelled' ? 'selected' : '' ?>>
                                            Cancelled
                                        </option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                            <h6 class="fw-bold mb-0">Customer Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="stat-icon stat-icon-primary rounded-circle"><i class="bi bi-person"></i></div>
                                <div>
                                    <div class="fw-medium"><?= e($o['customer_name']) ?></div>
                                    <div class="text-muted small"><?= e($o['customer_email']) ?></div>
                                </div>
                            </div>
                            <?php if ($o['customer_phone']): ?>
                                <div class="d-flex align-items-center gap-2 text-muted small">
                                    <i class="bi bi-telephone"></i> <?= e($o['customer_phone']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                            <h6 class="fw-bold mb-0">Shipping & Payment</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-1 text-muted small">Address:</p>
                            <p class="fw-medium mb-3">
                                <?= e($o['shipping_address']) ?><br>
                                <?= e($o['shipping_city']) ?>     <?= e($o['shipping_zip']) ?>
                            </p>
                            <p class="mb-1 text-muted small">Payment Method:</p>
                            <p class="fw-medium mb-0"><?= e(strtoupper(str_replace('_', ' ', $o['payment_method']))) ?></p>
                        </div>
                    </div>
                </div>
            </div>

        <?php else:
            // ── LIST ALL ORDERS ──
            $statusF = $_GET['status'] ?? '';
            $page = max(1, (int) ($_GET['page'] ?? 1));
            $perPage = 20;

            $where = ["1=1"];
            $params = [];
            $types = '';

            if ($statusF) {
                $where[] = "o.status = ?";
                $params[] = $statusF;
                $types .= 's';
            }
            $whereSQL = implode(' AND ', $where);

            $total = dbFetchOne("SELECT COUNT(*) AS total FROM orders o WHERE $whereSQL", $types, ...$params)['total'];
            $pag = paginate($total, $perPage, $page);

            $orders = dbFetchAll("SELECT o.id, o.created_at, o.status, o.total, u.name as customer_name 
                                  FROM orders o JOIN users u ON u.id = o.user_id 
                                  WHERE $whereSQL ORDER BY o.created_at DESC LIMIT {$perPage} OFFSET {$pag['offset']}", $types, ...$params);
            ?>

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h1 class="h3 fw-bold mb-0">Orders</h1>
            </div>

            <!-- Filter -->
            <div class="card border-0 shadow-sm mb-4 p-3">
                <form method="GET" action="orders.php" class="d-flex align-items-center gap-3">
                    <label class="fw-medium mb-0">Filter by Status:</label>
                    <select name="status" class="form-select" style="width:200px" onchange="this.form.submit()">
                        <option value="">All Orders</option>
                        <option value="pending" <?= $statusF === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="processing" <?= $statusF === 'processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="shipped" <?= $statusF === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                        <option value="delivered" <?= $statusF === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="cancelled" <?= $statusF === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </form>
            </div>

            <!-- Table -->
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Order ID</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">#<?= str_pad($o['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= formatDate($o['created_at']) ?></td>
                                    <td><?= e($o['customer_name']) ?></td>
                                    <td class="fw-medium"><?= formatPrice((float) $o['total']) ?></td>
                                    <td><?= orderStatusBadge($o['status']) ?></td>
                                    <td class="text-end pe-4">
                                        <a href="orders.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary">View
                                            Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No orders found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <?= paginationLinks($pag['pages'], $pag['current'], 'orders.php?' . http_build_query(array_filter(['status' => $statusF]))) ?>
            </div>

        <?php endif; ?>

<?php include '../includes/admin-footer.php'; ?>