<?php
/**
 * NiRu-Furnitures — Admin Products (admin/products.php)
 */
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

$action = $_GET['action'] ?? 'list';

// ── Handle Delete ─────────────────────────────────────────────
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $p = dbFetchOne("SELECT images FROM products WHERE id = ?", 'i', $id);
    if ($p) {
        dbExecute("DELETE FROM products WHERE id = ?", 'i', $id);
        $imgs = json_decode($p['images'] ?? '[]', true);
        foreach ($imgs as $img) {
            $path = UPLOADS_PATH . '/products/' . $img;
            if (file_exists($path)) unlink($path);
        }
        setFlash('Product deleted successfully.', 'success');
    }
    redirect('products.php');
}

// ── Handle Save (POST) ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)($_POST['id'] ?? 0);
    $name        = sanitize($_POST['name'] ?? '');
    $slug        = sanitize($_POST['slug'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price       = (float)($_POST['price'] ?? 0);
    $sale_price  = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $stock       = (int)($_POST['stock'] ?? 0);
    $status      = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
    $featured    = isset($_POST['featured']) ? 1 : 0;
    $sku         = sanitize($_POST['sku'] ?? '');
    $material    = sanitize($_POST['material'] ?? '');
    $color       = sanitize($_POST['color'] ?? '');
    $dimensions  = sanitize($_POST['dimensions'] ?? '');
    $weight      = !empty($_POST['weight']) ? (float)$_POST['weight'] : null;
    $description = $_POST['description'] ?? '';

    if (empty($name) || empty($slug) || $category_id <= 0 || $price <= 0) {
        setFlash('Please fill in all required fields correctly.', 'danger');
    } else {
        // ── Image Upload ──────────────────────────────────────
        $existingImages = json_decode($_POST['existing_images'] ?? '[]', true);
        if (!is_array($existingImages)) $existingImages = [];

        $newImages  = [];
        $uploadErrs = [];

        if (!empty($_FILES['images']['name'][0])) {
            $dir = UPLOADS_PATH . '/products/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            foreach ($_FILES['images']['name'] as $i => $origName) {
                if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                    if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                        $uploadErrs[] = "File #" . ($i + 1) . " — error code " . $_FILES['images']['error'][$i];
                    }
                    continue;
                }
                $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $uploadErrs[] = "\"$origName\": unsupported format (use jpg/png/webp)";
                    continue;
                }
                if ($_FILES['images']['size'][$i] > MAX_FILE_SIZE) {
                    $uploadErrs[] = "\"$origName\": exceeds 5MB limit";
                    continue;
                }
                $fName = $slug . '-' . time() . '-' . $i . '.' . $ext;
                if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $dir . $fName)) {
                    $newImages[] = $fName;
                } else {
                    $uploadErrs[] = "\"$origName\": could not save (check uploads/products/ permissions)";
                }
            }
        }

        if (!empty($uploadErrs)) {
            setFlash('Upload issues: ' . implode(' | ', $uploadErrs), 'warning');
        }

        $finalImages = array_values(array_merge($existingImages, $newImages));
        $imagesJson  = json_encode($finalImages);

        if ($id > 0) {
            dbExecute(
                "UPDATE products SET category_id=?, name=?, slug=?, description=?, price=?, sale_price=?,
                 stock=?, sku=?, material=?, color=?, dimensions=?, weight=?, images=?, status=?, featured=?
                 WHERE id=?",
                'isssddisssssssii',
                $category_id, $name, $slug, $description, $price, $sale_price,
                $stock, $sku, $material, $color, $dimensions, $weight, $imagesJson, $status, $featured, $id
            );
            $msg = 'Product updated.';
        } else {
            dbInsert(
                "INSERT INTO products (category_id, name, slug, description, price, sale_price,
                 stock, sku, material, color, dimensions, weight, images, status, featured)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                'isssddissssssssi',
                $category_id, $name, $slug, $description, $price, $sale_price,
                $stock, $sku, $material, $color, $dimensions, $weight, $imagesJson, $status, $featured
            );
            $msg = 'Product created.';
        }

        if (!empty($newImages)) $msg .= ' ' . count($newImages) . ' image(s) uploaded.';
        setFlash($msg, 'success');
        redirect('products.php');
    }
}

