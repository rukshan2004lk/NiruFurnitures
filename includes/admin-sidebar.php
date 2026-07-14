<?php
/**
 * NiRu-Furnitures — Admin Sidebar
 */
$currentAdminPage = basename($_SERVER['PHP_SELF']);

function adminNavItem(string $href, string $icon, string $label, string $current, string $badge = ''): string
{
    $active = basename($href) === $current ? 'active' : '';
    $badgeHtml = $badge ? '<span class="sidebar-badge">' . $badge . '</span>' : '';
    return '<li class="nav-item">
        <a class="sidebar-link ' . $active . '" href="' . e($href) . '">
            <i class="bi ' . $icon . ' sidebar-icon"></i>
            <span class="sidebar-label">' . $label . $badgeHtml . '</span>
        </a>
    </li>';
}

$pendingOrders = dbFetchOne("SELECT COUNT(*) AS c FROM orders   WHERE status = 'pending'")['c'] ?? 0;
$unreadMsgs = dbFetchOne("SELECT COUNT(*) AS c FROM messages WHERE is_read = 0")['c'] ?? 0;
?>

<aside class="admin-sidebar" id="adminSidebar">

    <div class="sidebar-profile">
        <img src="<?= avatarUrl($_SESSION['user_avatar'] ?? null) ?>" class="sidebar-avatar" alt="Admin">
        <div class="sidebar-profile-info">
            <strong><?= e($_SESSION['user_name'] ?? 'Admin') ?></strong>
            <small><?= ucfirst($_SESSION['user_role'] ?? 'Administrator') ?></small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column mb-0">

            <li class="sidebar-section-title">Main</li>
            <?= adminNavItem('dashboard.php', 'bi-speedometer2', 'Dashboard', $currentAdminPage) ?>
            <?= adminNavItem('orders.php', 'bi-bag-check', 'Orders', $currentAdminPage, $pendingOrders > 0 ? (string) $pendingOrders : '') ?>
            <?= adminNavItem('customers.php', 'bi-people', 'Customers', $currentAdminPage) ?>

            <li class="sidebar-section-title">Catalogue</li>
            <?= adminNavItem('products.php', 'bi-box-seam', 'Products', $currentAdminPage) ?>
            <?= adminNavItem('categories.php', 'bi-tags', 'Categories', $currentAdminPage) ?>
            <?= adminNavItem('banners.php', 'bi-images', 'Banners', $currentAdminPage) ?>

            <li class="sidebar-section-title">Communication</li>
            <?= adminNavItem('messages.php', 'bi-envelope', 'Messages', $currentAdminPage, $unreadMsgs > 0 ? (string) $unreadMsgs : '') ?>
            <?= adminNavItem('reviews.php', 'bi-star', 'Reviews', $currentAdminPage) ?>

            <li class="sidebar-section-title">Analytics</li>
            <?= adminNavItem('sales.php', 'bi-graph-up', 'Sales', $currentAdminPage) ?>
            <?= adminNavItem('reports.php', 'bi-bar-chart-line', 'Reports', $currentAdminPage) ?>

            <li class="sidebar-section-title">System</li>
            <?= adminNavItem('staff.php', 'bi-person-badge', 'Staff', $currentAdminPage) ?>
            <?= adminNavItem('settings.php', 'bi-gear', 'Settings', $currentAdminPage) ?>

        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= SITE_URL ?>" class="sidebar-link" target="_blank">
            <i class="bi bi-shop sidebar-icon"></i>
            <span class="sidebar-label">View Store</span>
        </a>
        <a href="<?= SITE_URL ?>/logout.php" class="sidebar-link" style="color:#dc3545">
            <i class="bi bi-box-arrow-right sidebar-icon"></i>
            <span class="sidebar-label">Logout</span>
        </a>
    </div>

</aside>