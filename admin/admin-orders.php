<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connection.php';

if (!isset($_SESSION['a']) || ((int)($_SESSION['a']['role_id'] ?? 0) !== 1)) {
    header("Location: ../login.php");
    exit();
}

$admin = $_SESSION['a'];

$total_orders_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `orders`");
$total_orders = (int)($total_orders_rs->fetch_assoc()['cnt'] ?? 0);

$pending_orders_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `orders` WHERE `order_status_id` = 6");
$pending_orders = (int)($pending_orders_rs->fetch_assoc()['cnt'] ?? 0);

$process_orders_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `orders` WHERE `order_status_id` IN (7, 8)");
$process_orders = (int)($process_orders_rs->fetch_assoc()['cnt'] ?? 0);

$mtd_rev_rs = Database::search("SELECT SUM(`total_amount`) AS `rev` 
                               FROM `orders` 
                               WHERE `payment_status` = 'paid' 
                                 AND MONTH(`placed_at`) = MONTH(CURRENT_DATE()) 
                                 AND YEAR(`placed_at`) = YEAR(CURRENT_DATE())");
$mtd_revenue = (float)($mtd_rev_rs->fetch_assoc()['rev'] ?? 0.00);

$status_list_rs = Database::search("SELECT * FROM `status` WHERE `status_id` IN (6, 7, 8, 9, 10, 11) ORDER BY `status_id` ASC");
$status_options = [];
while ($st = $status_list_rs->fetch_assoc()) {
    $status_options[] = $st;
}

$orders_query = "SELECT o.*, s.status_name,
                        COALESCE(u.first_name, o.customer_name) AS display_name,
                        COALESCE(u.email, o.customer_email) AS display_email,
                        (SELECT p.name FROM order_items oi INNER JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = o.order_id LIMIT 1) AS sample_product,
                        (SELECT SUM(quantity) FROM order_items WHERE order_id = o.order_id) AS total_items_qty
                 FROM `orders` o
                 LEFT JOIN `status` s ON o.order_status_id = s.status_id
                 LEFT JOIN `user` u ON o.user_id = u.user_id
                 ORDER BY o.placed_at DESC";
$orders_rs = Database::search($orders_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NiRu Furnitures - Order Management</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

  <div class="admin-mobile-topbar">
    <div class="fw-bold fs-5 text-white">NiRu Admin</div>
    <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileSidebar">
      <i class="bi bi-list fs-5"></i> Menu
    </button>
  </div>

  <div class="offcanvas offcanvas-start bg-dark text-light" tabindex="-1" id="adminMobileSidebar">
    <div class="offcanvas-header border-bottom border-secondary">
      <h5 class="offcanvas-title text-white fw-bold">Admin Navigation</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3">
      <ul class="nav flex-column gap-2 mb-4">
        <li class="nav-item"><a href="admin-dashboard.php" class="nav-link text-white-50"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
        <li class="nav-item"><a href="admin-products.php" class="nav-link text-white-50"><i class="bi bi-box-seam me-2"></i>Products</a></li>
        <li class="nav-item"><a href="admin-orders.php" class="nav-link active fw-bold text-white"><i class="bi bi-cart me-2"></i>Orders</a></li>
        <li class="nav-item"><a href="admin-users.php" class="nav-link text-white-50"><i class="bi bi-people me-2"></i>Users</a></li>
        <li class="nav-item"><a href="admin-settings.php" class="nav-link text-white-50"><i class="bi bi-gear me-2"></i>Settings</a></li>
        <li class="nav-item"><hr class="dropdown-divider border-secondary"></li>
        <li class="nav-item"><a href="../index.php" class="admin-link-store"><i class="bi bi-shop me-2"></i>Exit to Storefront</a></li>
        <li class="nav-item"><a href="../logout.php" class="admin-link-logout"><i class="bi bi-power me-2"></i>Log Out</a></li>
      </ul>
    </div>
  </div>

  <aside class="admin-sidebar">
    <div class="admin-brand">
      <h2>Admin Panel</h2>
      <small>Manage NiRu Furnitures</small>
    </div>

    <ul class="admin-menu">
      <li><a href="admin-dashboard.php" class="admin-link"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
      <li><a href="admin-products.php" class="admin-link"><i class="bi bi-box-seam"></i> Products</a></li>
      <li><a href="admin-orders.php" class="admin-link active"><i class="bi bi-cart"></i> Orders</a></li>
      <li><a href="admin-users.php" class="admin-link"><i class="bi bi-people"></i> Users</a></li>
      <li><a href="admin-settings.php" class="admin-link"><i class="bi bi-gear"></i> Settings</a></li>
    </ul>

    <div class="px-3 mt-auto d-flex flex-column gap-2 mb-3">
      <a href="../index.php" class="admin-link-store"><i class="bi bi-shop"></i> View Store</a>
      <a href="../logout.php" class="admin-link-logout"><i class="bi bi-box-arrow-left"></i> Log Out</a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="admin-main">
    
    <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-5">
      <div>
        <h1 class="display-6 fw-bold mb-1" style="color: var(--niru-primary);">Order Management</h1>
        <p class="text-muted mb-0">Review and update customer orders across fulfillment stages.</p>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-dark d-flex align-items-center gap-2 px-4 py-2" style="background-color: var(--niru-primary); border-radius: 12px;" onclick="window.print();">
          <i class="bi bi-download"></i> Generate Report
        </button>
      </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row g-4 mb-4">
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card bg-white p-3 rounded-4 shadow-sm border">
          <div class="small text-muted mb-2">Total Orders</div>
          <div class="d-flex align-items-center justify-content-between">
            <h3 class="fs-4 fw-semibold mb-0" style="color: var(--niru-primary);"><?php echo number_format($total_orders); ?></h3>
            <i class="bi bi-graph-up text-success fs-5"></i>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card bg-white p-3 rounded-4 shadow-sm border">
          <div class="small text-muted mb-2">Pending Dispatch</div>
          <div class="d-flex align-items-center justify-content-between">
            <h3 class="fs-4 fw-semibold mb-0" style="color: #ca8a04;"><?php echo number_format($pending_orders); ?></h3>
            <i class="bi bi-clock-history fs-5" style="color: #ca8a04;"></i>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card bg-white p-3 rounded-4 shadow-sm border">
          <div class="small text-muted mb-2">In Process (Packed / Out)</div>
          <div class="d-flex align-items-center justify-content-between">
            <h3 class="fs-4 fw-semibold mb-0" style="color: var(--niru-secondary);"><?php echo number_format($process_orders); ?></h3>
            <i class="bi bi-gear fs-5" style="color: var(--niru-secondary);"></i>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card bg-white p-3 rounded-4 shadow-sm border">
          <div class="small text-muted mb-2">Revenue (MTD)</div>
          <div class="d-flex align-items-center justify-content-between">
            <h3 class="fs-4 fw-semibold mb-0" style="color: var(--niru-primary);">Rs. <?php echo number_format($mtd_revenue, 2); ?></h3>
            <i class="bi bi-cash-stack fs-5" style="color: var(--niru-primary);"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Live Search Input -->
    <div class="search-input-wrapper mb-4 position-relative">
      <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
      <input type="text" id="adminOrderSearchInput" class="form-control rounded-pill ps-5 py-2 shadow-sm" placeholder="Search orders by customer, ID, or product..." onkeyup="filterAdminOrdersTable();">
    </div>

    <!-- Table Container -->
    <div class="admin-table-box bg-white rounded-4 border shadow-sm p-4">
      <div class="table-header-bar d-flex align-items-center justify-content-between mb-3">
        <h2 class="fs-4 fw-semibold mb-0" style="color: var(--niru-primary);">Orders</h2>
        
        <!-- Filter Dropdown showing only IDs 6 to 11 -->
        <div class="dropdown">
          <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2 px-3 py-2 rounded-2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-funnel"></i> Filter Status
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow border-0">
            <li><a class="dropdown-item" href="javascript:void(0);" onclick="filterOrdersByStatusPill('all');">All Statuses</a></li>
            <?php foreach ($status_options as $opt) { ?>
              <li><a class="dropdown-item" href="javascript:void(0);" onclick="filterOrdersByStatusPill('<?php echo htmlspecialchars($opt['status_name']); ?>');"><?php echo htmlspecialchars($opt['status_name']); ?></a></li>
            <?php } ?>
          </ul>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-management align-middle mb-0">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Product Sample</th>
              <th>Quantity</th>
              <th>Amount</th>
              <th>Date</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="adminOrdersTableBody">
            <?php 
            if ($orders_rs->num_rows > 0) {
              $avatar_bg = ['#ffdbd0', '#feddb3', '#e4e4cc', '#e5e2e1'];
              $color_idx = 0;

              while ($order = $orders_rs->fetch_assoc()) {
                $oid = (int)$order['order_id'];
                $order_num = htmlspecialchars($order['order_number']);
                $c_name = htmlspecialchars($order['display_name'] ?? 'Customer');
                $initial = strtoupper(substr($c_name, 0, 1));
                $bg = $avatar_bg[$color_idx % count($avatar_bg)];
                $color_idx++;

                $p_name = htmlspecialchars($order['sample_product'] ?? 'General Order');
                $qty = (int)($order['total_items_qty'] ?? 1);
                $amount = number_format($order['total_amount'], 2);
                $placed_date = date("M d, Y", strtotime($order['placed_at']));
                $status_name = $order['status_name'] ?? 'Pending Dispatch';
                $status_id = (int)($order['order_status_id'] ?? 6);

                // Styling tailored to statuses 6 through 11
                $dot_class = 'dot-pill-pending';
                if ($status_id === 9) { // Delivered
                  $dot_class = 'dot-pill-delivered';
                } else if ($status_id === 7 || $status_id === 8) { // Packed or Out for Delivery
                  $dot_class = 'dot-pill-processing';
                } else if ($status_id === 10 || $status_id === 11) { // Returned or Failed Delivery
                  $dot_class = 'bg-danger-subtle text-danger';
                }
            ?>
              <tr class="admin-order-row">
                <td class="fw-semibold" style="color: var(--niru-primary);">#<?php echo $order_num; ?></td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 600; background-color: <?php echo $bg; ?>;">
                      <?php echo $initial; ?>
                    </div>
                    <div>
                      <span class="d-block fw-semibold text-dark"><?php echo $c_name; ?></span>
                      <small class="text-muted" style="font-size: 11px;"><?php echo htmlspecialchars($order['display_email'] ?? ''); ?></small>
                    </div>
                  </div>
                </td>
                <td><?php echo $p_name; ?></td>
                <td><?php echo str_pad($qty, 2, '0', STR_PAD_LEFT); ?></td>
                <td class="fw-semibold">Rs. <?php echo $amount; ?></td>
                <td class="text-muted"><?php echo $placed_date; ?></td>
                <td>
                  <span class="dot-pill <?php echo $dot_class; ?> order-status-pill">
                    <span class="dot"></span> <?php echo htmlspecialchars($status_name); ?>
                  </span>
                </td>
                <td class="text-end">
                  <a href="../user/invoice.php?id=<?php echo $oid; ?>" target="_blank" class="btn btn-sm btn-link text-dark p-1" title="View Invoice">
                    <i class="bi bi-eye"></i>
                  </a>
                  <button type="button" class="btn btn-sm btn-link text-dark p-1" title="Change Status" onclick="openOrderStatusModal(<?php echo $oid; ?>, <?php echo $status_id; ?>, '<?php echo $order_num; ?>');">
                    <i class="bi bi-pencil"></i>
                  </button>
                </td>
              </tr>
            <?php 
              }
            } else { 
            ?>
              <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                  <i class="bi bi-bag-x display-6 d-block mb-2 text-secondary"></i>
                  No customer orders found.
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>

  <!-- ======================== STATUS EDIT MODAL (LIMITED TO 6-11) ======================== -->
  <div class="modal fade" id="changeOrderStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold" style="color: var(--niru-primary);">Update Order Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <form id="changeOrderStatusForm">
            <input type="hidden" name="order_id" id="status_modal_order_id">

            <p class="mb-3 text-muted">
              Updating status for Order: <strong id="status_modal_order_number" class="text-dark"></strong>
            </p>

            <div class="mb-3">
              <label class="form-label fw-semibold">Fulfillment Status</label>
              <select name="status_id" id="status_modal_select" class="form-select" required>
                <?php foreach ($status_options as $st) { ?>
                  <option value="<?php echo $st['status_id']; ?>"><?php echo htmlspecialchars($st['status_name']); ?></option>
                <?php } ?>
              </select>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-dark fw-semibold" style="background-color: var(--niru-primary);" onclick="submitOrderStatusUpdate();">
                Save Status
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/script.js"></script>
</body>
</html>