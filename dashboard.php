<?php
/**
 * NiRu-Furnitures — User Dashboard (dashboard.php)
 */
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$userId = currentUserId();
$user   = dbFetchOne("SELECT * FROM users WHERE id = ?", 'i', $userId);

// ── Handle Profile Update ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $name  = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    
    if (empty($name)) {
        setFlash('Name is required.', 'danger');
    } else {
        $avatarStr = $user['avatar'];
        
        if (!empty($_FILES['avatar']['name'])) {
            $dir = UPLOADS_PATH . '/profiles/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $newAvatar = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dir . $newAvatar)) {
                    if ($avatarStr && file_exists($dir . $avatarStr)) {
                        unlink($dir . $avatarStr);
                    }
                    $avatarStr = $newAvatar;
                }
            } else {
                setFlash('Invalid image format.', 'warning');
            }
        }
        
        dbExecute("UPDATE users SET name = ?, phone = ?, avatar = ? WHERE id = ?", 'sssi', $name, $phone, $avatarStr, $userId);
        $_SESSION['user_name'] = $name;
        $user['name'] = $name;
        $user['phone'] = $phone;
        $user['avatar'] = $avatarStr;
        setFlash('Profile updated successfully.', 'success');
    }
}

// ── Handle Address Update ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_address') {
    $address = sanitize($_POST['address'] ?? '');
    $city    = sanitize($_POST['city'] ?? '');
    $zip     = sanitize($_POST['zip_code'] ?? '');

    dbExecute("UPDATE users SET address = ?, city = ?, zip_code = ? WHERE id = ?", 'sssi', $address, $city, $zip, $userId);
    
    $user['address']  = $address;
    $user['city']     = $city;
    $user['zip_code'] = $zip;
    setFlash('Shipping address updated.', 'success');
}

// ── Fetch Orders ──────────────────────────────────────────────
$orders = dbFetchAll("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC", 'i', $userId);

// Chart Data (Order Status Distribution)
$statusCounts = array_fill_keys(['pending', 'processing', 'shipped', 'delivered', 'cancelled'], 0);
foreach ($orders as $o) {
    if (isset($statusCounts[$o['status']])) {
        $statusCounts[$o['status']]++;
    }
}
$chartLabels = json_encode(array_map('ucfirst', array_keys($statusCounts)));
$chartValues = json_encode(array_values($statusCounts));

