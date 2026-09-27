<?php
session_start();
require_once "../connection.php";
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NiRu Furnitures - User Settings</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap"
    rel="stylesheet" />

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet" />

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    rel="stylesheet" />

  <link rel="stylesheet" href="../assets/css/style.css" />
</head>

<body>

  <?php
  if (isset($_SESSION["u"])) {
    $email = $_SESSION["u"]["email"];

    $details_rs = Database::search("SELECT * FROM `user` WHERE `email`='" . $email . "'");
    $details_data = $details_rs->fetch_assoc();

  ?>
    <div class="mobile-topbar">
      <a href="../index.php" class="brand-logo text-decoration-none">NiRu</a>
      <button
        class="btn btn-dark"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#userMobileSidebar"
        aria-controls="userMobileSidebar">
        <i class="bi bi-list fs-5"></i> Menu
      </button>
    </div>

    <div
      class="offcanvas offcanvas-start bg-light"
      tabindex="-1"
      id="userMobileSidebar"
      aria-labelledby="userMobileSidebarLabel">
      <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold" id="userMobileSidebarLabel">
          User Menu
        </h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="offcanvas"
          aria-label="Close"></button>
      </div>
      <div class="offcanvas-body p-3">
        <ul class="nav flex-column gap-2 mb-4">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link text-muted"><i class="bi bi-grid me-2"></i>Dashboard</a>
          </li>
          <li class="nav-item">
            <a href="orders.php" class="nav-link text-muted"><i class="bi bi-bag me-2"></i>My Orders</a>
          </li>
          <li class="nav-item">
            <a href="wishlist.php" class="nav-link text-muted"><i class="bi bi-heart me-2"></i>Saved Wishlist</a>
          </li>
          <li class="nav-item">
            <a href="settings.php" class="nav-link active fw-bold text-dark"><i class="bi bi-gear me-2"></i>Account Settings</a>
          </li>
          <li class="nav-item">
            <hr class="dropdown-divider" />
          </li>
          <li class="nav-item">
            <a href="../index.php" class="nav-link text-primary"><i class="bi bi-arrow-left me-2"></i>Back to Storefront</a>
          </li>
          <li class="nav-item">
            <a href="../logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a>
          </li>
        </ul>
      </div>
    </div>

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
          <a href="wishlist.php" class="sidebar-link">
            <i class="bi bi-heart"></i> Wishlist
          </a>
        </li>
        <li>
          <a href="settings.php" class="sidebar-link active">
            <i class="bi bi-gear"></i> Settings
          </a>
        </li>
      </ul>

      <div class="user-profile-badge mt-auto">
        <div class="avatar-sm">
          <?= strtoupper(substr($details_data["first_name"] ?? "U", 0, 1) . substr($details_data["last_name"] ?? "", 0, 1)); ?>
        </div>
        <div class="overflow-hidden">
          <h4
            class="fs-6 fw-semibold mb-0 text-truncate"
            style="color: var(--niru-primary)">
            <?= htmlspecialchars($details_data["first_name"] . " " . $details_data["last_name"]); ?>
          </h4>
          <small
            class="text-muted d-block text-truncate"
            title="<?= htmlspecialchars($details_data['email']); ?>">
            <?= htmlspecialchars($details_data["email"]); ?>
          </small>
        </div>
      </div>

      <a href="../logout.php" class="btn btn-outline-danger btn-sm mt-3 m-2"><i class="bi bi-box-arrow-right me-1"></i> Log Out</a>
    </aside>

    <main class="dashboard-main">

      <div class="top-header-bar d-flex align-items-center justify-content-between mb-4">
        <h1 class="fs-3 fw-semibold mb-0" style="color: var(--niru-primary)">
          Settings
        </h1>
        <div class="d-flex align-items-center gap-3">
          <button class="btn btn-link text-dark p-0" aria-label="Notifications">
            <i class="bi bi-bell fs-5"></i>
          </button>
          <button
            class="btn btn-link text-dark p-0"
            aria-label="Settings Quick Link">
            <i class="bi bi-gear fs-5"></i>
          </button>
        </div>
      </div>

      <div class="settings-canvas">

        <div class="dashboard-card p-4 p-md-5 mb-4">
          <h2 class="fs-4 fw-semibold mb-4" style="color: var(--niru-primary)">
            Account Information
          </h2>

          <form onsubmit="event.preventDefault()">
            <div class="row g-4 mb-4">
              <div class="col-12 col-md-6">
                <label for="fName" class="form-label-custom">First Name</label>
                <input
                  type="text"
                  class="form-control form-control-custom"
                  id="fName"
                  value="<?php echo ($details_data['first_name'] ?? ''); ?>" />
              </div>

              <div class="col-12 col-md-6">
                <label for="lName" class="form-label-custom">Last Name</label>
                <input
                  type="text"
                  class="form-control form-control-custom"
                  id="lName"
                  value="<?php echo ($details_data['last_name'] ?? ''); ?>" />
              </div>

              <div class="col-12 col-md-6">
                <label for="phoneNumber" class="form-label-custom">Phone Number</label>
                <input
                  type="text"
                  class="form-control form-control-custom"
                  id="phoneNumber"
                  value="<?php echo ($details_data['phone'] ?? ''); ?>" />
              </div>

              <!-- Address Line 1 -->
              <div class="col-12 col-md-6">
                <label for="line1" class="form-label-custom">Address Line 01</label>
                <input
                  type="text"
                  class="form-control form-control-custom"
                  id="line1"
                  placeholder="e.g. No. 123, Main Street"
                  value="<?php echo ($details_data['line_1'] ?? ''); ?>" />
              </div>

              <!-- Address Line 2 -->
              <div class="col-12 col-md-6">
                <label for="line2" class="form-label-custom">Address Line 02 (Optional)</label>
                <input
                  type="text"
                  class="form-control form-control-custom"
                  id="line2"
                  placeholder="e.g. 2nd Lane, Apartment 4B"
                  value="<?php echo htmlspecialchars($details_data['line_2'] ?? $details_data['address_line2'] ?? ''); ?>" />
              </div>

              <!-- City -->
              <div class="col-12 col-md-6">
                <label for="city" class="form-label-custom">City</label>
                <input
                  type="text"
                  class="form-control form-control-custom"
                  id="city"
                  placeholder="e.g. Embilipitiya / Colombo"
                  value="<?php echo htmlspecialchars($details_data['city'] ?? ''); ?>" />
              </div>

              <!-- Postal Code -->
              <div class="col-12 col-md-6">
                <label for="postalCode" class="form-label-custom">Postal Code</label>
                <input
                  type="text"
                  class="form-control form-control-custom"
                  id="postalCode"
                  placeholder="e.g. 70200"
                  value="<?php echo htmlspecialchars($details_data['postal_code'] ?? $details_data['zip'] ?? ''); ?>" />
              </div>
            </div>

            <div class="text-end">
              <button type="button" class="btn-niru-action" onclick=" updateSetting();">
                Update Account
              </button>
            </div>
          </form>

          <div class="dashboard-card p-4 p-md-5 mb-4">
            <div class="d-flex align-items-center gap-2 mb-4">
              <i class="bi bi-lock fs-4" style="color: var(--niru-primary)"></i>
              <h2
                class="fs-4 fw-semibold mb-0"
                style="color: var(--niru-primary)">
                Change Password
              </h2>
            </div>

            <form onsubmit="event.preventDefault()">

              <div class="mb-4">
                <label for="currentPassword" class="form-label-custom">Current Password</label>
                <input
                  type="password"
                  class="form-control form-control-custom"
                  id="currentPassword"
                  placeholder="••••••••" />
              </div>

              <div class="row g-4 mb-4">
                <div class="col-12 col-md-6">
                  <label for="newPassword" class="form-label-custom">New Password</label>
                  <input
                    type="password"
                    class="form-control form-control-custom"
                    id="newPassword"
                    placeholder="••••••••" />
                </div>
                <div class="col-12 col-md-6">
                  <label for="confirmNewPassword" class="form-label-custom">Confirm New Password</label>
                  <input
                    type="password"
                    class="form-control form-control-custom"
                    id="confirmNewPassword"
                    placeholder="••••••••" />
                </div>
              </div>
              <div id="msgdiv" class="d-none">
                <div id="msg" role="alert"></div>
              </div>

              <div class="text-end">
                <button type="button" class="btn-niru-action" onclick=" passwordChange();">
                  Save Password
                </button>
              </div>
            </form>
          </div>
        </div>
      <?php
    } else {
      // Not Logged In View
      ?>
        <div class="col-12">
          <div class="row">
            <div class="col-12 text-center mt-5 mb-5" style="height: 50vh;">
              <h2 class="text-danger fw-bold">Please Login to view your Profile</h2>
              <a href="../login.php" class="btn btn-primary mt-3">Login / Register</a>
            </div>
          </div>
        </div>
      <?php
    }
      ?>
      <footer>
        <div class="row g-4">
          <div class="col-12 col-lg-3">
            <h3 class="fs-4 fw-bold mb-3" style="color: var(--niru-primary)">
              NiRu
            </h3>
            <p class="mb-0" style="color: var(--niru-body-text)">
              © 2024 NiRu Furnitures.<br />Crafted for Comfort.
            </p>
          </div>
          <div class="col-6 col-md-3">
            <div class="footer-heading">Company</div>
            <ul class="footer-links">
              <li><a href="#">About Us</a></li>
              <li><a href="#">Sustainability</a></li>
            </ul>
          </div>
          <div class="col-6 col-md-3">
            <div class="footer-heading">Support</div>
            <ul class="footer-links">
              <li><a href="#">Privacy Policy</a></li>
              <li><a href="#">Shipping Info</a></li>
            </ul>
          </div>
          <div class="col-12 col-md-3">
            <div class="footer-heading">Contact</div>
            <p class="mb-2" style="color: var(--niru-body-text)">
              nirufurni@gmail.com
            </p>
            <div class="d-flex gap-3 fs-5" style="color: var(--niru-primary)">
              <a
                href="#"
                class="text-decoration-none"
                style="color: inherit"
                aria-label="Instagram"><i class="bi bi-instagram"></i></a>
              <a
                href="#"
                class="text-decoration-none"
                style="color: inherit"
                aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
            </div>
          </div>
        </div>
      </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>