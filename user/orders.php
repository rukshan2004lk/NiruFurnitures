<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['u'])) {
    header("Location: ../login.php");
    exit();
}

$user = $_SESSION['u'];
$user_id = (int)$user['user_id'];
$first_name = htmlspecialchars($user['first_name'] ?? 'User');
$last_name = htmlspecialchars($user['last_name'] ?? '');
$email = htmlspecialchars($user['email'] ?? '');
$initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));

$total_orders_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `orders` WHERE `user_id` = '$user_id'");
$total_orders = (int)($total_orders_rs->fetch_assoc()['cnt'] ?? 0);

$in_transit_rs = Database::search("SELECT COUNT(*) AS `cnt` 
                                  FROM `orders` o 
                                  INNER JOIN `status` s ON o.order_status_id = s.status_id 
                                  WHERE o.user_id = '$user_id' 
                                    AND s.status_name IN ('Pending Dispatch', 'Packed', 'Out for Delivery')");
$in_transit = (int)($in_transit_rs->fetch_assoc()['cnt'] ?? 0);

$total_spent_rs = Database::search("SELECT SUM(`total_amount`) AS `total` 
                                   FROM `orders` 
                                   WHERE `user_id` = '$user_id' AND `payment_status` = 'paid'");
$total_spent = (float)($total_spent_rs->fetch_assoc()['total'] ?? 0.00);

$orders_query = "SELECT o.*, s.status_name 
                 FROM `orders` o 
                 LEFT JOIN `status` s ON o.order_status_id = s.status_id 
                 WHERE o.user_id = '$user_id' 
                 ORDER BY o.placed_at DESC";
$orders_rs = Database::search($orders_query);
$orders_count = $orders_rs->num_rows;
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NiRu Furnitures - My Orders</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap"
      rel="stylesheet"
    />
    
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      rel="stylesheet"
    />
    
    <link rel="stylesheet" href="../assets/css/style.css" />
  </head>
  <body>
    
    <!-- Mobile Topbar -->
    <div class="mobile-topbar">
      <a href="../index.php" class="brand-logo text-decoration-none">NiRu</a>
      <button
        class="btn btn-dark"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#userMobileSidebar"
        aria-controls="userMobileSidebar"
      >
        <i class="bi bi-list fs-5"></i> Menu
      </button>
    </div>

    <!-- Mobile Offcanvas -->
    <div
      class="offcanvas offcanvas-start bg-light"
      tabindex="-1"
      id="userMobileSidebar"
      aria-labelledby="userMobileSidebarLabel"
    >
      <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold" id="userMobileSidebarLabel">User Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body p-3">
        <ul class="nav flex-column gap-2 mb-4">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link text-muted"><i class="bi bi-grid me-2"></i>Dashboard</a>
          </li>
          <li class="nav-item">
            <a href="orders.php" class="nav-link active fw-bold text-dark"><i class="bi bi-bag me-2"></i>My Orders</a>
          </li>
          <li class="nav-item">
            <a href="wishlist.php" class="nav-link text-muted"><i class="bi bi-heart me-2"></i>Saved Wishlist</a>
          </li>
          <li class="nav-item">
            <a href="settings.php" class="nav-link text-muted"><i class="bi bi-gear me-2"></i>Account Settings</a>
          </li>
          <li class="nav-item"><hr class="dropdown-divider" /></li>
          <li class="nav-item">
            <a href="../index.php" class="nav-link text-primary"><i class="bi bi-arrow-left me-2"></i>Back to Storefront</a>
          </li>
          <li class="nav-item">
            <a href="../logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Desktop Sidebar -->
    <aside class="dashboard-sidebar">
      <a href="../index.php" class="sidebar-brand">NiRu</a>

      <ul class="sidebar-menu mb-5">
        <li>
          <a href="dashboard.php" class="sidebar-link">
            <i class="bi bi-grid"></i> Dashboard
          </a>
        </li>
        <li>
          <a href="orders.php" class="sidebar-link active">
            <i class="bi bi-bag"></i> My Orders
          </a>
        </li>
        <li>
          <a href="wishlist.php" class="sidebar-link">
            <i class="bi bi-heart"></i> Wishlist
          </a>
        </li>
        <li>
          <a href="settings.php" class="sidebar-link">
            <i class="bi bi-gear"></i> Settings
          </a>
        </li>
      </ul>
      
      <div class="user-profile-badge mt-auto">
        <div class="avatar-sm"><?php echo $initials ?: 'U'; ?></div>
        <div class="overflow-hidden">
          <h4 class="fs-6 fw-semibold mb-0 text-truncate" style="color: var(--niru-primary)">
            <?php echo $first_name . ' ' . $last_name; ?>
          </h4>
          <small class="text-muted text-truncate d-block"><?php echo $email; ?></small>
        </div>
      </div>

      <a href="../logout.php" class="btn btn-outline-danger btn-sm mt-3 m-2"><i class="bi bi-box-arrow-right me-1"></i> Log Out</a>
    </aside>

    <!-- Main Container -->
    <main class="orders-main">
      
      <div class="orders-container">
        
        <!-- Header Bar with Filter & Search -->
        <div class="top-header-bar d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
          <div>
            <h1 class="fs-3 fw-semibold mb-0" style="color: var(--niru-primary)">
              My Orders
            </h1>
          </div>
          
          <div class="d-flex align-items-center gap-2">
            <!-- Real-time Filter Input -->
            <div class="input-group input-group-sm" style="max-width: 240px;">
              <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
              <input type="text" id="orderSearchInput" onkeyup="filterOrders();" class="form-control border-start-0" placeholder="Search orders...">
            </div>

            <!-- Status Dropdown Filter -->
            <div class="dropdown">
              <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-funnel me-1"></i> Filter
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="filterByStatus('all')">All Orders</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="filterByStatus('Delivered')">Delivered</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="filterByStatus('Dispatch')">Pending Dispatch</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="filterByStatus('Delivery')">Out for Delivery</a></li>
              </ul>
            </div>
          </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="row g-4 mb-5">
          <div class="col-12 col-md-4">
            <div class="summary-card">
              <div
                class="summary-card-icon"
                style="background-color: #ffdbd0; color: var(--niru-primary)"
              >
                <i class="bi bi-box-seam"></i>
              </div>
              <div>
                <div class="small text-muted mb-1">Total Orders</div>
                <h3
                  class="fs-4 fw-semibold mb-0"
                  style="color: var(--niru-primary)"
                >
                  <?php echo str_pad($total_orders, 2, '0', STR_PAD_LEFT); ?>
                </h3>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-4">
            <div class="summary-card">
              <div
                class="summary-card-icon"
                style="background-color: #feddb3; color: var(--niru-secondary)"
              >
                <i class="bi bi-truck"></i>
              </div>
              <div>
                <div class="small text-muted mb-1">In Transit</div>
                <h3
                  class="fs-4 fw-semibold mb-0"
                  style="color: var(--niru-secondary)"
                >
                  <?php echo str_pad($in_transit, 2, '0', STR_PAD_LEFT); ?>
                </h3>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-4">
            <div class="summary-card">
              <div
                class="summary-card-icon"
                style="background-color: #e4e4cc; color: #303221"
              >
                <i class="bi bi-credit-card"></i>
              </div>
              <div>
                <div class="small text-muted mb-1">Total Spent</div>
                <h3 class="fs-4 fw-semibold mb-0" style="color: #303221">
                  Rs. <?php echo number_format($total_spent, 2); ?>
                </h3>
              </div>
            </div>
          </div>
        </div>

        <!-- Orders Table -->
        <div class="orders-table-card mb-5">
          <div class="table-responsive">
            <table class="table table-orders align-middle mb-0">
              <thead>
                <tr>
                  <th>ORDER ID</th>
                  <th>DATE</th>
                  <th>STATUS</th>
                  <th>TOTAL AMOUNT</th>
                  <th class="text-end">ACTIONS</th>
                </tr>
              </thead>
              <tbody id="ordersTableBody">
                <?php 
                if ($orders_count > 0) {
                  while ($order = $orders_rs->fetch_assoc()) { 
                    $order_date = date("M d, Y", strtotime($order['placed_at']));
                    $status_name = $order['status_name'] ?? 'Pending Dispatch';

                    // Assign styling based on status
                    $badge_class = 'status-shipped';
                    if (stripos($status_name, 'Delivered') !== false) {
                      $badge_class = 'status-delivered';
                    } else if (stripos($status_name, 'Pending') !== false || stripos($status_name, 'Packed') !== false) {
                      $badge_class = 'status-processing';
                    } else if (stripos($status_name, 'Returned') !== false || stripos($status_name, 'Failed') !== false) {
                      $badge_class = 'status-cancelled';
                    }
                ?>
                  <tr class="order-row">
                    <td class="fw-semibold" style="color: #1b1c1c"><?php echo htmlspecialchars($order['order_number']); ?></td>
                    <td><?php echo $order_date; ?></td>
                    <td>
                      <span class="status-pill <?php echo $badge_class; ?>"><?php echo strtoupper(htmlspecialchars($status_name)); ?></span>
                    </td>
                    <td class="fw-semibold" style="color: #1b1c1c">Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                    <td class="text-end">
                      <a
                        href="invoice.php?id=<?php echo $order['order_id']; ?>"
                        class="fw-semibold text-decoration-none"
                        style="color: var(--niru-primary)"
                      >
                        View Details <i class="bi bi-arrow-right small ms-1"></i>
                      </a>
                    </td>
                  </tr>
                <?php 
                  }
                } else { 
                ?>
                  <tr id="emptyOrdersRow">
                    <td colspan="5" class="text-center py-5 text-muted">
                      <i class="bi bi-box-seam display-6 d-block mb-2 text-secondary"></i>
                      No orders placed yet.
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

          <!-- Bottom Counter -->
          <div class="d-flex align-items-center justify-content-between px-4 py-3 bg-light border-top">
            <small id="orderCountLabel" class="text-muted">Showing <?php echo $orders_count; ?> of <?php echo $orders_count; ?> orders</small>
          </div>
        </div>

        <!-- Banner Card -->
        <div class="promo-card p-4 p-md-5 d-flex flex-column flex-md-row align-items-center justify-content-between text-center text-md-start">
          <div class="mb-4 mb-md-0">
            <h3 class="fs-4 fw-semibold mb-2 text-white">Upgrade your space?</h3>
            <p class="mb-4 text-white-50">
              Explore our new minimal collection and transform your rooms today.
            </p>
            <a href="../shop.php" class="btn btn-light rounded-pill px-4 py-2 fw-semibold">Browse Collection</a>
          </div>
          <div class="d-none d-md-block">
            <img
              src="../Images/mainproduct/whitechair (1).png"
              alt="NiRu Furniture"
              style="border-radius: 12px; width: 180px; height: 140px; object-fit: cover; background: #fff;"
            />
          </div>
        </div>
      </div>

      <footer>
        <div class="row g-4">
          <div class="col-12 col-lg-3">
            <h3 class="fs-4 fw-bold mb-3" style="color: var(--niru-primary)">NiRu</h3>
            <p class="mb-0" style="color: var(--niru-body-text)">
              Crafting artisanal furniture that combines Scandinavian functionality with domestic warmth.
            </p>
          </div>
          <div class="col-6 col-md-3">
            <div class="footer-heading">Company</div>
            <ul class="footer-links list-unstyled">
              <li><a href="../about.php" class="text-decoration-none text-muted">About Us</a></li>
              <li><a href="../shop.php" class="text-decoration-none text-muted">Shop Catalog</a></li>
              <li><a href="../faq.php" class="text-decoration-none text-muted">FAQ</a></li>
            </ul>
          </div>
          <div class="col-6 col-md-3">
            <div class="footer-heading">Support</div>
            <ul class="footer-links list-unstyled">
              <li><a href="../faq.php" class="text-decoration-none text-muted">Shipping Info</a></li>
              <li><a href="../contact.php" class="text-decoration-none text-muted">Returns</a></li>
              <li><a href="../contact.php" class="text-decoration-none text-muted">Contact Us</a></li>
            </ul>
          </div>
          <div class="col-12 col-md-3">
            <div class="footer-heading">Contact</div>
            <p class="mb-2" style="color: var(--niru-body-text)">nirufurni@gmail.com</p>
            <p class="mb-0" style="color: var(--niru-body-text)">076 4209970</p>
          </div>
        </div>
      </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/script.js"></script>
  </body>
</html>