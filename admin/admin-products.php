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

$status_filter = $_GET['status'] ?? 'all';
$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$cnt_all_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `products`");
$total_all = (int)($cnt_all_rs->fetch_assoc()['cnt'] ?? 0);

$cnt_active_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `products` WHERE `status_id` = 1");
$total_active = (int)($cnt_active_rs->fetch_assoc()['cnt'] ?? 0);

$cnt_draft_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `products` WHERE `status_id` != 1");
$total_draft = (int)($cnt_draft_rs->fetch_assoc()['cnt'] ?? 0);

$where_clause = "";
if ($status_filter === 'active') {
    $where_clause = " WHERE p.status_id = 1";
} else if ($status_filter === 'draft') {
    $where_clause = " WHERE p.status_id != 1";
}

$total_filtered_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `products` p" . $where_clause);
$total_filtered = (int)($total_filtered_rs->fetch_assoc()['cnt'] ?? 0);
$total_pages = max(1, (int)ceil($total_filtered / $limit));

$query = "SELECT p.*, c.name AS category_name,
                 COALESCE(i.quantity, 0) AS stock_qty,
                 (SELECT image_path FROM product_images WHERE product_id = p.product_id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS image_path
          FROM `products` p
          LEFT JOIN `categories` c ON p.category_id = c.category_id
          LEFT JOIN `inventory` i ON p.product_id = i.product_id
          $where_clause
          ORDER BY p.product_id DESC
          LIMIT $limit OFFSET $offset";

$products_rs = Database::search($query);

$categories_list = [];
$cat_query_rs = Database::search("SELECT * FROM `categories` ORDER BY `name` ASC");
while ($c = $cat_query_rs->fetch_assoc()) {
    $categories_list[] = $c;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NiRu Furnitures - Admin Product Inventory</title>
  
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
        <li class="nav-item"><a href="admin-products.php" class="nav-link active fw-bold text-white"><i class="bi bi-box-seam me-2"></i>Products</a></li>
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
      <li><a href="admin-dashboard.php" class="admin-link"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
      <li><a href="admin-products.php" class="admin-link active"><i class="bi bi-box-seam"></i> Products</a></li>
      <li><a href="admin-orders.php" class="admin-link"><i class="bi bi-cart"></i> Orders</a></li>
      <li><a href="admin-users.php" class="admin-link"><i class="bi bi-people"></i> Users</a></li>
      <li><a href="admin-settings.php" class="admin-link"><i class="bi bi-gear"></i> Settings</a></li>
    </ul>

    <div class="px-3 mt-auto d-flex flex-column gap-2 mb-3">
      <a href="../index.php" class="admin-link-store"><i class="bi bi-shop"></i> View Store</a>
      <a href="../logout.php" class="admin-link-logout"><i class="bi bi-box-arrow-left"></i> Log Out</a>
    </div>
  </aside>

  <!-- Main Canvas -->
  <main class="admin-main">
    
    <div class="top-header-glass">
      <div>
        <h1 class="fs-4 fw-semibold mb-0" style="color: var(--niru-primary);">Products</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="admin-dashboard.php" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item active fw-semibold" style="color: #1b1c1c;">Inventory</li>
          </ol>
        </nav>
      </div>
   
    </div>

    <div class="admin-content-canvas">
      
      <!-- Actions Bar -->
      <div class="action-bar-card">
        <div class="d-flex flex-wrap gap-2">
          <button class="pill-filter-btn <?php echo ($status_filter === 'all') ? 'active' : ''; ?>" onclick="filterAdminProducts('all');">
            All Products (<?php echo $total_all; ?>)
          </button>
          <button class="pill-filter-btn <?php echo ($status_filter === 'active') ? 'active' : ''; ?>" onclick="filterAdminProducts('active');">
            Active (<?php echo $total_active; ?>)
          </button>
          <button class="pill-filter-btn <?php echo ($status_filter === 'draft') ? 'active' : ''; ?>" onclick="filterAdminProducts('draft');">
            Draft / Inactive (<?php echo $total_draft; ?>)
          </button>
        </div>

        <button type="button" class="btn-add-product border-0" data-bs-toggle="modal" data-bs-target="#addProductModal" onclick="resetAddModal();">
          <i class="bi bi-plus-lg"></i> Add New Product
        </button>
      </div>

      <!-- Inventory Table -->
      <div class="product-table-card mb-4">
        <div class="table-responsive">
          <table class="table table-products align-middle mb-0">
            <thead>
              <tr>
                <th>PRODUCT</th>
                <th>CATEGORY</th>
                <th>PRICE</th>
                <th>STOCK</th>
                <th>STATUS</th>
                <th class="text-end">ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              if ($products_rs->num_rows > 0) {
                while ($prod = $products_rs->fetch_assoc()) {
                  $pid = (int)$prod['product_id'];
                  $img = !empty($prod['image_path']) ? '../' . $prod['image_path'] : '../Images/products/nordic_lounge.png';
                  $stock = (int)$prod['stock_qty'];
                  $is_active = ((int)$prod['status_id'] === 1);

                  $stock_badge = ($stock === 0) ? 'stock-badge-out' : (($stock < 10) ? 'stock-badge-low' : 'stock-badge-normal');
              ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-3">
                      <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>" class="product-thumb-sm" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px;">
                      <div>
                        <h4 class="fs-6 fw-bold mb-0" style="color: #1b1c1c;">
                          <a href="../product-detail.php?id=<?php echo $pid; ?>" target="_blank" class="text-decoration-none text-dark">
                            <?php echo htmlspecialchars($prod['name']); ?>
                          </a>
                        </h4>
                        <small class="text-muted" style="font-size: 12px;">ID: #NR-<?php echo str_pad($pid, 4, '0', STR_PAD_LEFT); ?></small>
                      </div>
                    </div>
                  </td>
                  <td style="color: #1b1c1c;"><?php echo htmlspecialchars($prod['category_name'] ?? 'Collection'); ?></td>
                  <td class="fw-semibold" style="color: #1b1c1c;">Rs. <?php echo number_format($prod['price'], 2); ?></td>
                  <td><span class="<?php echo $stock_badge; ?>"><?php echo str_pad($stock, 2, '0', STR_PAD_LEFT); ?> units</span></td>
                  <td>
                    <?php if ($is_active) { ?>
                      <span class="status-badge-active"><span class="dot-active"></span> ACTIVE</span>
                    <?php } else { ?>
                      <span class="status-badge-draft"><span class="dot-draft"></span> DRAFT</span>
                    <?php } ?>
                  </td>
                  <td class="text-end">
                    <button type="button" class="btn btn-sm btn-link text-dark p-1" title="Edit Product" onclick="openEditModal(<?php echo $pid; ?>);">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-link text-danger p-1" title="Deactivate Product" onclick="deleteProduct(<?php echo $pid; ?>);">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              <?php 
                }
              } else { 
              ?>
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox display-6 d-block mb-2 text-secondary"></i>
                    No products found.
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination Footer -->
        <div class="d-flex align-items-center justify-content-between p-3 bg-light border-top">
          <small class="text-muted">
            Showing <strong><?php echo ($total_filtered > 0) ? ($offset + 1) : 0; ?> - <?php echo min($offset + $limit, $total_filtered); ?></strong> of <strong><?php echo $total_filtered; ?></strong> products
          </small>
          
          <?php if ($total_pages > 1) { ?>
            <ul class="pagination pagination-sm mb-0">
              <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?status=<?php echo urlencode($status_filter); ?>&page=<?php echo $page - 1; ?>"><i class="bi bi-chevron-left"></i></a>
              </li>
              <?php for ($p = 1; $p <= $total_pages; $p++) { ?>
                <li class="page-item <?php echo ($p === $page) ? 'active' : ''; ?>">
                  <a class="page-link <?php echo ($p !== $page) ? 'text-dark' : ''; ?>" 
                     href="?status=<?php echo urlencode($status_filter); ?>&page=<?php echo $p; ?>" 
                     style="<?php echo ($p === $page) ? 'background-color: var(--niru-primary); border-color: var(--niru-primary);' : ''; ?>">
                    <?php echo $p; ?>
                  </a>
                </li>
              <?php } ?>
              <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link text-dark" href="?status=<?php echo urlencode($status_filter); ?>&page=<?php echo $page + 1; ?>"><i class="bi bi-chevron-right"></i></a>
              </li>
            </ul>
          <?php } ?>
        </div>
      </div>

    </div>

  </main>

  <!-- ======================== 1. ADD PRODUCT MODAL ======================== -->
  <div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold" style="color: var(--niru-primary);">Add New Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <form id="addProductModalForm" enctype="multipart/form-data">
            
            <div class="mb-3">
              <label class="form-label fw-semibold">Product Name</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. Nordic Lounge Armchair" required>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Category</label>
                <select name="category_id" class="form-select" required>
                  <option value="" disabled selected>Select Category</option>
                  <?php foreach ($categories_list as $cat) { ?>
                    <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                  <?php } ?>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Initial Status</label>
                <select name="status_id" class="form-select">
                  <option value="1">Active (Visible in Store)</option>
                  <option value="2">Draft / Inactive</option>
                </select>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Price (Rs.)</label>
                <input type="number" step="0.01" name="price" class="form-control" placeholder="12500.00" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Initial Stock Quantity</label>
                <input type="number" name="quantity" class="form-control" placeholder="10" min="0" required>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Craftsmanship details, materials, and dimensions..."></textarea>
            </div>

            <!-- Up to 5 Images Selection -->
            <div class="mb-3">
              <label class="form-label fw-semibold d-flex justify-content-between">
                <span>Product Images (Select up to 5)</span>
                <small class="text-muted">Select the radio button to set the Primary image</small>
              </label>
              <input type="file" id="add_product_images_input" name="product_images[]" class="form-control" accept="image/*" multiple onchange="handleNewImagesPreview(this, 'add_preview_box', 'primary_img_idx');" required>
              <small class="text-muted d-block mt-1">If no image is specifically selected as primary, the 1st image becomes primary by default.</small>
            </div>

            <div id="add_preview_box" class="d-flex flex-wrap gap-3 mb-3"></div>

            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-dark fw-semibold" style="background-color: var(--niru-primary);" onclick="submitAddProductModal();">
                <i class="bi bi-cloud-arrow-up me-1"></i> Save Product
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- ======================== 2. EDIT PRODUCT MODAL ======================== -->
  <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold" style="color: var(--niru-primary);">Edit Product & Gallery</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <form id="editProductModalForm" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="edit_product_id">
            <input type="hidden" name="deleted_images" id="edit_deleted_images" value="[]">

            <div class="mb-3">
              <label class="form-label fw-semibold">Product Name</label>
              <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Category</label>
                <select name="category_id" id="edit_category_id" class="form-select" required>
                  <?php foreach ($categories_list as $cat) { ?>
                    <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                  <?php } ?>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="status_id" id="edit_status_id" class="form-select">
                  <option value="1">Active</option>
                  <option value="2">Draft / Inactive</option>
                </select>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Price (Rs.)</label>
                <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Stock Quantity</label>
                <input type="number" name="quantity" id="edit_quantity" class="form-control" min="0" required>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
            </div>

            <!-- Existing Gallery -->
            <div class="mb-3">
              <label class="form-label fw-semibold d-flex justify-content-between">
                <span>Existing Images</span>
                <small class="text-muted">Choose Primary Image (Radio) or Remove</small>
              </label>
              <div id="edit_existing_images_box" class="d-flex flex-wrap gap-3 p-2 bg-light rounded-3 border"></div>
            </div>

            <!-- Additional Images -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Upload Additional Images</label>
              <input type="file" id="edit_new_images_input" name="new_product_images[]" class="form-control" accept="image/*" multiple onchange="handleEditAdditionalImages(this);">
              <small class="text-muted" id="edit_images_count_notice">You can upload up to 5 total images.</small>
            </div>
            <div id="edit_new_preview_box" class="d-flex flex-wrap gap-3 mb-3"></div>

            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-dark fw-semibold" style="background-color: var(--niru-primary);" onclick="submitEditProductModal();">
                <i class="bi bi-check2-circle me-1"></i> Update Product & Images
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