// Fetch Wishlist Items
$wishlistItems = dbFetchAll("
    SELECT p.* 
    FROM wishlist w 
    JOIN products p ON p.id = w.product_id 
    WHERE w.user_id = ?
", 'i', $userId);

$pageTitle = 'My Dashboard — NiRu-Furnitures';
$extraJs = [
    'https://cdn.jsdelivr.net/npm/chart.js',
    SITE_URL . '/assets/js/dashboard.js'
];

include 'includes/head.php';
include 'includes/navbar.php';
?>


<!-- Hero -->
<section class="page-hero" aria-label="My Dashboard">
    <div class="container-xl">
        <span class="hero-label">My Account</span>
        <h1>My Dashboard</h1>
        <p>Welcome back, <?= e(explode(' ', $user['name'])[0]) ?>. Manage your orders, wishlist and profile here.</p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">My Dashboard</li>
            </ol>
        </nav>
    </div>
</div>

<section class="dashboard-page">
    <div class="container-xl">
        <?= renderFlash() ?>
        
        <div class="row g-4">
            
            <!-- ── Sidebar ── -->
            <div class="col-lg-3">
                <div class="dashboard-sidebar-card">
                    <img src="<?= avatarUrl($user['avatar'] ?? null) ?>" alt="<?= e($user['name']) ?>" class="dash-avatar">
                    <h5><?= e($user['name']) ?></h5>
                    <p><?= e($user['email']) ?></p>
                    <a href="logout.php" class="btn-dashboard-danger d-block text-center text-decoration-none">
                        <i class="bi bi-box-arrow-right me-1"></i>Sign Out
                    </a>
                </div>

                <div class="dashboard-nav-card">
                    <div class="nav flex-column nav-pills" id="dashboard-tabs" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active text-start" data-bs-toggle="pill" data-bs-target="#tab-overview" type="button" role="tab">
                            <i class="bi bi-grid"></i>Overview
                        </button>
                        <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#tab-orders" type="button" role="tab">
                            <i class="bi bi-box-seam"></i>Order History
                        </button>
                        <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#tab-wishlist" type="button" role="tab">
                            <i class="bi bi-heart"></i>My Wishlist
                        </button>
                        <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#tab-profile" type="button" role="tab">
                            <i class="bi bi-person"></i>Profile Settings
                        </button>
                    </div>
                </div>
            </div>

            <!-- ── Main Content ── -->
            <div class="col-lg-9">
                <div class="tab-content" id="dashboard-tabContent">
                    
                    <!-- Overview Tab -->
                    <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
                        <div class="dashboard-content-card mb-4">
                            <h3 class="mb-4" style="font-size:1.25rem">Dashboard Overview</h3>
                            <div class="row g-4 mb-0">
                                <div class="col-md-4">
                                    <div class="dashboard-stat-card">
                                        <div class="stat-label">Total Orders</div>
                                        <div class="stat-value"><?= count($orders) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <?php
                                    $wishlistCount = dbFetchOne("SELECT COUNT(*) as c FROM wishlist WHERE user_id = ?", 'i', $userId)['c'];
                                    ?>
                                    <div class="dashboard-stat-card accent-red">
                                        <div class="stat-label">Wishlist Items</div>
                                        <div class="stat-value"><?= $wishlistCount ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <?php
                                    $totalSpent = dbFetchOne("SELECT SUM(total) as s FROM orders WHERE user_id = ? AND status != 'cancelled'", 'i', $userId)['s'] ?? 0;
                                    ?>
                                    <div class="dashboard-stat-card accent-green">
                                        <div class="stat-label">Total Spent</div>
                                        <div class="stat-value" style="font-size:1.4rem"><?= formatPrice((float)$totalSpent) ?></div>
                                    </div>
                                </div>
                            </div><!-- /row -->
                        </div><!-- /content-card -->
                        
                        <?php if (count($orders) > 0): ?>
                        <div class="row g-4">
                            <div class="col-md-8">
                                <div class="dashboard-content-card">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h6 class="mb-0">Recent Orders</h6>
                                        <button class="btn btn-link btn-sm text-decoration-none p-0" style="color:#7d4f3f;font-size:.82rem;font-weight:700" onclick="document.querySelector('[data-bs-target=\'#tab-orders\']').click()">View All &rarr;</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table dashboard-table mb-0">
                                            <thead><tr>
                                                <th class="ps-0">Order ID</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th class="text-end pe-0">Total</th>
                                            </tr></thead>
                                            <tbody>
                                                <?php foreach (array_slice($orders, 0, 4) as $o): ?>
                                                <tr>
                                                    <td class="ps-0"><a href="#" class="fw-medium text-primary text-decoration-none">#<?= str_pad($o['id'], 6, '0', STR_PAD_LEFT) ?></a></td>
                                                    <td><small><?= formatDate($o['created_at']) ?></small></td>
                                                    <td><?= orderStatusBadge($o['status']) ?></td>
                                                    <td class="text-end pe-0 fw-medium"><?= formatPrice((float)$o['total']) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="dashboard-content-card">
                                    <h6 class="mb-3">Order Status</h6>
                                    <div class="position-relative" style="height:200px">
                                        <canvas id="orderStatusChart" 
                                                data-labels='<?= $chartLabels ?>' 
                                                data-values='<?= $chartValues ?>'></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div><!-- /overview tab -->
                    
                    <!-- Orders Tab -->
                    <div class="tab-pane fade" id="tab-orders" role="tabpanel">
                        <div class="dashboard-content-card">
                            <h3 class="mb-4" style="font-size:1.25rem">Order History</h3>
                        <?php if (count($orders) > 0): ?>
                        <div class="card border-0 shadow-sm overflow-hidden">
                            <?php foreach ($orders as $i => $o): 
                                $items = dbFetchAll("SELECT oi.*, p.name, p.images FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?", 'i', $o['id']);
                            ?>
                            <div class="border-bottom <?= $i === 0 ? '' : 'mt-2' ?>">
                                <div class="p-4 d-flex justify-content-between align-items-center bg-white cursor-pointer" 
                                     data-order-toggle="<?= $o['id'] ?>">
                                    <div>
                                        <div class="d-flex align-items-center gap-3 mb-1">
                                            <span class="fw-bold fs-5">#<?= str_pad($o['id'], 6, '0', STR_PAD_LEFT) ?></span>
                                            <?= orderStatusBadge($o['status']) ?>
                                        </div>
                                        <div class="text-muted small">Placed on <?= formatDate($o['created_at'], true) ?></div>
                                    </div>
                                    <div class="text-end d-flex align-items-center gap-4">
                                        <div>
                                            <div class="text-muted small">Total Amount</div>
                                            <div class="fw-bold"><?= formatPrice((float)$o['total']) ?></div>
                                        </div>
                                        <i class="bi bi-chevron-down toggle-icon transition-transform fs-5 text-muted"></i>
                                    </div>
                                </div>
                                
                                <div id="orderDetail_<?= $o['id'] ?>" class="p-4 bg-light border-top" hidden>
                                    <h6 class="fw-bold mb-3 small text-uppercase">Items in this order</h6>
                                    <?php foreach ($items as $item): 
                                        $imgs = json_decode($item['images'] ?? '[]', true);
                                    ?>
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <img src="<?= productImageUrl($imgs[0] ?? null) ?>" width="56" height="56" class="rounded" style="object-fit:cover">
                                        <div class="flex-grow-1">
                                            <div class="fw-medium text-dark"><?= e($item['name']) ?></div>
                                            <div class="text-muted small">Qty: <?= $item['qty'] ?> &times; <?= formatPrice((float)$item['unit_price']) ?></div>
                                        </div>
                                        <div class="fw-medium"><?= formatPrice($item['qty'] * $item['unit_price']) ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                    
                                    <hr>
                                    
                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <h6 class="fw-bold small text-uppercase">Shipping Address</h6>
                                            <p class="text-muted small mb-0">
                                                <?= e($o['shipping_address']) ?><br>
                                                <?= e($o['shipping_city']) ?><br>
                                                <?= e($o['shipping_zip']) ?>
                                            </p>
                                        </div>
                                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                                            <h6 class="fw-bold small text-uppercase">Payment Method</h6>
                                            <p class="text-muted small mb-0">
                                                <?= e(strtoupper(str_replace('_', ' ', $o['payment_method']))) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                            <div class="wishlist-empty-state">
                                <i class="bi bi-box-seam"></i>
                                <h5>No orders yet</h5>
                                <p>When you buy something, your orders will appear here.</p>
                                <a href="shop.php" class="btn-cta-primary mx-auto">Start Shopping</a>
                            </div>
                        <?php endif; ?>
                        </div><!-- /content-card -->
                    </div><!-- /orders tab -->
                    
                    <!-- Wishlist Tab -->
                    <div class="tab-pane fade" id="tab-wishlist" role="tabpanel">
                        <div class="dashboard-content-card">
                            <h3 class="mb-4" style="font-size:1.25rem">My Wishlist</h3>
                            <?php if (count($wishlistItems) > 0): ?>
                            <div class="row g-4">
                                <?php foreach ($wishlistItems as $item): 
                                    $imgs = json_decode($item['images'] ?? '[]', true);
                                    $price = (float)$item['price'];
                                    $sale = $item['sale_price'] ? (float)$item['sale_price'] : null;
                                ?>
                                <div class="col-sm-6 col-md-4">
                                    <div class="p-card h-100 d-flex flex-column" style="border: 1px solid #ede8e3; border-radius: 8px; overflow: hidden; position: relative;">
                                        <a href="product.php?id=<?= $item['id'] ?>" class="d-block" style="height: 200px; background: #f5f1ec;">
                                            <img src="<?= productImageUrl($imgs[0] ?? null) ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?= e($item['name']) ?>">
                                        </a>
                                        <button class="p-wishlist-btn active" data-action="toggle-wishlist" data-product-id="<?= $item['id'] ?>" aria-label="Wishlist" style="position: absolute; top: 10px; right: 10px; border: none; background: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                                            <i class="bi bi-heart-fill"></i>
                                        </button>
                                        <div class="p-3 d-flex flex-column flex-grow-1">
                                            <h6 class="mb-1" style="font-size: 0.95rem; font-weight: 600;"><a href="product.php?id=<?= $item['id'] ?>" class="text-dark text-decoration-none"><?= e($item['name']) ?></a></h6>
                                            <div class="mt-auto pt-2">
                                                <span class="fw-bold" style="color: #c0392b;"><?= formatPrice($sale ?? $price) ?></span>
                                                <?php if ($sale): ?>
                                                    <span class="text-muted text-decoration-line-through ms-2 small"><?= formatPrice($price) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="wishlist-empty-state">
                                <i class="bi bi-heart"></i>
                                <h5>Your wishlist is empty</h5>
                                <p>Save items you like to your wishlist and they will appear here.</p>
                                <a href="shop.php" class="btn-cta-primary mx-auto">Discover Products</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div><!-- /wishlist tab -->
                    
                    <!-- Profile Tab -->
                    <div class="tab-pane fade" id="tab-profile" role="tabpanel">
                        <div class="row g-4">
                            <!-- Personal Details -->
                            <div class="col-md-6">
                                <div class="dashboard-content-card h-100">
                                    <h5 style="font-size:1rem;font-weight:700;color:#3d2219;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid #ede8e3">Personal Details</h5>
                                    <form method="POST" enctype="multipart/form-data">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="update_profile">
                                        
                                        <div class="mb-4 d-flex align-items-center gap-3">
                                            <img src="<?= avatarUrl($user['avatar'] ?? null) ?>" id="avatarPreview" alt="Avatar Preview" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 2px solid #ede8e3;">
                                            <div>
                                                <label class="btn btn-sm btn-outline-secondary" style="font-size: 0.8rem;" for="avatarInput">Change Photo</label>
                                                <input type="file" id="avatarInput" name="avatar" accept="image/*" class="d-none">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" name="name" value="<?= e($user['name']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled>
                                            <div class="form-text" style="color:#9b8076">Email cannot be changed.</div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" name="phone" value="<?= e($user['phone']) ?>">
                                        </div>
                                        <button type="submit" class="btn-dashboard-primary w-100">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Address Book -->
                            <div class="col-md-6">
                                <div class="dashboard-content-card h-100">
                                    <h5 style="font-size:1rem;font-weight:700;color:#3d2219;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid #ede8e3">Default Shipping Address</h5>
                                    <form method="POST">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="update_address">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Street Address</label>
                                            <input type="text" class="form-control" name="address" value="<?= e($user['address'] ?? '') ?>" placeholder="e.g. 123 Main St">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="city" value="<?= e($user['city'] ?? '') ?>">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Postal / Zip Code</label>
                                            <input type="text" class="form-control" name="zip_code" value="<?= e($user['zip_code'] ?? '') ?>">
                                        </div>
                                        <button type="submit" class="btn-dashboard-primary w-100">Update Address</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
