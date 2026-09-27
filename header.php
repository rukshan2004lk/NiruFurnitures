<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($base_path)) {
    $base_path = file_exists('assets/css/style.css') ? '' : '../';
}

if (!isset($page_title)) {
    $page_title = "NiRu Furnitures - Stylish Furniture For Modern Living";
}

$cart_count = 0;
if (isset($_SESSION["u"])) {
    require_once __DIR__ . '/connection.php';
    $u_id = (int)($_SESSION["u"]["user_id"] ?? 0);
    if ($u_id > 0) {
        $c_rs = Database::search("SELECT SUM(ci.quantity) AS `cnt` 
                                  FROM `cart_items` ci 
                                  INNER JOIN `carts` c ON ci.cart_id = c.cart_id 
                                  WHERE c.user_id = '$u_id'");
        if ($c_rs && $c_row = $c_rs->fetch_assoc()) {
            $cart_count = (int)($c_row['cnt'] ?? 0);
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet" />
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
    
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css" />
</head>
<body>
    <header>
      <nav class="navbar navbar-expand-lg fixed-top px-3 px-lg-5">
        <div class="container-fluid max-w-1320">
          <a class="brand-logo me-4" href="<?php echo $base_path; ?>index.php">NiRu</a>

          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mx-auto text-center mb-2 mb-lg-0 gap-lg-4">
              <li class="nav-item">
                <a class="nav-link-custom <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>index.php">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link-custom <?php echo (basename($_SERVER['PHP_SELF']) == 'shop.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>shop.php">Shop</a>
              </li>
              <li class="nav-item">
                <a class="nav-link-custom <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>about.php">About Us</a>
              </li>
              <li class="nav-item">
                <a class="nav-link-custom <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>contact.php">Contact</a>
              </li>
              <li class="nav-item">
                <a class="nav-link-custom <?php echo (basename($_SERVER['PHP_SELF']) == 'faq.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>faq.php">FAQ</a>
              </li>
            </ul>

            <div class="d-flex align-items-center gap-3 justify-content-center mt-3 mt-lg-0">
              <a href="<?php echo $base_path; ?>cart.php" class="icon-btn text-decoration-none position-relative" aria-label="Cart">
                <i class="bi bi-bag"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;"><?php echo $cart_count; ?></span>
              </a>

              <div class="dropdown">
                <button class="icon-btn dropdown-toggle border-0 bg-transparent p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account">
                  <i class="bi bi-person"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3">
                  <?php if (isset($_SESSION["u"])) : ?>
                    <li class="px-3 py-2 border-bottom">
                      <span class="d-block fw-semibold small text-truncate"><?php echo htmlspecialchars($_SESSION["u"]["first_name"] . " " . $_SESSION["u"]["last_name"]); ?></span>
                      <span class="d-block text-muted text-truncate" style="font-size: 11px;"><?php echo htmlspecialchars($_SESSION["u"]["email"]); ?></span>
                    </li>
                    <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>user/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>My Dashboard</a></li>
                    <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>user/orders.php"><i class="bi bi-box-seam me-2"></i>My Orders</a></li>
                    <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>user/wishlist.php"><i class="bi bi-heart me-2"></i>Wishlist</a></li>
                    <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>user/settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                    <?php if (isset($_SESSION["a"]) || ((int)($_SESSION["u"]["role_id"] ?? 0) === 1)) : ?>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>admin/admin-dashboard.php"><i class="bi bi-shield-lock me-2"></i>Admin Panel</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 text-danger" href="<?php echo $base_path; ?>logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                  <?php else : ?>
                    <li><a class="dropdown-item py-2" href="<?php echo $base_path; ?>login.php"><i class="bi bi-box-arrow-in-right me-2"></i>Sign In / Register</a></li>
                  <?php endif; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </nav>
    </header>
