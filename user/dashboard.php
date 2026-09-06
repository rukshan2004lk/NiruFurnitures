<?php
session_start();
require_once '../connection.php';

// Check if user is logged in
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

// 1. Stat Card: Total Orders
$total_orders_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `orders` WHERE `user_id` = '$user_id'");
$total_orders = $total_orders_rs->fetch_assoc()['cnt'] ?? 0;

// 2. Stat Card: Total Amount Spent (Paid orders)
$total_spent_rs = Database::search("SELECT SUM(`total_amount`) AS `total` FROM `orders` WHERE `user_id` = '$user_id' AND `payment_status` = 'paid'");
$total_spent = (float)($total_spent_rs->fetch_assoc()['total'] ?? 0.00);

// 3. Stat Card: Saved Items (Wishlist)
$saved_items_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `wishlists` WHERE `user_id` = '$user_id'");
$saved_items = $saved_items_rs->fetch_assoc()['cnt'] ?? 0;

// 4. Stat Card: Active Cart Items Count
$cart_items_rs = Database::search("SELECT SUM(ci.quantity) AS `cnt` 
                                  FROM `cart_items` ci 
                                  INNER JOIN `carts` c ON ci.cart_id = c.cart_id 
                                  WHERE c.user_id = '$user_id'");
$cart_count = $cart_items_rs->fetch_assoc()['cnt'] ?? 0;

// 5. Recent Orders (Top 5)
$orders_query = "SELECT o.*, s.status_name 
                 FROM `orders` o 
                 LEFT JOIN `status` s ON o.order_status_id = s.status_id 
                 WHERE o.user_id = '$user_id' 
                 ORDER BY o.placed_at DESC LIMIT 5";
$recent_orders_rs = Database::search($orders_query);

// 6. Wishlist Items (Top 3 for dashboard sidebar widget)
$wishlist_query = "SELECT w.wishlist_id, p.product_id, p.name, p.price, c.name AS category_name,
                          (SELECT image_path FROM product_images WHERE product_id = p.product_id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS image_path 
                   FROM `wishlists` w 
                   INNER JOIN `products` p ON w.product_id = p.product_id 
                   LEFT JOIN `categories` c ON p.category_id = c.category_id 
                   WHERE w.user_id = '$user_id' 
                   ORDER BY w.created_at DESC LIMIT 3";
$wishlist_rs = Database::search($wishlist_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NiRu Furnitures - User Dashboard</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

  <!-- Mobile Topbar -->
  <div class="mobile-topbar">
    <a href="../index.php" class="brand-logo text-decoration-none">NiRu</a>
    <button class="btn btn-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#userMobileSidebar" aria-controls="userMobileSidebar">
      <i class="bi bi-list fs-5"></i> Menu
    </button>
  </div>

  <!-- Offcanvas Mobile Menu -->
  <div class="offcanvas offcanvas-start bg-light" tabindex="-1" id="userMobileSidebar" aria-labelledby="userMobileSidebarLabel">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title fw-bold" id="userMobileSidebarLabel">User Menu</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
      <ul class="nav flex-column gap-2 mb-4">
        <li class="nav-item"><a href="dashboard.php" class="nav-link active fw-bold text-dark"><i class="bi bi-grid me-2"></i>Dashboard</a></li>
        <li class="nav-item"><a href="orders.php" class="nav-link text-muted"><i class="bi bi-bag me-2"></i>My Orders</a></li>
        <li class="nav-item"><a href="wishlist.php" class="nav-link text-muted"><i class="bi bi-heart me-2"></i>Saved Wishlist</a></li>
        <li class="nav-item"><a href="settings.php" class="nav-link text-muted"><i class="bi bi-gear me-2"></i>Account Settings</a></li>
        <li class="nav-item"><hr class="dropdown-divider"></li>
        <li class="nav-item"><a href="../index.php" class="nav-link text-primary"><i class="bi bi-arrow-left me-2"></i>Back to Storefront</a></li>
        <li class="nav-item"><a href="../logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a></li>
      </ul>
    </div>
  </div>

  <!-- Desktop Sidebar -->
  <aside class="dashboard-sidebar">
    <a href="../index.php" class="sidebar-brand">NiRu</a>

    <ul class="sidebar-menu mb-5">
      <li>
        <a href="dashboard.php" class="sidebar-link active">
          <i class="bi bi-grid"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="orders.php" class="sidebar-link">
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

  <!-- Main Content -->
  <main class="dashboard-main">
    
    <div class="top-search-bar">
      <div>
        <h1 class="fs-2 fw-bold mb-1" style="color: var(--niru-primary);">Dashboard</h1>
        <p class="text-muted mb-0">Welcome back, <?php echo $first_name; ?>! Here is your account overview.</p>
      </div>

      <div class="d-flex align-items-center gap-3">
        <!-- Live Instant Filter for Recent Orders Table -->
        <div class="search-wrapper-relative">
          <i class="bi bi-search"></i>
          <input type="text" id="orderSearchInput" onkeyup="filterRecentOrders();" class="form-control search-pill-input" placeholder="Search orders, items...">
        </div>
        <a href="../cart.php" class="btn btn-outline-secondary rounded-circle p-2 position-relative" aria-label="Cart">
          <i class="bi bi-bag"></i>
          <?php if ($cart_count > 0) { ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px;"><?php echo $cart_count; ?></span>
          <?php } ?>
        </a>
      </div>
    </div>

    <!-- Stats Bento Cards -->
    <div class="row g-4 mb-5">
      
      <!-- Stat 1: Total Orders -->
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-bento-card">
          <div>
            <div class="stat-label">TOTAL ORDERS</div>
            <h3 class="fs-3 fw-bold mb-0" style="color: var(--niru-primary);"><?php echo str_pad($total_orders, 2, '0', STR_PAD_LEFT); ?></h3>
          </div>
          <div class="stat-icon-wrapper" style="background-color: #ffdbd0; color: var(--niru-primary);">
            <i class="bi bi-box-seam"></i>
          </div>
        </div>
      </div>

      <!-- Stat 2: Total Spent -->
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-bento-card">
          <div>
            <div class="stat-label">TOTAL SPENT</div>
            <h3 class="fs-3 fw-bold mb-0" style="color: var(--niru-secondary);">Rs. <?php echo number_format($total_spent, 2); ?></h3>
          </div>
          <div class="stat-icon-wrapper" style="background-color: #feddb3; color: var(--niru-secondary);">
            <i class="bi bi-cash-stack"></i>
          </div>
        </div>
      </div>

      <!-- Stat 3: Saved Items -->
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-bento-card">
          <div>
            <div class="stat-label">SAVED ITEMS</div>
            <h3 class="fs-3 fw-bold mb-0" style="color: #303221;"><?php echo str_pad($saved_items, 2, '0', STR_PAD_LEFT); ?></h3>
          </div>
          <div class="stat-icon-wrapper" style="background-color: #e4e4cc; color: #303221;">
            <i class="bi bi-bookmark-heart"></i>
          </div>
        </div>
      </div>

      <!-- Stat 4: Cart Items -->
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-bento-card">
          <div>
            <div class="stat-label">ITEMS IN CART</div>
            <h3 class="fs-3 fw-bold mb-0" style="color: var(--niru-primary);"><?php echo str_pad($cart_count, 2, '0', STR_PAD_LEFT); ?></h3>
          </div>
          <div class="stat-icon-wrapper" style="background-color: #ffdbd0; color: var(--niru-primary);">
            <i class="bi bi-cart3"></i>
          </div>
        </div>
      </div>

    </div>

    <!-- Orders and Wishlist Section -->
    <div class="row g-4">
      
      <!-- Recent Orders Table -->
      <div class="col-12 col-lg-8">
        <div class="dashboard-card">
          <div class="card-header-custom">
            <h2 class="fs-4 fw-semibold mb-0" style="color: var(--niru-primary);">Recent Orders</h2>
            <a href="orders.php" class="fw-bold text-decoration-none small" style="color: var(--niru-primary); letter-spacing: 0.7px;">View All</a>
          </div>

          <div class="table-responsive">
            <table class="table table-custom align-middle">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Total</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="recentOrdersTableBody">
                <?php 
                if ($recent_orders_rs->num_rows > 0) {
                  while ($order = $recent_orders_rs->fetch_assoc()) { 
                    $order_date = date("M d, Y", strtotime($order['placed_at']));
                    $status_name = $order['status_name'] ?? 'Pending';
                    
                    // Assign status badge styling
                    $badge_class = 'status-shipped';
                    if (stripos($status_name, 'Delivered') !== false) {
                      $badge_class = 'status-delivered';
                    } else if (stripos($status_name, 'Failed') !== false || stripos($status_name, 'Returned') !== false) {
                      $badge_class = 'status-cancelled';
                    }
                ?>
                  <tr>
                    <td class="fw-bold" style="color: var(--niru-primary);"><?php echo htmlspecialchars($order['order_number']); ?></td>
                    <td><?php echo $order_date; ?></td>
                    <td><span class="status-badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($status_name); ?></span></td>
                    <td class="fw-semibold">Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                    <td>
                      <a href="invoice.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-link p-0 text-dark" title="View Invoice">
                        <i class="bi bi-eye"></i>
                      </a>
                    </td>
                  </tr>
                <?php 
                  }
                } else { 
                ?>
                  <tr id="noOrdersRow">
                    <td colspan="5" class="text-center py-4 text-muted">No orders found yet. Start shopping!</td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Quick Wishlist Widget -->
      <div class="col-12 col-lg-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h2 class="fs-4 fw-semibold mb-0" style="color: var(--niru-primary);">Wishlist</h2>
          <a href="wishlist.php" class="fw-bold text-decoration-none small text-muted" style="letter-spacing: 1.2px;">EXPLORE ALL</a>
        </div>

        <div class="d-flex flex-column gap-3">
          <?php 
          if ($wishlist_rs->num_rows > 0) {
            while ($wish = $wishlist_rs->fetch_assoc()) { 
              $thumb = !empty($wish['image_path']) ? '../' . $wish['image_path'] : '../Images/products/nordic_lounge.png';
          ?>
            <div class="wishlist-item-card">
              <img src="<?php echo htmlspecialchars($thumb); ?>" alt="<?php echo htmlspecialchars($wish['name']); ?>" class="wishlist-thumb" style="object-fit: cover; width: 65px; height: 65px; border-radius: 8px;">
              <div class="flex-grow-1">
                <h3 class="fs-6 fw-normal mb-0" style="color: #1b1c1c;">
                  <a href="../product-detail.php?id=<?php echo $wish['product_id']; ?>" class="text-decoration-none text-dark">
                    <?php echo htmlspecialchars($wish['name']); ?>
                  </a>
                </h3>
                <small class="text-muted d-block mb-1"><?php echo htmlspecialchars($wish['category_name'] ?? 'Furniture'); ?></small>
                <div class="fw-bold" style="color: var(--niru-primary);">Rs. <?php echo number_format($wish['price'], 2); ?></div>
              </div>
              <button class="btn btn-sm text-muted p-2" onclick="removeWishlistItem(<?php echo $wish['wishlist_id']; ?>)" aria-label="Remove item">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          <?php 
            }
          } else { 
          ?>
            <div class="p-4 border rounded-3 text-center text-muted">
              <i class="bi bi-heart text-secondary fs-4 d-block mb-2"></i>
              Your wishlist is empty.
            </div>
          <?php } ?>
        </div>
      </div>

    </div>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/script.js"></script>
</body>
</html>