<?php
/**
 * NiRu-Furnitures — Admin Banners (admin/banners.php)
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
    $b = dbFetchOne("SELECT image FROM banners WHERE id = ?", 'i', $id);
    if ($b) {
        dbExecute("DELETE FROM banners WHERE id = ?", 'i', $id);
        if ($b['image'] && file_exists('../assets/images/banners/' . $b['image'])) {
            unlink('../assets/images/banners/' . $b['image']);
        }
        setFlash('Banner deleted successfully.', 'success');
    }
    redirect('banners.php');
}

// ── Handle Save ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = (int)($_POST['id'] ?? 0);
    $title      = sanitize($_POST['title'] ?? '');
    $subtitle   = sanitize($_POST['subtitle'] ?? '');
    $link       = sanitize($_POST['link'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $active     = isset($_POST['active']) ? 1 : 0;
    
    if (empty($title)) {
        setFlash('Title is required.', 'danger');
    } else {
        $imageName = $_POST['current_image'] ?? null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                $dir = '../assets/images/banners/';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                
                $newImage = 'banner-' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $newImage)) {
                    if ($imageName && file_exists($dir . $imageName)) unlink($dir . $imageName);
                    $imageName = $newImage;
                }
            } else {
                setFlash('Invalid image format.', 'danger');
            }
        }
        
        if ($id > 0) {
            dbExecute("UPDATE banners SET title=?, subtitle=?, link=?, image=?, sort_order=?, active=? WHERE id=?", 
                      'ssssiii', $title, $subtitle, $link, $imageName, $sort_order, $active, $id);
            setFlash('Banner updated successfully.', 'success');
        } else {
            dbInsert("INSERT INTO banners (title, subtitle, link, image, sort_order, active) VALUES (?, ?, ?, ?, ?, ?)", 
                     'ssssii', $title, $subtitle, $link, $imageName, $sort_order, $active);
            setFlash('Banner created successfully.', 'success');
        }
        redirect('banners.php');
    }
}

// ── View Logic ────────────────────────────────────────────────
if ($action === 'edit' || $action === 'add') {
    $id = (int)($_GET['id'] ?? 0);
    $banner = $id ? dbFetchOne("SELECT * FROM banners WHERE id = ?", 'i', $id) : null;
    $pageTitle = ($banner ? 'Edit' : 'Add') . ' Banner — Admin';
} else {
    $banners = dbFetchAll("SELECT * FROM banners ORDER BY sort_order ASC, id DESC");
    $pageTitle = 'Banners — Admin';
}

$extraJs = [SITE_URL . '/assets/js/admin.js'];
include '../includes/admin-navbar.php';
?>

    <?php include '../includes/admin-sidebar.php'; ?>

    <main class="admin-main">
        <?= renderFlash() ?>

        <?php if ($action === 'list'): ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-0">Hero Banners</h1>
            <a href="banners.php?action=add" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Add Banner
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width:120px">Image</th>
                            <th>Title / Subtitle</th>
                            <th>Sort Order</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($banners as $b): ?>
                        <tr>
                            <td class="ps-4">
                                <img src="<?= productImageUrl($b['image'], 'banners') ?>" 
                                     alt="<?= e($b['title']) ?>" class="rounded" width="96" height="48" style="object-fit:cover">
                            </td>
                            <td>
                                <div class="fw-medium text-dark"><?= e($b['title']) ?></div>
                                <div class="text-muted small"><?= e($b['subtitle']) ?></div>
                            </td>
                            <td><?= $b['sort_order'] ?></td>
                            <td>
                                <span class="badge <?= $b['active'] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?>">
                                    <?= $b['active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="banners.php?action=edit&id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="banners.php?action=delete&id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-danger" title="Delete" data-confirm="Delete banner?"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($banners)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No banners found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php else: // Add / Edit Form ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-0"><?= $banner ? 'Edit Banner' : 'Add New Banner' ?></h1>
            <a href="banners.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>

        <form method="POST" action="banners.php?action=<?= $action ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
            <input type="hidden" name="id" value="<?= $banner['id'] ?? 0 ?>">
            <input type="hidden" name="current_image" value="<?= e($banner['image'] ?? '') ?>">

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm p-4 h-100">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" value="<?= e($banner['title'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Subtitle</label>
                            <input type="text" class="form-control" name="subtitle" value="<?= e($banner['subtitle'] ?? '') ?>">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-medium">Button Link</label>
                            <input type="text" class="form-control" name="link" value="<?= e($banner['link'] ?? 'shop.php') ?>" placeholder="e.g. shop.php?category=living-room">
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm p-4 mb-4">
                        <label class="form-label fw-medium">Banner Image <span class="text-danger">*</span></label>
                        <div class="mb-3 text-center bg-light rounded" style="aspect-ratio:21/9; overflow:hidden;">
                            <img id="banImgPreview" 
                                 src="<?= productImageUrl($banner['image'] ?? null, 'banners') ?>" 
                                 class="w-100 h-100" style="object-fit:cover;">
                        </div>
                        <input type="file" class="form-control form-control-sm" name="image" accept="image/*" data-preview="banImgPreview" <?= !$banner ? 'required' : '' ?>>
                    </div>

                    <div class="card border-0 shadow-sm p-4">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" value="<?= $banner['sort_order'] ?? 0 ?>">
                        </div>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="active" name="active" <?= ($banner['active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-medium" for="active">Active (Visible)</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save me-1"></i>Save Banner
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <?php endif; ?>

<?php include '../includes/admin-footer.php'; ?>
