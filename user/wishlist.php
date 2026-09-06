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
$last_name  = htmlspecialchars($user['last_name'] ?? '');
$email      = htmlspecialchars($user['email'] ?? '');
$initials   = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));

// Fetch Wishlist Items joined with Products and Categories
$wishlist_query = "SELECT w.wishlist_id, p.product_id, p.name, p.price, p.short_description, c.name AS category_name,
                          (SELECT image_path FROM product_images WHERE product_id = p.product_id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS image_path 
                   FROM `wishlists` w 
                   INNER JOIN `products` p ON w.product_id = p.product_id 
                   LEFT JOIN `categories` c ON p.category_id = c.category_id 
                   WHERE w.user_id = '$user_id' 
                   ORDER BY w.created_at DESC";
$wishlist_rs = Database::search($wishlist_query);
$wishlist_count = $wishlist_rs->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NiRu Furnitures - Your Collection (Wishlist)</title>
  
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

  <!-- Offcanvas Mobile Sidebar -->
  <div class="offcanvas offcanvas-start bg-light" tabindex="-1" id="userMobileSidebar" aria-labelledby="userMobileSidebarLabel">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title fw-bold" id="userMobileSidebarLabel">User Menu</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
      <ul class="nav flex-column gap-2 mb-4">
        <li class="nav-item"><a href="dashboard.php" class="nav-link text-muted"><i class="bi bi-grid me-2"></i>Dashboard</a></li>
        <li class="nav-item"><a href="orders.php" class="nav-link text-muted"><i class="bi bi-bag me-2"></i>My Orders</a></li>
        <li class="nav-item"><a href="wishlist.php" class="nav-link active fw-bold text-dark"><i class="bi bi-heart me-2"></i>Saved Wishlist</a></li>
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
        <a href="dashboard.php" class="sidebar-link">
          <i class="bi bi-grid"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="orders.php" class="sidebar-link">
          <i class="bi bi-bag"></i> My Orders
        </a>
      </li>
      <li>
        <a href="wishlist.php" class="sidebar-link active">
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
    
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-4 mb-5">
      <div>
        <nav aria-label="breadcrumb" class="mb-2">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="../shop.php" class="text-decoration-none text-muted">Store</a></li>
            <li class="breadcrumb-item active fw-bold" style="color: var(--niru-primary);">Wishlist</li>
          </ol>
        </nav>
        <h1 class="display-4 fw-bold mb-2" style="color: var(--niru-primary); line-height: 1.1;">
          Your Collection
        </h1>
        <p class="fs-5 text-muted mb-0" style="max-width: 620px;">
          A curated selection of your favorite pieces. Each item in your wishlist represents a step towards a more serene and intentional home.
        </p>
      </div>

      <div class="d-flex align-items-center gap-3">
        <?php if ($wishlist_count > 0) { ?>
          <button class="btn btn-outline-secondary rounded-pill px-3 py-2" onclick="shareWishlist();">
            <i class="bi bi-share me-1"></i> Share List
          </button>
          <button class="btn btn-niru-primary rounded-pill px-3 py-2" onclick="addAllToCart();">
            <i class="bi bi-bag-plus me-1"></i> Add All to Cart
          </button>
        <?php } ?>
      </div>
    </div>

    <div class="row g-4 mb-5">
      
      <?php 
      if ($wishlist_count > 0) {
        while ($item = $wishlist_rs->fetch_assoc()) { 
          $thumb = !empty($item['image_path']) ? '../' . $item['image_path'] : '../Images/products/nordic_lounge.png';
      ?>
        <div class="col-12 col-md-6 col-xl-4 wishlist-card-item" data-product-id="<?php echo $item['product_id']; ?>">
          <div class="collection-card h-100 d-flex flex-column">
            <div class="collection-img-box position-relative">
              <img src="<?php echo htmlspecialchars($thumb); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 100%; height: 260px; object-fit: cover;">
              <button class="remove-wishlist-btn" onclick="removeFromWishlist(<?php echo $item['wishlist_id']; ?>)" aria-label="Remove item">
                <i class="bi bi-heart-fill text-danger"></i>
              </button>
            </div>
            <div class="p-4 d-flex flex-column justify-content-between flex-grow-1">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div>
                    <div class="category-micro-tag mb-1 text-uppercase text-muted small fw-bold">
                      <?php echo htmlspecialchars($item['category_name'] ?? 'FURNITURE'); ?>
                    </div>
                    <h3 class="fs-5 fw-semibold mb-0" style="color: var(--niru-primary);">
                      <a href="../product-detail.php?id=<?php echo $item['product_id']; ?>" class="text-decoration-none text-dark">
                        <?php echo htmlspecialchars($item['name']); ?>
                      </a>
                    </h3>
                  </div>
                  <span class="fs-5 fw-semibold" style="color: var(--niru-primary);">Rs. <?php echo number_format($item['price'], 2); ?></span>
                </div>
                <p class="small text-muted mb-4">
                  <?php echo htmlspecialchars($item['short_description'] ?? 'Sculptural elegance meets ergonomic comfort.'); ?>
                </p>
              </div>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-niru-primary flex-grow-1 py-2" onclick="addSingleToCart(<?php echo $item['product_id']; ?>);">
                  Add to Cart
                </button>
                <button class="btn btn-outline-danger px-3 py-2" onclick="removeFromWishlist(<?php echo $item['wishlist_id']; ?>);" title="Remove">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      <?php 
        }
      } else { 
      ?>
        <div class="col-12 text-center py-5">
          <div class="card border-0 shadow-sm rounded-4 p-5">
            <i class="bi bi-heartbreak display-3 text-muted mb-3"></i>
            <h4 class="fw-bold">Your Wishlist is Empty</h4>
            <p class="text-muted">You haven't saved any items yet. Browse our catalog and discover minimal furniture pieces.</p>
            <div class="mt-3">
              <a href="../shop.php" class="btn btn-niru-primary px-4 py-2 rounded-3">Explore Catalog</a>
            </div>
          </div>
        </div>
      <?php } ?>

      <!-- Discover More Card -->
      <div class="col-12 col-md-6 col-xl-4">
        <div class="discover-card h-100 d-flex flex-column justify-content-center align-items-start p-4 rounded-4" style="background-color: var(--niru-bg-alt); min-height: 380px;">
          <i class="bi bi-compass fs-1 mb-3" style="color: var(--niru-secondary);"></i>
          <h3 class="fs-3 fw-semibold mb-2" style="color: var(--niru-primary);">Discover More</h3>
          <p class="text-muted mb-4">Looking for something else to complete your interior layout?</p>
          <a href="../shop.php" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-semibold">
            Explore New Arrivals <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>
      </div>

    </div>

  </main>

  <footer>
    <div class="container-xl">
      <div class="row g-4 border-top pt-4">
        <div class="col-12 col-lg-4">
          <h3 class="fs-4 fw-bold mb-3" style="color: var(--niru-primary);">NiRu</h3>
          <p class="small mb-0" style="color: var(--niru-body-text);">
            Creating timeless furniture that honors the beauty of natural materials and simple living.
          </p>
        </div>

        <div class="col-6 col-md-4 col-lg-4">
          <div class="footer-heading fw-bold mb-2">COMPANY</div>
          <ul class="footer-links list-unstyled">
            <li><a href="../about.php" class="text-decoration-none text-muted small">About Us</a></li>
            <li><a href="../shop.php" class="text-decoration-none text-muted small">Sustainability</a></li>
          </ul>
        </div>

        <div class="col-6 col-md-4 col-lg-4">
          <div class="footer-heading fw-bold mb-2">SUPPORT</div>
          <ul class="footer-links list-unstyled">
            <li><a href="../faq.php" class="text-decoration-none text-muted small">Privacy Policy</a></li>
            <li><a href="../faq.php" class="text-decoration-none text-muted small">Shipping Info</a></li>
          </ul>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/script.js"></script>
</body>
</html>