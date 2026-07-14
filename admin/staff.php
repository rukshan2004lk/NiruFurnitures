<?php
/**
 * NiRu-Furnitures — Admin Staff (admin/staff.php)
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
    if ($id === currentUserId()) {
        setFlash("You cannot delete your own account.", "danger");
    } else {
        dbExecute("DELETE FROM users WHERE id = ? AND role IN ('admin', 'manager')", 'i', $id);
        setFlash("Staff member deleted.", "success");
    }
    redirect("staff.php");
}

// ── Handle Save ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = (int)($_POST['id'] ?? 0);
    $name     = sanitize($_POST['name'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $role     = sanitize($_POST['role'] ?? 'manager');
    $password = $_POST['password'] ?? '';
    
    if (empty($name) || empty($email)) {
        setFlash('Name and email are required.', 'danger');
    } elseif ($id === 0 && empty($password)) {
        setFlash('Password is required for new staff members.', 'danger');
    } else {
        if ($id > 0) {
            // Update
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                dbExecute("UPDATE users SET name=?, email=?, role=?, password_hash=? WHERE id=?", 'ssssi', $name, $email, $role, $hash, $id);
            } else {
                dbExecute("UPDATE users SET name=?, email=?, role=? WHERE id=?", 'sssi', $name, $email, $role, $id);
            }
            setFlash('Staff member updated.', 'success');
        } else {
            // Check email
            $exists = dbFetchOne("SELECT id FROM users WHERE email=?", 's', $email);
            if ($exists) {
                setFlash('Email already in use.', 'danger');
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                dbInsert("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)", 'ssss', $name, $email, $hash, $role);
                setFlash('Staff member added.', 'success');
            }
        }
        redirect('staff.php');
    }
}

// ── View Logic ────────────────────────────────────────────────
if ($action === 'edit' || $action === 'add') {
    $id = (int)($_GET['id'] ?? 0);
    $staff = $id ? dbFetchOne("SELECT * FROM users WHERE id = ?", 'i', $id) : null;
    $pageTitle = ($staff ? 'Edit' : 'Add') . ' Staff — Admin';
} else {
    $staffList = dbFetchAll("SELECT * FROM users WHERE role IN ('admin', 'manager') ORDER BY name ASC");
    $pageTitle = 'Staff Members — Admin';
}

$extraJs = [SITE_URL . '/assets/js/admin.js'];
include '../includes/admin-navbar.php';
?>

    <?php include '../includes/admin-sidebar.php'; ?>

    <main class="admin-main">
        <?= renderFlash() ?>

        <?php if ($action === 'list'): ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-0">Staff Members</h1>
            <a href="staff.php?action=add" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i>Add Staff
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($staffList as $s): ?>
                        <tr>
                            <td class="ps-4 fw-medium text-dark"><?= e($s['name']) ?></td>
                            <td class="text-muted"><?= e($s['email']) ?></td>
                            <td>
                                <span class="badge <?= $s['role'] === 'admin' ? 'bg-danger-subtle text-danger' : 'bg-info-subtle text-info' ?>">
                                    <?= ucfirst($s['role']) ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="staff.php?action=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <?php if ($s['id'] !== currentUserId()): ?>
                                <a href="staff.php?action=delete&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" title="Delete" data-confirm="Delete this staff member?"><i class="bi bi-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php else: ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-0"><?= $staff ? 'Edit Staff Member' : 'Add Staff Member' ?></h1>
            <a href="staff.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>

        <form method="POST" action="staff.php?action=<?= $action ?>" class="needs-validation" novalidate>
            <input type="hidden" name="id" value="<?= $staff['id'] ?? 0 ?>">
            
            <div class="card border-0 shadow-sm p-4 mx-auto" style="max-width: 600px;">
                <div class="mb-3">
                    <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="<?= e($staff['name'] ?? '') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" value="<?= e($staff['email'] ?? '') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="manager" <?= ($staff['role'] ?? '') === 'manager' ? 'selected' : '' ?>>Manager</option>
                        <option value="admin" <?= ($staff['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-medium">Password <?= $staff ? '' : '<span class="text-danger">*</span>' ?></label>
                    <input type="password" class="form-control" name="password" minlength="8" <?= $staff ? '' : 'required' ?>>
                    <?php if ($staff): ?>
                    <div class="form-text">Leave blank to keep current password.</div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 btn-lg">
                    <i class="bi bi-save me-1"></i>Save Staff Member
                </button>
            </div>
        </form>

        <?php endif; ?>

<?php include '../includes/admin-footer.php'; ?>
