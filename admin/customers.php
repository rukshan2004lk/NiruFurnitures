<?php
/**
 * NiRu-Furnitures — Admin Customers (admin/customers.php)
 */
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

// ── Handle Delete & Status Toggle ─────────────────────────────
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = (int)($_GET['id'] ?? 0);
    
    // Prevent modifying main admin (id=1 usually, or own account)
    if ($id === currentUserId()) {
        setFlash("You cannot modify your own account.", "danger");
        redirect("customers.php");
    }
    
    if ($action === 'toggle_status') {
        $u = dbFetchOne("SELECT status FROM users WHERE id = ?", 'i', $id);
        if ($u) {
            $isActive = in_array((string) $u['status'], ['1', 'active'], true);
            $newStatus = $isActive ? 0 : 1;
            $newStatusLabel = $newStatus ? 'active' : 'inactive';
            dbExecute("UPDATE users SET status = ? WHERE id = ?", 'ii', $newStatus, $id);
            setFlash("User status updated to $newStatusLabel.", "success");
        }
    } elseif ($action === 'delete') {
        dbExecute("DELETE FROM users WHERE id = ?", 'i', $id);
        setFlash("User deleted.", "success");
    }
    redirect("customers.php");
}

// ── View Logic ────────────────────────────────────────────────
$q = sanitize($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;

$where = ["role = 'customer'"];
$params = [];
$types = '';

if ($q) {
    $where[] = "(name LIKE ? OR email LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $types .= 'ss';
}

$whereSQL = implode(' AND ', $where);

$total = dbFetchOne("SELECT COUNT(*) AS total FROM users WHERE $whereSQL", $types, ...$params)['total'];
$pag = paginate($total, $perPage, $page);

$users = dbFetchAll("SELECT * FROM users WHERE $whereSQL ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$pag['offset']}", $types, ...$params);

$pageTitle = 'Customers — Admin';
$extraJs = [SITE_URL . '/assets/js/admin.js'];

include '../includes/admin-navbar.php';
?>

    <?php include '../includes/admin-sidebar.php'; ?>

    <main class="admin-main">
        <?= renderFlash() ?>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h1 class="h3 fw-bold mb-0">Customers</h1>
        </div>

        <!-- Filter -->
        <div class="card border-0 shadow-sm mb-4 p-3">
            <form method="GET" action="customers.php" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-icon-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" name="q" class="form-control" placeholder="Search by name or email..." value="<?= e($q) ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Search</button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Customer</th>
                            <th>Contact Info</th>
                            <th>Joined Date</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <?php
                            $isActive = in_array((string) $u['status'], ['1', 'active'], true);
                            $statusLabel = $isActive ? 'Active' : 'Inactive';
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= avatarUrl($u['avatar'] ?? null) ?>" width="40" height="40" class="rounded-circle" style="object-fit:cover">
                                    <div class="fw-medium text-dark"><?= e($u['name']) ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="text-muted small"><i class="bi bi-envelope me-1"></i><?= e($u['email']) ?></div>
                                <?php if ($u['phone']): ?>
                                <div class="text-muted small"><i class="bi bi-telephone me-1"></i><?= e($u['phone']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= formatDate($u['created_at']) ?></td>
                            <td>
                                <span class="badge <?= $isActive ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                                    <?= $statusLabel ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="customers.php?action=toggle_status&id=<?= $u['id'] ?>" 
                                   class="btn btn-sm <?= $isActive ? 'btn-outline-warning' : 'btn-outline-success' ?>" 
                                   title="<?= $isActive ? 'Disable Account' : 'Enable Account' ?>">
                                    <i class="bi <?= $isActive ? 'bi-person-dash' : 'bi-person-check' ?>"></i>
                                </a>
                                <a href="customers.php?action=delete&id=<?= $u['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   title="Delete Customer" data-confirm="Are you sure you want to permanently delete this customer?">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No customers found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <?= paginationLinks($pag['pages'], $pag['current'], 'customers.php?' . http_build_query(array_filter(['q' => $q]))) ?>
        </div>

<?php include '../includes/admin-footer.php'; ?>
