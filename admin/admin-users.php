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

$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$roles_rs = Database::search("SELECT * FROM `user_role` ORDER BY `role_id` ASC");
$roles_list = [];
while ($r = $roles_rs->fetch_assoc()) {
    $roles_list[] = $r;
}

$total_users_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `user`");
$total_users = (int)($total_users_rs->fetch_assoc()['cnt'] ?? 0);
$total_pages = max(1, (int)ceil($total_users / $limit));

$query = "SELECT u.*, r.role_name 
          FROM `user` u 
          LEFT JOIN `user_role` r ON u.role_id = r.role_id 
          ORDER BY u.user_id DESC 
          LIMIT $limit OFFSET $offset";
$users_rs = Database::search($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NiRu Furnitures - User & Role Management</title>
  
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
    <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileSidebar">
      <i class="bi bi-list fs-5"></i> Menu
    </button>
  </div>

  <!-- Mobile Offcanvas -->
  <div class="offcanvas offcanvas-start bg-dark text-light" tabindex="-1" id="adminMobileSidebar">
    <div class="offcanvas-header border-bottom border-secondary">
      <h5 class="offcanvas-title text-white fw-bold">Admin Navigation</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3">
      <ul class="nav flex-column gap-2 mb-4">
        <li class="nav-item"><a href="admin-dashboard.php" class="nav-link text-white-50"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
        <li class="nav-item"><a href="admin-products.php" class="nav-link text-white-50"><i class="bi bi-box-seam me-2"></i>Products</a></li>
        <li class="nav-item"><a href="admin-orders.php" class="nav-link text-white-50"><i class="bi bi-cart me-2"></i>Orders</a></li>
        <li class="nav-item"><a href="admin-users.php" class="nav-link active fw-bold text-white"><i class="bi bi-people me-2"></i>Users</a></li>
        <li class="nav-item"><a href="admin-settings.php" class="nav-link text-white-50"><i class="bi bi-gear me-2"></i>Settings</a></li>
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
      <li><a href="admin-users.php" class="admin-link active"><i class="bi bi-people"></i> Users</a></li>
      <li><a href="admin-settings.php" class="admin-link"><i class="bi bi-gear"></i> Settings</a></li>
    </ul>

    <div class="px-3 mt-auto d-flex flex-column gap-2 mb-3">
      <a href="../index.php" class="admin-link-store"><i class="bi bi-shop"></i> View Store</a>
      <a href="../logout.php" class="admin-link-logout"><i class="bi bi-box-arrow-left"></i> Log Out</a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="admin-main">
    
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-5">
      <div>
        <h1 class="fs-3 fw-bold mb-1" style="color: var(--niru-primary);">User & Role Management</h1>
        <p class="text-muted mb-0">Oversee customer accounts, assign administrative privileges, or adjust permissions.</p>
      </div>

      <div class="d-flex gap-2">
        <div class="dropdown">
          <button class="btn btn-light border d-flex align-items-center gap-2 px-3 py-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" style="background-color: #eae7e7; border-color: var(--niru-border);">
            <i class="bi bi-funnel"></i> Filter Role
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow border-0">
            <li><a class="dropdown-item" href="javascript:void(0);" onclick="filterUsersByRolePill('all');">All Roles</a></li>
            <?php foreach ($roles_list as $rl) { ?>
              <li><a class="dropdown-item" href="javascript:void(0);" onclick="filterUsersByRolePill('<?php echo htmlspecialchars($rl['role_name']); ?>');"><?php echo htmlspecialchars($rl['role_name']); ?></a></li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="customer-table-card mb-5 bg-white rounded-4 border shadow-sm p-4">
      
      <div class="toolbar-customer d-flex justify-content-between align-items-center mb-4">
        <div class="search-input-customer position-relative flex-grow-1 me-3" style="max-width: 400px;">
          <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
          <input type="text" id="adminUserSearchInput" class="form-control rounded-pill ps-5 py-2 shadow-sm" placeholder="Search by name, email or ID..." onkeyup="filterAdminUsersTable();">
        </div>

        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-sm btn-outline-secondary rounded-circle" aria-label="Refresh" onclick="window.location.reload();"><i class="bi bi-arrow-clockwise"></i></button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-customers align-middle mb-0">
          <thead>
            <tr>
              <th>USER</th>
              <th>ROLE</th>
              <th>EMAIL ADDRESS</th>
              <th>JOINED DATE</th>
              <th>STATUS</th>
              <th class="text-end">ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if ($users_rs->num_rows > 0) {
              $bg_colors = ['#ffdbd0', '#feddb3', '#e4e4cc', '#e5e2e1'];
              $i = 0;

              while ($user = $users_rs->fetch_assoc()) {
                $uid = (int)$user['user_id'];
                $first_name = $user['first_name'] ?? 'Customer';
                $last_name = $user['last_name'] ?? '';
                $full_name = trim($first_name . ' ' . $last_name);
                $initial = strtoupper(substr($first_name, 0, 1));
                $bg = $bg_colors[$i % count($bg_colors)];
                $i++;

                $role_name = $user['role_name'] ?? 'Customer';
                $role_id = (int)($user['role_id'] ?? 2);
                $email = htmlspecialchars($user['email']);
                $date_joined = isset($user['created_at']) ? date("M d, Y", strtotime($user['created_at'])) : 'Active';
                $status_id = (int)($user['status_id'] ?? 1);
                $is_active = ($status_id === 1);

                // Badge styling per role
                $role_badge = 'bg-secondary-subtle text-secondary';
                if ($role_id === 1) {
                  $role_badge = 'bg-danger-subtle text-danger border border-danger-subtle';
                } else if ($role_id === 3) {
                  $role_badge = 'bg-info-subtle text-info border border-info-subtle';
                } else if ($role_id === 2) {
                  $role_badge = 'bg-primary-subtle text-primary border border-primary-subtle';
                }
            ?>
              <tr class="admin-user-row">
                <td>
                  <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px; background-color: <?php echo $bg; ?>; color: var(--niru-primary);">
                      <?php echo $initial; ?>
                    </div>
                    <div>
                      <div class="fw-semibold" style="color: var(--niru-primary);"><?php echo htmlspecialchars($full_name); ?></div>
                      <small class="text-muted" style="font-size: 11px;">ID: #USR-<?php echo str_pad($uid, 4, '0', STR_PAD_LEFT); ?></small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge rounded-pill <?php echo $role_badge; ?> px-3 py-2 fw-bold user-role-badge">
                    <?php echo htmlspecialchars(strtoupper($role_name)); ?>
                  </span>
                </td>
                <td class="text-muted"><?php echo $email; ?></td>
                <td class="text-muted"><?php echo $date_joined; ?></td>
                <td>
                  <?php if ($is_active) { ?>
                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">Active</span>
                  <?php } else { ?>
                    <span class="badge bg-secondary-subtle text-muted px-3 py-2 rounded-pill fw-bold">Deactivated</span>
                  <?php } ?>
                </td>
                <td class="text-end">
                  <button type="button" 
                          class="btn btn-sm btn-outline-dark px-3 py-1 rounded-pill" 
                          onclick="openEditUserRoleModal(<?php echo $uid; ?>, <?php echo $role_id; ?>, <?php echo $status_id; ?>, '<?php echo htmlspecialchars(addslashes($full_name)); ?>');">
                    <i class="bi bi-pencil me-1"></i> Edit Role
                  </button>
                </td>
              </tr>
            <?php 
              }
            } else { 
            ?>
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="bi bi-people display-6 d-block mb-2 text-secondary"></i>
                  No registered users found.
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div class="d-flex align-items-center justify-content-between p-3 bg-light border-top rounded-bottom-4 mt-3">
        <small class="text-muted">
          Showing <strong><?php echo ($total_users > 0) ? ($offset + 1) : 0; ?> - <?php echo min($offset + $limit, $total_users); ?></strong> of <strong><?php echo $total_users; ?></strong> entries
        </small>
        
        <?php if ($total_pages > 1) { ?>
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
              <a class="page-link" href="?page=<?php echo $page - 1; ?>"><i class="bi bi-chevron-left"></i></a>
            </li>
            <?php for ($p = 1; $p <= $total_pages; $p++) { ?>
              <li class="page-item <?php echo ($p === $page) ? 'active' : ''; ?>">
                <a class="page-link <?php echo ($p !== $page) ? 'text-dark' : ''; ?>" 
                   href="?page=<?php echo $p; ?>" 
                   style="<?php echo ($p === $page) ? 'background-color: var(--niru-primary); border-color: var(--niru-primary);' : ''; ?>">
                  <?php echo $p; ?>
                </a>
              </li>
            <?php } ?>
            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
              <a class="page-link text-dark" href="?page=<?php echo $page + 1; ?>"><i class="bi bi-chevron-right"></i></a>
            </li>
          </ul>
        <?php } ?>
      </div>
    </div>

  </main>

  <!-- ======================== EDIT USER ROLE MODAL ======================== -->
  <div class="modal fade" id="editUserRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold" style="color: var(--niru-primary);">Update User Role & Access</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <form id="editUserRoleForm">
            <input type="hidden" name="user_id" id="user_modal_id">

            <p class="mb-3 text-muted">
              Managing permissions for: <strong id="user_modal_name" class="text-dark"></strong>
            </p>

            <div class="mb-3">
              <label class="form-label fw-semibold">Assigned Role</label>
              <select name="role_id" id="user_modal_role_select" class="form-select" required>
                <?php foreach ($roles_list as $rl) { ?>
                  <option value="<?php echo $rl['role_id']; ?>"><?php echo htmlspecialchars($rl['role_name']); ?> (<?php echo htmlspecialchars($rl['description'] ?? ''); ?>)</option>
                <?php } ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Account Status</label>
              <select name="status_id" id="user_modal_status_select" class="form-select" required>
                <option value="1">Active</option>
                <option value="2">Deactivated / Suspended</option>
              </select>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-dark fw-semibold" style="background-color: var(--niru-primary);" onclick="submitUserRoleUpdate();">
                Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/script.js"></script>
</body>
</html>