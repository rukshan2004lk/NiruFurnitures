<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connection.php';

if (!isset($_SESSION['a']) || ((int)($_SESSION['a']['role_id'] ?? 0) !== 1)) {
    header("Location: ../login.php");
    exit();
}

$user_id = (int)($_SESSION['a']['user_id'] ?? 0);

// Fetch fresh details from database
$user_rs = Database::search("SELECT * FROM `user` WHERE `user_id` = '$user_id'");
$admin = $user_rs->fetch_assoc();

$first_name = htmlspecialchars($admin['first_name'] ?? '');
$last_name  = htmlspecialchars($admin['last_name'] ?? '');
$email      = htmlspecialchars($admin['email'] ?? '');
$phone      = htmlspecialchars($admin['phone'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NiRu Furnitures - Admin Settings</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

  <!-- Mobile Topbar -->
  <div class="admin-mobile-topbar">
    <div class="fw-bold fs-5 text-white">NiRu Admin</div>
    <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileSidebar" aria-controls="adminMobileSidebar">
      <i class="bi bi-list fs-5"></i> Menu
    </button>
  </div>

  <!-- Mobile Offcanvas Menu -->
  <div class="offcanvas offcanvas-start bg-dark text-light" tabindex="-1" id="adminMobileSidebar" aria-labelledby="adminMobileSidebarLabel">
    <div class="offcanvas-header border-bottom border-secondary">
      <h5 class="offcanvas-title text-white fw-bold" id="adminMobileSidebarLabel">Admin Navigation</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
      <ul class="nav flex-column gap-2 mb-4">
        <li class="nav-item"><a href="admin-dashboard.php" class="nav-link text-white-50"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
        <li class="nav-item"><a href="admin-products.php" class="nav-link text-white-50"><i class="bi bi-box-seam me-2"></i>Products</a></li>
        <li class="nav-item"><a href="admin-orders.php" class="nav-link text-white-50"><i class="bi bi-cart me-2"></i>Orders</a></li>
        <li class="nav-item"><a href="admin-users.php" class="nav-link text-white-50"><i class="bi bi-people me-2"></i>Users</a></li>
        <li class="nav-item"><a href="admin-settings.php" class="nav-link active fw-bold text-white"><i class="bi bi-gear me-2"></i>Settings</a></li>
        <li class="nav-item"><hr class="dropdown-divider border-secondary"></li>
        <li class="nav-item"><a href="../index.php" class="admin-link-store"><i class="bi bi-shop me-2"></i>Exit to Storefront</a></li>
        <li class="nav-item"><a href="../logout.php" class="admin-link-logout"><i class="bi bi-power me-2"></i>Log Out</a></li>
      </ul>
    </div>
  </div>

  <!-- Desktop Sidebar -->
  <aside class="admin-sidebar">
    <div class="admin-brand">
      <h2>Admin Panel</h2>
      <small>Manage NiRu Furnitures</small>
    </div>

    <ul class="admin-menu">
      <li><a href="admin-dashboard.php" class="admin-link"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
      <li><a href="admin-products.php" class="admin-link"><i class="bi bi-box-seam"></i> Products</a></li>
      <li><a href="admin-orders.php" class="admin-link"><i class="bi bi-cart"></i> Orders</a></li>
      <li><a href="admin-users.php" class="admin-link"><i class="bi bi-people"></i> Users</a></li>
      <li><a href="admin-settings.php" class="admin-link active"><i class="bi bi-gear"></i> Settings</a></li>
    </ul>

    <div class="px-3 mt-auto d-flex flex-column gap-2 mb-3">
      <a href="../index.php" class="admin-link-store"><i class="bi bi-shop"></i> View Store</a>
      <a href="../logout.php" class="admin-link-logout"><i class="bi bi-box-arrow-left"></i> Log Out</a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="admin-main">
    
    <div class="top-header-glass">
      <div>
        <h1 class="fs-4 fw-semibold mb-0" style="color: var(--niru-primary);">Settings</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="admin-dashboard.php" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item active fw-semibold" style="color: #1b1c1c;">Account Settings</li>
          </ol>
        </nav>
      </div>
    </div>

    <div class="admin-content-canvas">
      
      <!-- 1. Account Information Card -->
      <div class="admin-stat-card mb-4 h-auto p-4 bg-white rounded-4 border shadow-sm">
        <h2 class="fs-4 fw-semibold mb-3" style="color: var(--niru-primary);">Account Information</h2>
        
        <form id="adminAccountForm" onsubmit="event.preventDefault(); submitAdminAccountUpdate();">
          <div class="row g-4 mb-4">
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">First Name</label>
              <input type="text" name="first_name" class="form-control" value="<?php echo $first_name; ?>" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Last Name</label>
              <input type="text" name="last_name" class="form-control" value="<?php echo $last_name; ?>">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Email Address (Read-only)</label>
              <input type="email" class="form-control bg-light" value="<?php echo $email; ?>" readonly>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Phone Number</label>
              <input type="text" name="phone" class="form-control" value="<?php echo $phone; ?>" placeholder="07X XXXXXXX">
            </div>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-dark px-4 py-2 fw-semibold" style="background-color: var(--niru-primary); border-radius: 8px;">
              Update Account
            </button>
          </div>
        </form>
      </div>

      <!-- 2. Change Password Card -->
      <div class="admin-stat-card mb-4 h-auto p-4 bg-white rounded-4 border shadow-sm">
        <div class="d-flex align-items-center gap-2 mb-3">
          <i class="bi bi-shield-lock fs-4" style="color: var(--niru-primary);"></i>
          <h2 class="fs-4 fw-semibold mb-0" style="color: var(--niru-primary);">Change Password</h2>
        </div>

        <form id="adminPasswordForm" onsubmit="event.preventDefault(); submitAdminPasswordChange();">
          <div class="mb-4">
            <label class="form-label fw-semibold">Current Password</label>
            <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>
          </div>

          <div class="row g-4 mb-4">
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">New Password</label>
              <input type="password" name="new_password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Confirm New Password</label>
              <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
            </div>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-dark px-4 py-2 fw-semibold" style="background-color: var(--niru-primary); border-radius: 8px;">
              Save Password
            </button>
          </div>
        </form>
      </div>

    </div>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/script.js"></script>
</body>
</html>