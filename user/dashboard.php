<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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

$saved_items_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `wishlists` WHERE `user_id` = '$user_id'");
$saved_items = (int)($saved_items_rs->fetch_assoc()['cnt'] ?? 0);

$orders_query = "SELECT o.*, s.status_name 
                 FROM `orders` o 
                 LEFT JOIN `status` s ON o.order_status_id = s.status_id 
                 WHERE o.user_id = '$user_id' 
                 ORDER BY o.placed_at DESC LIMIT 5";
$recent_orders_rs = Database::search($orders_query);
$recent_orders_count = $recent_orders_rs ? $recent_orders_rs->num_rows : 0;

$wishlist_query = "SELECT w.wishlist_id, p.product_id, p.name, p.price, c.name AS category_name,
                          (SELECT image_path FROM product_images WHERE product_id = p.product_id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS image_path 
                   FROM `wishlists` w 
                   INNER JOIN `products` p ON w.product_id = p.product_id 
                   LEFT JOIN `categories` c ON p.category_id = c.category_id 
                   WHERE w.user_id = '$user_id' 
                   ORDER BY w.created_at DESC LIMIT 4";
$wishlist_rs = Database::search($wishlist_query);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NiRu Furnitures - User Dashboard</title>

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
    <link
      href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"
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
            <a href="dashboard.php" class="nav-link active fw-bold text-dark"><i class="bi bi-grid me-2"></i>Dashboard</a>
          </li>
          <li class="nav-item">
            <a href="orders.php" class="nav-link text-muted"><i class="bi bi-bag me-2"></i>My Orders</a>
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

    <!-- Main Container -->
    <main class="orders-main">
      <div class="orders-container">

        <!-- Top Header Bar -->
        <div class="top-header-bar d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
          <div>
            <h1 class="fs-3 fw-semibold mb-1" style="color: var(--niru-primary)">
              Welcome back, <?php echo $first_name; ?>!
            </h1>
            <p class="text-muted small mb-0">Here is an overview of your activity, recent orders, and wishlist.</p>
          </div>

          <div class="d-flex align-items-center gap-2">
            <a href="../shop.php" class="btn btn-sm btn-outline-secondary px-3 py-2 fw-semibold">
              <i class="bi bi-bag-plus me-1"></i> Browse Store
            </a>
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-5">
          <div class="col-12 col-sm-6 col-xl-3">
            <div class="summary-card">
              <div
                class="summary-card-icon"
                style="background-color: #ffdbd0; color: var(--niru-primary)"
              >
                <i class="bi bi-box-seam"></i>
              </div>
              <div>
                <div class="small text-muted mb-1">Total Orders</div>
                <h3 class="fs-4 fw-semibold mb-0" style="color: var(--niru-primary)">
                  <?php echo str_pad($total_orders, 2, '0', STR_PAD_LEFT); ?>
                </h3>
              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-xl-3">
            <div class="summary-card">
              <div
                class="summary-card-icon"
                style="background-color: #feddb3; color: var(--niru-secondary)"
              >
                <i class="bi bi-truck"></i>
              </div>
              <div>
                <div class="small text-muted mb-1">In Transit</div>
                <h3 class="fs-4 fw-semibold mb-0" style="color: var(--niru-secondary)">
                  <?php echo str_pad($in_transit, 2, '0', STR_PAD_LEFT); ?>
                </h3>
              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-xl-3">
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

          <div class="col-12 col-sm-6 col-xl-3">
            <div class="summary-card">
              <div
                class="summary-card-icon"
                style="background-color: #fde8e8; color: #dc3545"
              >
                <i class="bi bi-heart"></i>
              </div>
              <div>
                <div class="small text-muted mb-1">Saved Wishlist</div>
                <h3 class="fs-4 fw-semibold mb-0" style="color: #dc3545">
                  <?php echo str_pad($saved_items, 2, '0', STR_PAD_LEFT); ?>
                </h3>
              </div>
            </div>
          </div>
        </div>

        <!-- Content Grid: Recent Orders & Wishlist -->
        <div class="row g-4 mb-5">
          <!-- Recent Orders Table -->
          <div class="col-12 col-xl-8">
            <div class="orders-table-card h-100">
              <div class="d-flex align-items-center justify-content-between p-4 border-bottom">
                <h2 class="fs-5 fw-semibold mb-0" style="color: var(--niru-primary)">Recent Orders</h2>
                <a href="orders.php" class="text-decoration-none small fw-semibold" style="color: var(--niru-primary)">
                  View All Orders <i class="bi bi-arrow-right small ms-1"></i>
                </a>
              </div>

              <div class="table-responsive">
                <table class="table table-orders align-middle mb-0">
                  <thead>
                    <tr>
                      <th>ORDER ID</th>
                      <th>DATE</th>
                      <th>STATUS</th>
                      <th>TOTAL</th>
                      <th class="text-end">ACTION</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    if ($recent_orders_count > 0) {
                      while ($order = $recent_orders_rs->fetch_assoc()) { 
                        $order_date = date("M d, Y", strtotime($order['placed_at']));
                        $status_name = $order['status_name'] ?? 'Pending Dispatch';

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
                        <td class="fw-semibold" style="color: #1b1c1c">#<?php echo htmlspecialchars($order['order_number'] ?? $order['order_id']); ?></td>
                        <td><?php echo $order_date; ?></td>
                        <td>
                          <span class="status-pill <?php echo $badge_class; ?>">
                            <?php echo strtoupper(htmlspecialchars($status_name)); ?>
                          </span>
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
                      <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                          <i class="bi bi-box-seam display-6 d-block mb-2 text-secondary"></i>
                          No recent orders found.
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

              <div class="d-flex align-items-center justify-content-between px-4 py-3 bg-light border-top">
                <small class="text-muted">Showing <?php echo $recent_orders_count; ?> recent order(s)</small>
                <a href="orders.php" class="small text-decoration-none text-muted">Manage full history &rarr;</a>
              </div>
            </div>
          </div>

          <!-- Saved Wishlist Panel -->
          <div class="col-12 col-xl-4">
            <div class="orders-table-card h-100 d-flex flex-column">
              <div class="d-flex align-items-center justify-content-between p-4 border-bottom">
                <h2 class="fs-5 fw-semibold mb-0" style="color: var(--niru-primary)">Saved Wishlist</h2>
                <a href="wishlist.php" class="text-decoration-none small fw-semibold" style="color: var(--niru-primary)">
                  View All <i class="bi bi-arrow-right small ms-1"></i>
                </a>
              </div>

              <div class="p-4 flex-grow-1 d-flex flex-column gap-3">
                <?php 
                if ($wishlist_rs && $wishlist_rs->num_rows > 0) {
                  while ($wish = $wishlist_rs->fetch_assoc()) { 
                    $thumb = !empty($wish['image_path']) ? '../' . $wish['image_path'] : '../Images/products/nordic_lounge.png';
                ?>
                  <div class="d-flex align-items-center gap-3 p-2 rounded-3 border bg-white">
                    <img 
                      src="<?php echo htmlspecialchars($thumb); ?>" 
                      alt="<?php echo htmlspecialchars($wish['name']); ?>" 
                      style="object-fit: cover; width: 56px; height: 56px; border-radius: 8px; flex-shrink: 0;"
                    />
                    <div class="flex-grow-1 overflow-hidden">
                      <h3 class="fs-6 fw-semibold mb-0 text-truncate">
                        <a href="../product-detail.php?id=<?php echo $wish['product_id']; ?>" class="text-decoration-none" style="color: #1b1c1c">
                          <?php echo htmlspecialchars($wish['name']); ?>
                        </a>
                      </h3>
                      <small class="text-muted d-block text-truncate"><?php echo htmlspecialchars($wish['category_name'] ?? 'Furniture'); ?></small>
                      <div class="fw-semibold small" style="color: var(--niru-primary)">
                        Rs. <?php echo number_format($wish['price'], 2); ?>
                      </div>
                    </div>
                    <button class="btn btn-sm btn-light border-0 text-muted p-2" onclick="removeWishlistItem(<?php echo $wish['wishlist_id']; ?>)" aria-label="Remove item">
                      <i class="bi bi-trash3"></i>
                    </button>
                  </div>
                <?php 
                  }
                } else { 
                ?>
                  <div class="text-center py-5 text-muted my-auto">
                    <i class="bi bi-heart display-6 d-block mb-2 text-secondary"></i>
                    Your wishlist is currently empty.
                  </div>
                <?php } ?>
              </div>

              <div class="px-4 py-3 bg-light border-top">
                <small class="text-muted">Quick access to items you have bookmarked.</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Banner Card -->
        <div class="promo-card p-4 p-md-5 d-flex flex-column flex-md-row align-items-center justify-content-between text-center text-md-start mb-5">
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

      <!-- Footer -->
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