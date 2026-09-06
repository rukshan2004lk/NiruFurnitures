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

$prod_count_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `products`");
$total_products = (int)($prod_count_rs->fetch_assoc()['cnt'] ?? 0);

$orders_count_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `orders`");
$total_orders = (int)($orders_count_rs->fetch_assoc()['cnt'] ?? 0);

$cust_count_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `user` WHERE `role_id` = 2 OR `role_id` IS NULL");
$total_customers = (int)($cust_count_rs->fetch_assoc()['cnt'] ?? 0);

$rev_rs = Database::search("SELECT SUM(`total_amount`) AS `rev` FROM `orders` WHERE `payment_status` = 'paid'");
$total_revenue = (float)($rev_rs->fetch_assoc()['rev'] ?? 0.00);

$recent_orders_query = "SELECT o.*, s.status_name 
                        FROM `orders` o 
                        LEFT JOIN `status` s ON o.order_status_id = s.status_id 
                        ORDER BY o.placed_at DESC LIMIT 5";
$recent_orders_rs = Database::search($recent_orders_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NiRu Furnitures - Admin Dashboard</title>
  
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
    <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileSidebar" aria-controls="adminMobileSidebar">
      <i class="bi bi-list fs-5"></i> Menu
    </button>
  </div>

  <div class="offcanvas offcanvas-start bg-dark text-light" tabindex="-1" id="adminMobileSidebar" aria-labelledby="adminMobileSidebarLabel">
    <div class="offcanvas-header border-bottom border-secondary">
      <h5 class="offcanvas-title text-white fw-bold" id="adminMobileSidebarLabel">Admin Navigation</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
      <ul class="nav flex-column gap-2 mb-4">
        <li class="nav-item"><a href="admin-dashboard.php" class="nav-link active fw-bold text-white"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
        <li class="nav-item"><a href="admin-products.php" class="nav-link text-white-50"><i class="bi bi-box-seam me-2"></i>Products</a></li>
        <li class="nav-item"><a href="admin-orders.php" class="nav-link text-white-50"><i class="bi bi-cart me-2"></i>Orders</a></li>
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
      <li>
        <a href="admin-dashboard.php" class="admin-link active">
          <i class="bi bi-speedometer2"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="admin-products.php" class="admin-link">
          <i class="bi bi-box-seam"></i> Products
        </a>
      </li>
      <li>
        <a href="admin-orders.php" class="admin-link">
          <i class="bi bi-cart"></i> Orders
        </a>
      </li>
      <li>
        <a href="admin-users.php" class="admin-link">
          <i class="bi bi-people"></i> Users
        </a>
      </li>
      <li>
        <a href="admin-settings.php" class="admin-link">
          <i class="bi bi-gear"></i> Settings
        </a>
      </li>
    </ul>

<div class="px-3 mt-auto d-flex flex-column gap-2 mb-3">

    <a href="../index.php"
       class="admin-link-store text-decoration-none d-flex align-items-center gap-2 fw-medium">
        <i class="bi bi-shop"></i>
        View Store
    </a>

    <a href="../logout.php"
       class="admin-link-logout text-decoration-none d-flex align-items-center gap-2 fw-medium">
        <i class="bi bi-box-arrow-left"></i>
        Log Out
    </a>

</div>
  </aside>

  <!-- Main Content Area -->
  <main class="admin-main">
    
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-5">
      <div>
        <h1 class="display-6 fw-bold mb-1" style="color: var(--niru-primary);">Overview</h1>
        <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($admin['first_name'] ?? 'Administrator'); ?>! Here is your business snapshot.</p>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-light border d-flex align-items-center gap-2 px-3 py-2 fw-semibold" style="background-color: #f0eded; color: var(--niru-primary);" onclick="window.location.reload();">
          <i class="bi bi-arrow-clockwise"></i> Refresh Data
        </button>
        <button class="btn btn-dark d-flex align-items-center gap-2 px-4 py-2 fw-semibold" style="background-color: var(--niru-primary);" onclick="window.print();">
          <i class="bi bi-printer"></i> Print Overview
        </button>
      </div>
    </div>

    <!-- Stat Bento Cards -->
    <div class="row g-4 mb-5">
      
      <!-- Total Products -->
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="admin-stat-card">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="p-2 rounded-circle" style="background-color: #ffdbd0; color: var(--niru-primary);">
              <i class="bi bi-box-seam fs-4"></i>
            </div>
            <span class="badge bg-light text-dark border">Catalog</span>
          </div>
          <small class="text-muted text-uppercase fw-semibold d-block mb-1" style="letter-spacing: 0.7px;">Total Products</small>
          <h2 class="display-6 fw-bold mb-0" style="color: var(--niru-primary);"><?php echo number_format($total_products); ?></h2>
        </div>
      </div>

      <!-- Total Orders -->
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="admin-stat-card">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="p-2 rounded-circle" style="background-color: #feddb3; color: var(--niru-secondary);">
              <i class="bi bi-bag-check fs-4"></i>
            </div>
            <span class="badge bg-light text-dark border">Fulfilled</span>
          </div>
          <small class="text-muted text-uppercase fw-semibold d-block mb-1" style="letter-spacing: 0.7px;">Total Orders</small>
          <h2 class="display-6 fw-bold mb-0" style="color: var(--niru-primary);"><?php echo number_format($total_orders); ?></h2>
        </div>
      </div>

      <!-- Total Customers -->
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="admin-stat-card">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="p-2 rounded-circle" style="background-color: #e4e4cc; color: #303221;">
              <i class="bi bi-people fs-4"></i>
            </div>
            <span class="badge bg-light text-dark border">Registered</span>
          </div>
          <small class="text-muted text-uppercase fw-semibold d-block mb-1" style="letter-spacing: 0.7px;">Total Customers</small>
          <h2 class="display-6 fw-bold mb-0" style="color: var(--niru-primary);"><?php echo number_format($total_customers); ?></h2>
        </div>
      </div>

      <!-- Total Revenue -->
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="admin-stat-card">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="p-2 rounded-circle" style="background-color: #ffdbd0; color: var(--niru-primary);">
              <i class="bi bi-cash-stack fs-4"></i>
            </div>
            <span class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
          </div>
          <small class="text-muted text-uppercase fw-semibold d-block mb-1" style="letter-spacing: 0.7px;">Total Revenue</small>
          <h2 class="display-6 fw-bold mb-0" style="color: var(--niru-primary);">Rs. <?php echo number_format($total_revenue, 2); ?></h2>
        </div>
      </div>

    </div>

    <!-- Recent Orders Table -->
    <div class="admin-table-card">
      <div class="card-header-admin d-flex justify-content-between align-items-center">
        <h2 class="fs-4 fw-semibold mb-0" style="color: var(--niru-primary);">Recent Orders</h2>
        <a href="admin-orders.php" class="fw-semibold text-decoration-none small" style="color: var(--niru-primary); letter-spacing: 0.7px;">View All Orders</a>
      </div>

      <div class="table-responsive">
        <table class="table table-admin align-middle mb-0">
          <thead>
            <tr>
              <th>ORDER ID</th>
              <th>CUSTOMER</th>
              <th>DATE</th>
              <th>AMOUNT</th>
              <th>STATUS</th>
              <th class="text-end">ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if ($recent_orders_rs->num_rows > 0) {
              $avatar_bg_colors = ['#ffdbd0', '#feddb3', '#e4e4cc', '#e5e2e1'];
              $i = 0;

              while ($order = $recent_orders_rs->fetch_assoc()) {
                $initial = strtoupper(substr($order['customer_name'] ?? 'C', 0, 1));
                $bg_color = $avatar_bg_colors[$i % count($avatar_bg_colors)];
                $i++;

                $status_name = $order['status_name'] ?? 'Pending Dispatch';
                
                // Color-coded status badge
                $badge_class = 'bg-primary-subtle text-primary';
                if (stripos($status_name, 'Delivered') !== false) {
                  $badge_class = 'bg-success-subtle text-success';
                } else if (stripos($status_name, 'Pending') !== false || stripos($status_name, 'Packed') !== false) {
                  $badge_class = 'bg-warning-subtle text-warning-emphasis';
                } else if (stripos($status_name, 'Failed') !== false || stripos($status_name, 'Returned') !== false) {
                  $badge_class = 'bg-danger-subtle text-danger';
                }
            ?>
              <tr>
                <td class="fw-semibold" style="color: var(--niru-primary);">
                  <?php echo htmlspecialchars($order['order_number']); ?>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="customer-avatar d-flex align-items-center justify-content-center fw-bold" style="background-color: <?php echo $bg_color; ?>; width: 32px; height: 32px; border-radius: 50%;">
                      <?php echo $initial; ?>
                    </div>
                    <div>
                      <span class="d-block fw-semibold text-dark"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                      <small class="text-muted"><?php echo htmlspecialchars($order['customer_phone']); ?></small>
                    </div>
                  </div>
                </td>
                <td class="text-muted">
                  <?php echo date("M d, Y", strtotime($order['placed_at'])); ?>
                </td>
                <td class="fw-bold">
                  Rs. <?php echo number_format($order['total_amount'], 2); ?>
                </td>
                <td>
                  <span class="badge rounded-pill <?php echo $badge_class; ?> px-3 py-2 fw-bold">
                    <?php echo htmlspecialchars($status_name); ?>
                  </span>
                </td>
                <td class="text-end">
                  <a href="../user/invoice.php?id=<?php echo $order['order_id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="View Printable Invoice">
                    <i class="bi bi-eye"></i>
                  </a>
                </td>
              </tr>
            <?php 
              }
            } else { 
            ?>
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="bi bi-box-seam display-6 d-block mb-2 text-secondary"></i>
                  No orders recorded in the system yet.
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/script.js"></script>
</body>
</html>