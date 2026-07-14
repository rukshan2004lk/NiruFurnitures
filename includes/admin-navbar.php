<?php
/**
 * NiRu-Furnitures — Admin Navbar
 */
requireAdmin();

$pendingOrders = dbFetchOne("SELECT COUNT(*) AS c FROM orders   WHERE status = 'pending'")['c'] ?? 0;
$unreadMessages = dbFetchOne("SELECT COUNT(*) AS c FROM messages WHERE is_read = 0")['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin — ' . SITE_NAME) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>

<body class="admin-body">

    <!-- ═══ Top Navbar ═══ -->
    <nav class="admin-topnav" id="adminTopNav">
        <div class="container-fluid">

            <button class="sidebar-toggle me-2" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="bi bi-list fs-5"></i>
            </button>

            <a class="admin-brand me-3" href="<?= SITE_URL ?>/admin/dashboard.php">
                <i class="bi bi-shield-check me-1 text-primary"></i>NiRu <span class="text-primary">Admin</span>
            </a>

            <div class="ms-auto d-flex align-items-center gap-1 gap-sm-2">

                <a href="<?= SITE_URL ?>" class="btn btn-sm btn-outline-secondary d-none d-lg-inline-flex"
                    target="_blank">
                    <i class="bi bi-box-arrow-up-right me-1"></i>View Site
                </a>

                <a href="messages.php" class="admin-icon-btn" aria-label="Messages">
                    <i class="bi bi-envelope fs-5"></i>
                    <?php if ($unreadMessages > 0): ?>
                        <span class="badge-dot"><?= $unreadMessages ?></span>
                    <?php endif; ?>
                </a>

                <a href="orders.php" class="admin-icon-btn" aria-label="Pending Orders">
                    <i class="bi bi-bell fs-5"></i>
                    <?php if ($pendingOrders > 0): ?>
                        <span class="badge-dot badge-dot-warning"><?= $pendingOrders ?></span>
                    <?php endif; ?>
                </a>

                <div class="dropdown">
                    <button class="btn admin-user-btn d-flex align-items-center gap-2 dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?= avatarUrl($_SESSION['user_avatar'] ?? null) ?>" class="admin-avatar" alt="Admin">
                        <span
                            class="d-none d-sm-inline"><?= e(explode(' ', $_SESSION['user_name'] ?? 'Admin')[0]) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li class="px-3 py-2">
                            <strong class="d-block small"><?= e($_SESSION['user_name'] ?? '') ?></strong>
                            <span class="text-muted"
                                style="font-size:.78rem"><?= e($_SESSION['user_email'] ?? '') ?></span>
                        </li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li><a class="dropdown-item small" href="settings.php"><i
                                    class="bi bi-gear me-2"></i>Settings</a></li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li><a class="dropdown-item small text-danger" href="<?= SITE_URL ?>/logout.php"><i
                                    class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </nav>

    <!-- Mobile sidebar overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ═══ Layout wrapper ═══ -->
    <div class="admin-layout" id="adminLayout">