// ── View Logic ────────────────────────────────────────────────
if ($action === 'edit' || $action === 'add') {
    $id = (int)($_GET['id'] ?? 0);
    $p  = $id ? dbFetchOne("SELECT * FROM products WHERE id = ?", 'i', $id) : [];
    if ($p === null) $p = [];
    $categories = dbFetchAll("SELECT id, name FROM categories ORDER BY sort_order ASC, name ASC");
    $pageTitle  = (!empty($p) ? 'Edit' : 'Add') . ' Product — Admin';
} else {
    $q     = sanitize($_GET['q'] ?? '');
    $catId = (int)($_GET['category'] ?? 0);
    $where = ["1=1"];
    $params = [];
    $types  = '';
    if ($q)     { $where[] = "(p.name LIKE ? OR p.sku LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; $types .= 'ss'; }
    if ($catId) { $where[] = "p.category_id = ?"; $params[] = $catId; $types .= 'i'; }
    $whereSQL = implode(' AND ', $where);
    $page    = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 20;
    $total   = dbFetchOne("SELECT COUNT(*) AS t FROM products p WHERE $whereSQL", $types, ...$params)['t'] ?? 0;
    $pag     = paginate($total, $perPage, $page);
    $products = dbFetchAll(
        "SELECT p.id, p.name, p.price, p.sale_price, p.stock, p.status, p.featured, p.images, c.name AS category_name
         FROM products p JOIN categories c ON c.id = p.category_id
         WHERE $whereSQL ORDER BY p.created_at DESC LIMIT {$perPage} OFFSET {$pag['offset']}",
        $types, ...$params
    );
    $categories = dbFetchAll("SELECT id, name FROM categories ORDER BY name ASC");
    $pageTitle  = 'Products — Admin';
}

$extraJs = [SITE_URL . '/assets/js/admin.js'];
include '../includes/admin-navbar.php';
?>

    <?php include '../includes/admin-sidebar.php'; ?>

    <main class="admin-main">
        <?= renderFlash() ?>

        <?php if ($action === 'list'): ?>
        <!-- ── Product List ───────────────────────────────── -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h1 class="h3 fw-bold mb-0">Products</h1>
            <a href="products.php?action=add" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Add Product
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-4 p-3">
            <form method="GET" action="products.php" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-icon-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" name="q" class="form-control" placeholder="Search by name or SKU..." value="<?= e($q) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $catId == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width:70px">Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $pr):
                            $imgs = json_decode($pr['images'] ?? '[]', true); ?>
                        <tr>
                            <td class="ps-4">
                                <img src="<?= productImageUrl($imgs[0] ?? null) ?>"
                                     alt="<?= e($pr['name']) ?>" class="rounded"
                                     width="48" height="48" style="object-fit:cover">
                            </td>
                            <td>
                                <div class="fw-medium text-dark"><?= e($pr['name']) ?></div>
                                <?php if ($pr['featured']): ?>
                                    <span class="badge bg-warning text-dark" style="font-size:.65rem">Featured</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?= e($pr['category_name']) ?></td>
                            <td>
                                <?php if ($pr['sale_price']): ?>
                                    <span class="fw-medium text-danger"><?= formatPrice((float)$pr['sale_price']) ?></span><br>
                                    <del class="text-muted small"><?= formatPrice((float)$pr['price']) ?></del>
                                <?php else: ?>
                                    <span class="fw-medium"><?= formatPrice((float)$pr['price']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= stockBadge((int)$pr['stock']) ?></td>
                            <td>
                                <span class="badge <?= $pr['status'] === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?>">
                                    <?= ucfirst($pr['status']) ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="../product.php?id=<?= $pr['id'] ?>" target="_blank"
                                   class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                                <a href="products.php?action=edit&id=<?= $pr['id'] ?>"
                                   class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="products.php?action=delete&id=<?= $pr['id'] ?>"
                                   class="btn btn-sm btn-outline-danger" title="Delete"
                                   data-confirm="Delete this product?"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($products)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">No products found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">
            <?= paginationLinks($pag['pages'], $pag['current'], 'products.php?' . http_build_query(array_filter(['q' => $q, 'category' => $catId]))) ?>
        </div>

        <?php else: // Add / Edit Form ?>
        <!-- ── Add / Edit Form ────────────────────────────── -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-0"><?= !empty($p) ? 'Edit Product' : 'Add New Product' ?></h1>
            <a href="products.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>

        <form method="POST" action="products.php?action=<?= $action ?>"
              enctype="multipart/form-data" class="needs-validation" novalidate>
            <input type="hidden" name="id" value="<?= $p['id'] ?? 0 ?>">

            <div class="row g-4">
                <!-- LEFT COLUMN -->
                <div class="col-lg-8">
                    <!-- Basic Info -->
                    <div class="card border-0 shadow-sm p-4 mb-4">
                        <h6 class="fw-bold mb-3">Basic Information</h6>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="<?= e($p['name'] ?? '') ?>"
                                   oninput="document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'')"
                                   required>
                            <div class="invalid-feedback">Name is required.</div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Slug <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="slug" name="slug"
                                       value="<?= e($p['slug'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Category <span class="text-danger">*</span></label>
                                <select class="form-select" name="category_id" required>
                                    <option value="">Select Category...</option>
                                    <?php foreach ($categories as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= ($p['category_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>>
                                        <?= e($c['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-medium">Description</label>
                            <textarea class="form-control" name="description" rows="6"><?= e($p['description'] ?? '') ?></textarea>
                            <div class="form-text">HTML is allowed for formatting.</div>
                        </div>
                    </div>

                    <!-- Pricing & Inventory -->
                    <div class="card border-0 shadow-sm p-4 mb-4">
                        <h6 class="fw-bold mb-3">Pricing &amp; Inventory</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Regular Price (Rs.) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price"
                                       value="<?= $p['price'] ?? '' ?>" min="1" step="0.01" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Sale Price (Rs.)</label>
                                <input type="number" class="form-control" name="sale_price"
                                       value="<?= $p['sale_price'] ?? '' ?>" min="0" step="0.01">
                                <div class="form-text">Leave empty for no sale.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Stock Qty <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stock"
                                       value="<?= $p['stock'] ?? 0 ?>" min="0" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">SKU</label>
                                <input type="text" class="form-control" name="sku" value="<?= e($p['sku'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Specifications -->
                    <div class="card border-0 shadow-sm p-4">
                        <h6 class="fw-bold mb-3">Specifications</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Material</label>
                                <input type="text" class="form-control" name="material"
                                       value="<?= e($p['material'] ?? '') ?>" placeholder="e.g. Solid Teak Wood">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Color</label>
                                <input type="text" class="form-control" name="color"
                                       value="<?= e($p['color'] ?? '') ?>" placeholder="e.g. Walnut Brown">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Dimensions</label>
                                <input type="text" class="form-control" name="dimensions"
                                       value="<?= e($p['dimensions'] ?? '') ?>" placeholder="e.g. W120 x D60 x H75 cm">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Weight (kg)</label>
                                <input type="number" class="form-control" name="weight"
                                       value="<?= $p['weight'] ?? '' ?>" step="0.1" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="col-lg-4">
                    <!-- Publishing -->
                    <div class="card border-0 shadow-sm p-4 mb-4">
                        <h6 class="fw-bold mb-3">Publishing</h6>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Status</label>
                            <select class="form-select" name="status">
                                <option value="active" <?= ($p['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active (Visible)</option>
                                <option value="inactive" <?= ($p['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive (Hidden)</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="featured" name="featured" <?= ($p['featured'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-medium" for="featured">Featured Product</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-save me-1"></i>Save Product
                        </button>
                    </div>

                    <!-- Images -->
                    <div class="card border-0 shadow-sm p-4">
                        <h6 class="fw-bold mb-1">Product Images</h6>
                        <p class="small text-muted mb-3">First image is the cover. JPG, PNG, WebP — max 5MB.</p>

                        <?php
                        $currImgs = json_decode($p['images'] ?? '[]', true);
                        if (!is_array($currImgs)) $currImgs = [];
                        ?>
                        <input type="hidden" id="existingImagesInput" name="existing_images"
                               value="<?= e(json_encode($currImgs)) ?>">

                        <!-- Existing images -->
                        <?php if (!empty($currImgs)): ?>
                        <div class="mb-3">
                            <p class="small fw-semibold text-secondary mb-2">Current Images</p>
                            <div class="d-flex flex-wrap gap-2" id="existingImagesWrap">
                                <?php foreach ($currImgs as $idx => $img): ?>
                                <div class="position-relative" id="eimg_<?= $idx ?>" style="width:80px">
                                    <img src="<?= productImageUrl($img) ?>" width="80" height="80"
                                         style="object-fit:cover;border-radius:8px;display:block" alt="">
                                    <button type="button"
                                            onclick="removeExistingImage('<?= e(addslashes($img)) ?>', 'eimg_<?= $idx ?>')"
                                            style="position:absolute;top:-6px;right:-6px;width:22px;height:22px;border-radius:50%;background:#dc3545;border:2px solid #fff;color:#fff;font-size:14px;line-height:1;cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center"
                                            title="Remove">&times;</button>
                                    <?php if ($idx === 0): ?>
                                    <span style="font-size:.65rem;background:#3d2219;color:#fff;padding:1px 6px;border-radius:4px;display:block;text-align:center;margin-top:3px">Cover</span>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Drag-drop zone -->
                        <div id="uploadZone"
                             style="border:2px dashed #c9b8ae;border-radius:12px;padding:1.5rem 1rem;text-align:center;cursor:pointer;transition:all .2s;background:#fdf8f5"
                             onclick="document.getElementById('imageFileInput').click()"
                             ondragover="event.preventDefault();this.style.borderColor='#3d2219';this.style.background='#f0e8e0'"
                             ondragleave="this.style.borderColor='#c9b8ae';this.style.background='#fdf8f5'"
                             ondrop="handleImageDrop(event)">
                            <i class="bi bi-cloud-arrow-up" style="font-size:2rem;color:#9b8076;display:block;margin-bottom:.4rem"></i>
                            <p class="mb-1 small fw-medium" style="color:#5a3e38">Click or drag images here</p>
                            <p class="mb-0" style="font-size:.73rem;color:#9b8076">JPG, PNG, WebP &bull; Max 5MB each</p>
                        </div>
                        <input type="file" id="imageFileInput" name="images[]"
                               accept="image/jpeg,image/png,image/webp,image/gif" multiple
                               style="display:none" onchange="previewNewImages(this.files)">

                        <!-- Preview of newly selected images -->
                        <div id="newImgPreviews" class="d-flex flex-wrap gap-2 mt-3"></div>
                    </div>
                </div><!-- /col-lg-4 -->
            </div><!-- /row -->
        </form>

        <script>
        function removeExistingImage(filename, wrapId) {
            document.getElementById(wrapId)?.remove();
            const hidden = document.getElementById('existingImagesInput');
            let imgs = JSON.parse(hidden.value || '[]');
            hidden.value = JSON.stringify(imgs.filter(f => f !== filename));
        }

        function previewNewImages(files) {
            const preview = document.getElementById('newImgPreviews');
            for (const file of files) {
                const reader = new FileReader();
                reader.onload = e => {
                    const div = document.createElement('div');
                    div.style.cssText = 'position:relative;width:80px';
                    div.innerHTML = `<img src="${e.target.result}" width="80" height="80"
                        style="object-fit:cover;border-radius:8px;display:block">
                        <span style="font-size:.65rem;background:#2d6a4f;color:#fff;padding:1px 6px;border-radius:4px;display:block;text-align:center;margin-top:3px">New</span>`;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        }

        function handleImageDrop(e) {
            e.preventDefault();
            const zone = document.getElementById('uploadZone');
            zone.style.borderColor = '#c9b8ae';
            zone.style.background  = '#fdf8f5';
            const input = document.getElementById('imageFileInput');
            const dt = new DataTransfer();
            for (const file of e.dataTransfer.files) {
                if (file.type.startsWith('image/')) dt.items.add(file);
            }
            input.files = dt.files;
            previewNewImages(input.files);
        }
        </script>

        <?php endif; ?>

<?php include '../includes/admin-footer.php'; ?>
