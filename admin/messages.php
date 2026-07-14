<?php
/**
 * NiRu-Furnitures — Admin Messages (admin/messages.php)
 */
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

// ── Handle Delete & Mark Read ──────────────────────────────────
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = (int)($_GET['id'] ?? 0);
    
    if ($action === 'delete') {
        dbExecute("DELETE FROM messages WHERE id = ?", 'i', $id);
        setFlash('Message deleted.', 'success');
    } elseif ($action === 'mark_read') {
        dbExecute("UPDATE messages SET is_read = 1 WHERE id = ?", 'i', $id);
        setFlash('Message marked as read.', 'success');
    }
    redirect('messages.php');
}

// ── View Logic ────────────────────────────────────────────────
$q = sanitize($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;

$where = ["1=1"];
$params = [];
$types = '';

if ($q) {
    $where[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ?)";
    $params[] = "%$q%"; $params[] = "%$q%"; $params[] = "%$q%";
    $types .= 'sss';
}
$whereSQL = implode(' AND ', $where);

$total = dbFetchOne("SELECT COUNT(*) AS total FROM messages WHERE $whereSQL", $types, ...$params)['total'];
$pag = paginate($total, $perPage, $page);

$messages = dbFetchAll("SELECT * FROM messages WHERE $whereSQL ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$pag['offset']}", $types, ...$params);

$pageTitle = 'Messages — Admin';
$extraJs = [SITE_URL . '/assets/js/admin.js'];

include '../includes/admin-navbar.php';
?>

    <?php include '../includes/admin-sidebar.php'; ?>

    <main class="admin-main">
        <?= renderFlash() ?>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h1 class="h3 fw-bold mb-0">Contact Messages</h1>
        </div>

        <div class="card border-0 shadow-sm mb-4 p-3">
            <form method="GET" action="messages.php" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-icon-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" name="q" class="form-control" placeholder="Search name, email, or subject..." value="<?= e($q) ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Search</button>
                </div>
            </form>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Status</th>
                            <th>Sender</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $m): ?>
                        <tr class="<?= $m['is_read'] ? '' : 'table-light fw-bold' ?>">
                            <td class="ps-4">
                                <?php if ($m['is_read']): ?>
                                <span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-envelope-open me-1"></i>Read</span>
                                <?php else: ?>
                                <span class="badge bg-primary-subtle text-primary"><i class="bi bi-envelope-fill me-1"></i>New</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="text-dark"><?= e($m['name']) ?></div>
                                <a href="mailto:<?= e($m['email']) ?>" class="small text-muted text-decoration-none"><?= e($m['email']) ?></a>
                            </td>
                            <td><?= e($m['subject']) ?></td>
                            <td><?= formatDate($m['created_at']) ?></td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#msgModal_<?= $m['id'] ?>">
                                    View
                                </button>
                                <a href="messages.php?action=delete&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete this message?"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        
                        <!-- Modal -->
                        <div class="modal fade" id="msgModal_<?= $m['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Message Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body pt-3">
                                        <div class="mb-3 pb-3 border-bottom">
                                            <div class="text-muted small">From</div>
                                            <div class="fw-medium"><?= e($m['name']) ?> &lt;<?= e($m['email']) ?>&gt;</div>
                                            <div class="text-muted small mt-2">Subject</div>
                                            <div class="fw-medium"><?= e($m['subject']) ?></div>
                                            <div class="text-muted small mt-2">Date</div>
                                            <div class="fw-medium"><?= formatDate($m['created_at'], true) ?></div>
                                        </div>
                                        <div>
                                            <div class="text-muted small mb-2">Message</div>
                                            <div class="p-3 bg-light rounded text-dark" style="white-space:pre-wrap"><?= e($m['message']) ?></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <?php if (!$m['is_read']): ?>
                                        <a href="messages.php?action=mark_read&id=<?= $m['id'] ?>" class="btn btn-primary">Mark as Read</a>
                                        <?php endif; ?>
                                        <a href="mailto:<?= e($m['email']) ?>" class="btn btn-outline-secondary">Reply via Email</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($messages)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No messages found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <?= paginationLinks($pag['pages'], $pag['current'], 'messages.php?' . http_build_query(array_filter(['q' => $q]))) ?>
        </div>

<?php include '../includes/admin-footer.php'; ?>
