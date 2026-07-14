<?php
/**
 * NiRu-Furnitures — Admin Categories (admin/categories.php)
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
    // Check if category has products
    $prodCount = dbFetchOne("SELECT COUNT(*) AS cnt FROM products WHERE category_id = ?", 'i', $id)['cnt'];
    if ($prodCount > 0) {
        setFlash("Cannot delete category. It contains $prodCount product(s).", 'danger');
    } else {
        $cat = dbFetchOne("SELECT image FROM categories WHERE id = ?", 'i', $id);
        if ($cat) {
            dbExecute("DELETE FROM categories WHERE id = ?", 'i', $id);
            if ($cat['image'] && file_exists('../assets/images/categories/' . $cat['image'])) {
                unlink('../assets/images/categories/' . $cat['image']);
            }
            setFlash('Category deleted successfully.', 'success');
        }
    }
    redirect('categories.php');
}

// ── Handle Save (Add / Edit) ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)($_POST['id'] ?? 0);
    $name        = sanitize($_POST['name'] ?? '');
    $slug        = sanitize($_POST['slug'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $sort_order  = (int)($_POST['sort_order'] ?? 0);
    
    if (empty($name) || empty($slug)) {
        setFlash('Name and Slug are required.', 'danger');
    } else {
        // Handle Image Upload
        $imageName = $_POST['current_image'] ?? null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                $dir = '../assets/images/categories/';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                
                $newImage = $slug . '-' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $newImage)) {
                    // Remove old image
                    if ($imageName && file_exists($dir . $imageName)) unlink($dir . $imageName);
                    $imageName = $newImage;
                }
            } else {
                setFlash('Invalid image format. Only JPG, PNG, WEBP allowed.', 'danger');
            }
        }
        
        if ($id > 0) {
            dbExecute("UPDATE categories SET name=?, slug=?, description=?, image=?, sort_order=? WHERE id=?", 
                      'ssssii', $name, $slug, $description, $imageName, $sort_order, $id);
            setFlash('Category updated successfully.', 'success');
        } else {
            dbInsert("INSERT INTO categories (name, slug, description, image, sort_order) VALUES (?, ?, ?, ?, ?)", 
                     'ssssi', $name, $slug, $description, $imageName, $sort_order);
            setFlash('Category created successfully.', 'success');
        }
        redirect('categories.php');
    }
}

// ── View Logic ────────────────────────────────────────────────
if ($action === 'edit' || $action === 'add') {
    $id = (int)($_GET['id'] ?? 0);
    $cat = $id ? dbFetchOne("SELECT * FROM categories WHERE id = ?", 'i', $id) : null;
    $pageTitle = ($cat ? 'Edit' : 'Add') . ' Category — Admin';
} else {
    $categories = dbFetchAll("
        SELECT c.*, COUNT(p.id) AS prod_count 
        FROM categories c 
        LEFT JOIN products p ON p.category_id = c.id 
        GROUP BY c.id 
        ORDER BY c.sort_order ASC, c.name ASC
    ");
    $pageTitle = 'Categories — Admin';
}

$extraJs = [SITE_URL . '/assets/js/admin.js'];
include '../includes/admin-navbar.php';
?>

    <?php include '../includes/admin-sidebar.php'; ?>

    <main class="admin-main">
        <?= renderFlash() ?>

        <?php if ($action === 'list'): ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-0">Categories</h1>
            <a href="categories.php?action=add" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Add Category
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 80px">Image</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Sort Order</th>
                            <th>Products</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $c): ?>
                        <tr>
                            <td class="ps-4">
                                <img src="<?= productImageUrl($c['image'], 'categories') ?>" 
                                     alt="<?= e($c['name']) ?>" class="rounded" width="48" height="48" style="object-fit:cover">
                            </td>
                            <td class="fw-medium"><?= e($c['name']) ?></td>
                            <td class="text-muted"><?= e($c['slug']) ?></td>
                            <td><?= $c['sort_order'] ?></td>
                            <td><span class="badge bg-secondary"><?= $c['prod_count'] ?></span></td>
                            <td class="text-end pe-4">
                                <a href="categories.php?action=edit&id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if ($c['prod_count'] == 0): ?>
                                <a href="categories.php?action=delete&id=<?= $c['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   title="Delete"
                                   data-confirm="Are you sure you want to delete this category?">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php else: ?>
                                <button class="btn btn-sm btn-outline-secondary" disabled title="Cannot delete category with products">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($categories)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No categories found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php else: // Add / Edit Form ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-0"><?= $cat ? 'Edit Category' : 'Add New Category' ?></h1>
            <a href="categories.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>

        <form method="POST" action="categories.php?action=<?= $action ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
            <input type="hidden" name="id" value="<?= $cat['id'] ?? 0 ?>">
            <input type="hidden" name="current_image" value="<?= e($cat['image'] ?? '') ?>">

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm p-4 h-100">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="<?= e($cat['name'] ?? '') ?>" 
                                   oninput="document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '')" required>
                            <div class="invalid-feedback">Name is required.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="slug" name="slug" value="<?= e($cat['slug'] ?? '') ?>" required>
                            <div class="form-text">URL-friendly name (e.g. living-room).</div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-medium">Description</label>
                            <textarea class="form-control" name="description" rows="4"><?= e($cat['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm p-4 mb-4">
                        <label class="form-label fw-medium">Category Image</label>
                        <div class="mb-3 text-center">
                            <img id="catImgPreview" 
                                 src="<?= productImageUrl($cat['image'] ?? null, 'categories') ?>" 
                                 class="rounded img-thumbnail" 
                                 style="width: 100%; aspect-ratio: 4/3; object-fit: cover;">
                        </div>
                        <input type="file" class="form-control form-control-sm" name="image" accept="image/*" data-preview="catImgPreview">
                    </div>

                    <div class="card border-0 shadow-sm p-4">
                        <div class="mb-4">
                            <label class="form-label fw-medium">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" value="<?= $cat['sort_order'] ?? 0 ?>" min="0">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save me-1"></i>Save Category
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <?php endif; ?>

<?php include '../includes/admin-footer.php'; ?>
