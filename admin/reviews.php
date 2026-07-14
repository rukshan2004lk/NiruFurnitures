<?php
/**
 * NiRu-Furnitures — Admin Reviews (admin/reviews.php)
 */
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

// ── Handle Actions ────────────────────────────────────────────
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = (int)($_GET['id'] ?? 0);
    
    if ($action === 'approve') {
        dbExecute("UPDATE reviews SET approved = 1 WHERE id = ?", 'i', $id);
        setFlash('Review approved and published.', 'success');
    } elseif ($action === 'hide') {
        dbExecute("UPDATE reviews SET approved = 0 WHERE id = ?", 'i', $id);
        setFlash('Review hidden.', 'success');
    } elseif ($action === 'delete') {
        dbExecute("DELETE FROM reviews WHERE id = ?", 'i', $id);
        setFlash('Review deleted.', 'success');
    }
    redirect('reviews.php');
}

// ── View Logic ────────────────────────────────────────────────
$q = sanitize($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;

$where = ["1=1"];
$params = [];
$types = '';

if ($q) {
    $where[] = "(p.name LIKE ? OR u.name LIKE ? OR r.comment LIKE ?)";
    $params[] = "%$q%"; $params[] = "%$q%"; $params[] = "%$q%";
    $types .= 'sss';
}
$whereSQL = implode(' AND ', $where);

$total = dbFetchOne("SELECT COUNT(*) AS total FROM reviews r JOIN products p ON p.id = r.product_id JOIN users u ON u.id = r.user_id WHERE $whereSQL", $types, ...$params)['total'];
$pag = paginate($total, $perPage, $page);

$reviews = dbFetchAll("
    SELECT r.*, p.name AS product_name, u.name AS user_name 
    FROM reviews r 
    JOIN products p ON p.id = r.product_id 
    JOIN users u ON u.id = r.user_id 
    WHERE $whereSQL 
    ORDER BY r.created_at DESC 
    LIMIT {$perPage} OFFSET {$pag['offset']}
", $types, ...$params);

$pageTitle = 'Reviews — Admin';
$extraJs = [SITE_URL . '/assets/js/admin.js'];

include '../includes/admin-navbar.php';
?>

    <?php include '../includes/admin-sidebar.php'; ?>

    <main class="admin-main">
        <?= renderFlash() ?>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h1 class="h3 fw-bold mb-0">Product Reviews</h1>
        </div>

        <div class="card border-0 shadow-sm mb-4 p-3">
            <form method="GET" action="reviews.php" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-icon-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" name="q" class="form-control" placeholder="Search by product, user, or comment..." value="<?= e($q) ?>">
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
                            <th class="ps-4">Product</th>
                            <th>Reviewer</th>
                            <th>Rating & Comment</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $r): ?>
                        <tr>
                            <td class="ps-4">
                                <a href="../product.php?id=<?= $r['product_id'] ?>" target="_blank" class="fw-medium text-dark text-decoration-none">
                                    <?= e($r['product_name']) ?>
                                </a>
                            </td>
                            <td><?= e($r['user_name']) ?><br><small class="text-muted"><?= formatDate($r['created_at']) ?></small></td>
                            <td style="max-width:300px">
                                <div class="mb-1"><?= starRating((float)$r['rating']) ?></div>
                                <div class="small text-muted text-truncate" title="<?= e($r['comment']) ?>"><?= e($r['comment']) ?></div>
                            </td>
                            <td>
                                <span class="badge <?= $r['approved'] ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' ?>">
                                    <?= $r['approved'] ? 'Published' : 'Pending' ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <?php if (!$r['approved']): ?>
                                <a href="reviews.php?action=approve&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-success" title="Approve"><i class="bi bi-check-lg"></i></a>
                                <?php else: ?>
                                <a href="reviews.php?action=hide&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-warning" title="Hide"><i class="bi bi-eye-slash"></i></a>
                                <?php endif; ?>
                                <a href="reviews.php?action=delete&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete review?"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($reviews)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No reviews found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <?= paginationLinks($pag['pages'], $pag['current'], 'reviews.php?' . http_build_query(array_filter(['q' => $q]))) ?>
        </div>

<?php include '../includes/admin-footer.php'; ?>
