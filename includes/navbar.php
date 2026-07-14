<?php
/**
 * NiRu-Furnitures — Public Navigation Bar
 * -------------------------------------------------------
 * Responsive sticky navbar with:
 *  - Logo
 *  - Main nav links
 *  - Live search bar
 *  - Cart badge
 *  - Wishlist badge
 *  - User dropdown (or Login/Register)
 */
$cartCount = getCartCount();
$wishlistCount = getWishlistCount();
$currentPath = basename($_SERVER['PHP_SELF']);
$isHomePage = $currentPath === 'index.php';

function navLink(string $href, string $label, string $current): string
{
    $active = basename($href) === $current ? 'active' : '';
    return '<li class="nav-item"><a class="nav-link ' . $active . '" href="' . $href . '">' . $label . '</a></li>';
}
?>

<!-- ═══ Topbar ═══ -->
<div class="topbar d-none d-md-block <?= $isHomePage ? 'home-topbar' : '' ?>">
    <div class="container-xl d-flex justify-content-between align-items-center">
        <span class="topbar-info">
            <i class="bi bi-telephone-fill me-1"></i>
            <?= e(getSetting('site_phone', '+94 77 000 0000')) ?>
            &nbsp;|&nbsp;
            <i class="bi bi-envelope-fill me-1"></i>
            <?= e(getSetting('site_email', 'info@nirufurnitures.com')) ?>
        </span>
        <span class="topbar-right">
            <?php if (!empty(getSetting('facebook_url'))): ?>
                <a href="<?= e(getSetting('facebook_url')) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i
                        class="bi bi-facebook"></i></a>
            <?php endif; ?>
            <?php if (!empty(getSetting('instagram_url'))): ?>
                <a href="<?= e(getSetting('instagram_url')) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i
                        class="bi bi-instagram"></i></a>
            <?php endif; ?>
            <a href="faq.php"><i class="bi bi-question-circle me-1"></i>Help</a>
        </span>
    </div>
</div>

<!-- ═══ Main Navbar ═══ -->
<nav id="mainNavbar" class="navbar navbar-expand-lg navbar-light sticky-top <?= $isHomePage ? 'home-navbar' : '' ?>"
    aria-label="Main navigation">
    <div class="container-xl">

        <!-- Logo -->
        <a class="navbar-brand text-dark fw-bold" href="<?= SITE_URL ?>">
            <span class="brand-text text-dark" style="font-size:1.8rem; letter-spacing: -1px;">NiRu</span>
        </a>

        <!-- Mobile Icons -->
        <div class="d-flex d-lg-none align-items-center gap-2 me-2">
            <a href="cart.php" class="navbar-icon-btn position-relative" aria-label="Cart">
                <i class="bi bi-bag"></i>
                <?php if ($cartCount > 0): ?>
                    <span class="cart-badge"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"
                aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <!-- Collapsible Menu -->
        <div class="collapse navbar-collapse" id="navMenu">

            <!-- Nav Links -->
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-4">
                <?= navLink(SITE_URL . '/shop.php', 'Products', $currentPath) ?>
                <?= navLink(SITE_URL . '/dashboard.php', 'Dashboard', $currentPath) ?>
                <?= navLink(SITE_URL . '/contact.php', 'Contact', $currentPath) ?>
            </ul>

            <!-- Right Action Icons (desktop) -->
            <div class="d-none d-lg-flex align-items-center gap-3">

                <!-- Search -->
                <form class="search-form d-flex m-0" action="shop.php" method="GET" role="search">
                    <div class="search-wrapper">
                        <i class="bi bi-search position-absolute text-muted" style="left:12px;"></i>
                        <input type="search" id="liveSearch" name="q"
                            class="form-control search-input bg-light border-0" placeholder="Search..."
                            value="<?= e($_GET['q'] ?? '') ?>" autocomplete="off" aria-label="Search"
                            style="padding-left: 2.2rem; border-radius: 50px;">
                    </div>
                    <div id="searchDropdown" class="search-dropdown" hidden></div>
                </form>

                <!-- Cart -->
                <a href="cart.php" class="navbar-icon-btn position-relative" aria-label="Shopping Cart">
                    <i class="bi bi-cart3"></i>
                    <?php if ($cartCount > 0): ?>
                        <span class="cart-badge"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>

                <!-- User Dropdown -->
                <?php if (isLoggedIn()): ?>
                    <div class="dropdown ms-1">
                        <button class="btn user-avatar-btn dropdown-toggle d-flex align-items-center gap-2"
                            id="userDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= avatarUrl($_SESSION['user_avatar'] ?? null) ?>"
                                alt="<?= e($_SESSION['user_name'] ?? 'User') ?>" class="user-avatar-small">
                            <span class="d-none d-xl-inline"><?= e(explode(' ', $_SESSION['user_name'] ?? 'User')[0]) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userDropdown">
                            <li class="dropdown-header">
                                <strong><?= e($_SESSION['user_name'] ?? '') ?></strong>
                                <small class="d-block text-muted"><?= e($_SESSION['user_email'] ?? '') ?></small>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="dashboard.php"><i
                                        class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a>
                            </li>
                            <li><a class="dropdown-item" href="wishlist.php"><i class="bi bi-heart me-2"></i>Wishlist</a>
                            </li>
                            <?php if (isAdmin()): ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-primary" href="<?= SITE_URL ?>/admin/dashboard.php"><i
                                            class="bi bi-shield-check me-2"></i>Admin Panel</a></li>
                            <?php endif; ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i
                                        class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="navbar-icon-btn" aria-label="Account">
                        <i class="bi bi-person-circle"></i>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile: user links -->
            <div class="d-lg-none border-top mt-2 pt-2">
                <?php if (isLoggedIn()): ?>
                    <a class="dropdown-item py-2" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                    <a class="dropdown-item py-2" href="wishlist.php"><i class="bi bi-heart me-2"></i>Wishlist</a>
                    <a class="dropdown-item py-2 text-danger" href="logout.php"><i
                            class="bi bi-box-arrow-right me-2"></i>Logout</a>
                <?php else: ?>
                    <a class="btn btn-outline-light btn-sm me-2 mb-1" href="login.php">Login</a>
                    <a class="btn btn-primary btn-sm mb-1" href="register.php">Register</a>
                <?php endif; ?>
            </div>

        </div><!-- /collapse -->
    </div><!-- /container -->
</nav>
<!-- ═══ /Navbar ═══ -->