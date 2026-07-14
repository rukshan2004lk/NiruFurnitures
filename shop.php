<?php
/**
 * NiRu-Furnitures — Shop / Product Listing (Redesigned)
 */
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Category emoji mapping
$catEmojis = [
    'living-room'    => '🛋️',
    'bedroom'        => '🛏️',
    'dining-room'    => '🍽️',
    'dining-kitchen' => '🍽️',
    'office'         => '🖥️',
    'outdoor'        => '🌿',
    'kids'           => '🧸',
    'kitchen'        => '🍳',
    'bathroom'       => '🚿',
];

$categorySlugs = [];
if (isset($_GET['category'])) {
    $cats = $_GET['category'];
    $categorySlugs = is_array($cats) ? array_map('sanitize', $cats) : [sanitize($cats)];
    $categorySlugs = array_filter($categorySlugs);
}

$searchQuery = sanitize($_GET['q'] ?? '');
$sortBy      = sanitize($_GET['sort'] ?? 'newest');
$priceMin    = (float) ($_GET['price_min'] ?? 0);
$priceMax    = (float) ($_GET['price_max'] ?? 500000);
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$perPage     = 9;

$where  = ["p.status = 'active'"];
$params = [];
$types  = '';
$cat    = null;

if (!empty($categorySlugs)) {
    if (count($categorySlugs) === 1) {
        $cat = dbFetchOne("SELECT * FROM categories WHERE slug = ?", 's', $categorySlugs[0]);
    }
    $placeholders = str_repeat('?,', count($categorySlugs) - 1) . '?';
    $where[] = "c.slug IN ($placeholders)";
    foreach ($categorySlugs as $slug) { $params[] = $slug; $types .= 's'; }
}
if ($searchQuery) {
    $like = '%' . $searchQuery . '%';
    $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = $like; $params[] = $like; $types .= 'ss';
}
if ($priceMin > 0) { $where[] = "COALESCE(p.sale_price, p.price) >= ?"; $params[] = $priceMin; $types .= 'd'; }
if ($priceMax > 0 && $priceMax < 500000) { $where[] = "COALESCE(p.sale_price, p.price) <= ?"; $params[] = $priceMax; $types .= 'd'; }

$whereSQL = 'WHERE ' . implode(' AND ', $where);

$orderSQL = match ($sortBy) {
    'price_asc'  => 'ORDER BY COALESCE(p.sale_price, p.price) ASC',
    'price_desc' => 'ORDER BY COALESCE(p.sale_price, p.price) DESC',
    'name_asc'   => 'ORDER BY p.name ASC',
    'popular'    => 'ORDER BY p.views DESC',
    'sales'      => 'ORDER BY p.sale_price IS NULL ASC, p.sale_price ASC',
    default      => 'ORDER BY p.created_at DESC',
};

$totalRow = dbFetchOne(
    "SELECT COUNT(p.id) AS total FROM products p JOIN categories c ON c.id = p.category_id $whereSQL",
    $types, ...$params
);
$total = (int) ($totalRow['total'] ?? 0);
$pag   = paginate($total, $perPage, $currentPage);

$products = dbFetchAll(
    "SELECT p.id, p.name, p.price, p.sale_price, p.images, p.featured, p.description, p.views,
            c.name AS category_name, c.slug AS category_slug,
            ROUND(COALESCE((SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.id), 4.5), 1) AS avg_rating
     FROM products p
     JOIN categories c ON c.id = p.category_id
     $whereSQL $orderSQL
     LIMIT {$perPage} OFFSET {$pag['offset']}",
    $types, ...$params
);

$allCategories = dbFetchAll(
    "SELECT c.*, COUNT(p.id) AS cnt FROM categories c
     LEFT JOIN products p ON p.category_id = c.id AND p.status='active'
     GROUP BY c.id ORDER BY c.sort_order"
);

$pageTitle       = ($cat['name'] ?? 'Our Products') . ' — NiRu-Furnitures';
$pageDescription = 'Discover handcrafted furniture for your home. Browse our complete collection.';
$extraJs         = [SITE_URL . '/assets/js/filter.js'];

include 'includes/head.php';
include 'includes/navbar.php';
?>

<style>
/* ── Shop Page Redesign ──────────────────────────────────── */
body { background: #F4EFE9; }

.shop-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2.5rem 1.5rem 5rem;
    display: flex;
    gap: 2.5rem;
    align-items: flex-start;
}

/* ── Sidebar ─────────────────────────────────────────────── */
.shop-sidebar {
    width: 210px;
    flex-shrink: 0;
    position: sticky;
    top: 80px;
}

.sidebar-section { margin-bottom: 2rem; }

.sidebar-heading {
    font-family: 'Poppins', sans-serif;
    font-size: .8rem;
    font-weight: 800;
    color: #3d2219;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: .9rem;
}

/* Category Pills */
.cat-pills { display: flex; flex-direction: column; gap: .5rem; }

.cat-pill-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .4rem;
    padding: .45rem .9rem;
    border-radius: 20px;
    border: 1.5px solid #e2d9d3;
    background: #fff;
    cursor: pointer;
    transition: all .18s;
    font-size: .82rem;
    font-weight: 500;
    color: #5a3e38;
    line-height: 1.3;
    user-select: none;
}
.cat-pill-label:hover { border-color: #9b6b55; background: #fdf8f5; }
.cat-pill-label.active {
    background: #3d2219;
    border-color: #3d2219;
    color: #fff;
}
.cat-pill-label .cat-count {
    font-size: .7rem;
    opacity: .65;
    font-weight: 400;
}
/* Hide actual checkbox */
.cat-pill-cb { display: none; }

/* Price Range */
.price-range-labels {
    display: flex;
    justify-content: space-between;
    font-size: .75rem;
    color: #9b8076;
    margin-bottom: .4rem;
}
.price-slider {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 4px;
    border-radius: 2px;
    background: linear-gradient(to right, #3d2219 0%, #3d2219 50%, #ddd6d0 50%);
    outline: none;
    cursor: pointer;
    transition: background .2s;
}
.price-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 16px; height: 16px;
    border-radius: 50%;
    background: #3d2219;
    border: 2px solid #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,.25);
    cursor: pointer;
}
.price-slider::-moz-range-thumb {
    width: 16px; height: 16px;
    border-radius: 50%;
    background: #3d2219;
    border: 2px solid #fff;
    cursor: pointer;
}

/* Eco-Choice Box */
.eco-box {
    background: linear-gradient(135deg, #e8f0df 0%, #d4e6c3 100%);
    border-radius: 14px;
    padding: 1rem 1.1rem;
    border: 1px solid #b8d4a0;
    margin-top: .5rem;
}
.eco-box-header {
    display: flex;
    align-items: center;
    gap: .4rem;
    font-size: .82rem;
    font-weight: 800;
    color: #3a5c2a;
    margin-bottom: .4rem;
}
.eco-box p {
    font-size: .75rem;
    color: #4a6e38;
    line-height: 1.5;
    margin: 0;
}

/* ── Main Content ─────────────────────────────────────────── */
.shop-main { flex: 1; min-width: 0; }

.shop-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 1.8rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.shop-title-block h1 {
    font-family: 'Poppins', sans-serif;
    font-size: 1.75rem;
    font-weight: 800;
    color: #1e1209;
    margin: 0 0 .2rem;
}
.shop-title-block p {
    font-size: .85rem;
    color: #9b8076;
    margin: 0;
}

/* Sort Tabs */
.sort-tabs { display: flex; gap: .4rem; }
.sort-tab {
    padding: .4rem 1rem;
    border-radius: 20px;
    border: 1.5px solid #e2d9d3;
    background: #fff;
    font-size: .8rem;
    font-weight: 600;
    color: #5a3e38;
    cursor: pointer;
    transition: all .18s;
    line-height: 1.3;
}
.sort-tab:hover { border-color: #9b6b55; }
.sort-tab.active {
    background: #3d2219;
    border-color: #3d2219;
    color: #fff;
}

/* ── Product Card ─────────────────────────────────────────── */
.product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }

@media (max-width: 900px) { .product-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 580px) { .product-grid { grid-template-columns: 1fr; } }

.p-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(74,44,36,.06);
    transition: box-shadow .2s, transform .2s;
    display: flex;
    flex-direction: column;
}
.p-card:hover { box-shadow: 0 6px 24px rgba(74,44,36,.12); transform: translateY(-2px); }

.p-card-img-wrap {
    position: relative;
    aspect-ratio: 4/3;
    overflow: hidden;
    background: #f9f5f1;
}
.p-card-img-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .4s ease;
    display: block;
}
.p-card:hover .p-card-img-wrap img { transform: scale(1.04); }

.eco-badge {
    position: absolute;
    top: .6rem;
    left: .6rem;
    background: #3a5c2a;
    color: #fff;
    font-size: .65rem;
    font-weight: 700;
    padding: .22rem .55rem;
    border-radius: 12px;
    letter-spacing: .02em;
}
.p-wishlist-btn {
    position: absolute;
    top: .6rem;
    right: .6rem;
    width: 32px; height: 32px;
    border-radius: 50%;
    background: rgba(255,255,255,.9);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: .85rem;
    color: #5a3e38;
    transition: all .2s;
    box-shadow: 0 1px 4px rgba(0,0,0,.12);
}
.p-wishlist-btn:hover { background: #fff; color: #c0392b; transform: scale(1.1); }
.p-wishlist-btn.active { color: #c0392b; }

.p-card-body {
    padding: .95rem 1rem 1rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.p-card-meta {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: .4rem;
    margin-bottom: .3rem;
}
.p-card-name {
    font-family: 'Poppins', sans-serif;
    font-size: .88rem;
    font-weight: 700;
    color: #1e1209;
    margin: 0;
    line-height: 1.35;
    flex: 1;
}
.p-card-rating {
    font-size: .75rem;
    color: #b8860b;
    white-space: nowrap;
    font-weight: 600;
    margin-top: .05rem;
}
.p-card-desc {
    font-size: .78rem;
    color: #9b8076;
    margin: 0 0 .75rem;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.p-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    margin-top: auto;
}
.p-card-price {
    font-family: 'Poppins', sans-serif;
    font-size: .95rem;
    font-weight: 800;
    color: #1e1209;
}
.p-card-price .price-original {
    font-size: .75rem;
    font-weight: 400;
    color: #b0a09a;
    text-decoration: line-through;
    margin-left: .3rem;
}
.btn-view {
    background: #3d2219;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: .38rem .85rem;
    font-size: .75rem;
    font-weight: 700;
    text-decoration: none;
    transition: background .18s;
    white-space: nowrap;
}
.btn-view:hover { background: #5a3228; color: #fff; }

/* No Results */
.no-results-box {
    grid-column: 1/-1;
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border-radius: 16px;
}
.no-results-box i { font-size: 3rem; color: #ddc9b8; display: block; margin-bottom: .8rem; }
.no-results-box h5 { font-family: 'Poppins', sans-serif; color: #3d2219; font-weight: 700; }
.no-results-box p { color: #9b8076; }

/* ── Pagination ───────────────────────────────────────────── */
.shop-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: .4rem;
    margin-top: 2.5rem;
}
.shop-pagination .pg-btn {
    width: 38px; height: 38px;
    border-radius: 50%;
    border: 1.5px solid #e2d9d3;
    background: #fff;
    color: #5a3e38;
    font-size: .82rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all .18s;
    text-decoration: none;
}
.shop-pagination .pg-btn:hover { border-color: #9b6b55; background: #fdf8f5; }
.shop-pagination .pg-btn.active {
    background: #3d2219;
    border-color: #3d2219;
    color: #fff;
}
.shop-pagination .pg-btn.arrow { font-size: 1rem; }

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width: 768px) {
    .shop-wrapper { flex-direction: column; gap: 1.5rem; padding: 1.5rem 1rem 3rem; }
    .shop-sidebar { width: 100%; position: static; }
    .cat-pills { flex-direction: row; flex-wrap: wrap; }
    .cat-pill-label { padding: .35rem .75rem; }
    .product-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>

<div class="shop-wrapper">

    <!-- ── Sidebar ──────────────────────────────────────────── -->
    <aside class="shop-sidebar">
        <form id="filterForm" method="GET" action="shop.php">
            <?php if ($searchQuery): ?>
                <input type="hidden" name="q" value="<?= e($searchQuery) ?>">
            <?php endif; ?>
            <input type="hidden" id="filterPageInput" name="page" value="1">
            <input type="hidden" id="sortInput" name="sort" value="<?= e($sortBy) ?>">
            <input type="hidden" name="price_min" value="0">

            <!-- Categories -->
            <div class="sidebar-section">
                <div class="sidebar-heading">Categories</div>
                <div class="cat-pills">
                    <!-- All -->
                    <label class="cat-pill-label <?= empty($categorySlugs) ? 'active' : '' ?>" id="catAllLabel">
                        <input class="cat-pill-cb" type="checkbox" id="catAll"
                               <?= empty($categorySlugs) ? 'checked' : '' ?>>
                        <span>All Collections!</span>
                    </label>

                    <?php foreach ($allCategories as $cat2):
                        $emoji = $catEmojis[$cat2['slug']] ?? '';
                        $isActive = in_array($cat2['slug'], $categorySlugs);
                    ?>
                    <label class="cat-pill-label <?= $isActive ? 'active' : '' ?>"
                           for="cat_<?= $cat2['id'] ?>">
                        <input class="cat-pill-cb" type="checkbox" name="category[]"
                               id="cat_<?= $cat2['id'] ?>"
                               value="<?= e($cat2['slug']) ?>"
                               <?= $isActive ? 'checked' : '' ?>>
                        <span><?= e($cat2['name']) ?> <?= $emoji ?></span>
                        <span class="cat-count"><?= $cat2['cnt'] ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Price Range -->
            <div class="sidebar-section">
                <div class="sidebar-heading">Price Range</div>
                <div class="price-range-labels">
                    <span>Rs. 0</span>
                    <span id="priceMaxLabel">Rs. <?= number_format($priceMax) ?>+</span>
                </div>
                <input type="range" class="price-slider" id="priceMax" name="price_max"
                       min="0" max="500000" step="5000" value="<?= $priceMax ?>">
            </div>

        </form>

        <!-- Eco-Choice Box (decorative) -->
        <div class="eco-box">
            <div class="eco-box-header">
                <span>🌿</span> Eco-Choice
            </div>
            <p>Sustainable materials sourced for longevity and comfort.</p>
        </div>
    </aside>

    <!-- ── Main Content ─────────────────────────────────────── -->
    <div class="shop-main">

        <!-- Header -->
        <div class="shop-header">
            <div class="shop-title-block">
                <h1>Our Products</h1>
                <p>Discover handcrafted comfort for your home.
                    <?php if ($searchQuery): ?>
                        &mdash; Results for "<strong><?= e($searchQuery) ?></strong>"
                    <?php endif; ?>
                </p>
            </div>

            <!-- Sort Tabs -->
            <div class="sort-tabs">
                <button type="button" class="sort-tab <?= $sortBy === 'newest' ? 'active' : '' ?>"
                        data-sort="newest">Newest</button>
                <button type="button" class="sort-tab <?= $sortBy === 'popular' ? 'active' : '' ?>"
                        data-sort="popular">Popular</button>
                <button type="button" class="sort-tab <?= ($sortBy === 'price_asc' || $sortBy === 'sales') ? 'active' : '' ?>"
                        data-sort="sales">Sales</button>
            </div>
        </div>

        <?= renderFlash() ?>

        <!-- Product Grid -->
        <div class="product-grid" id="productGrid">
            <?php 
            $userWishlist = getUserWishlistIds();
            if ($products): foreach ($products as $p):
                $images   = json_decode($p['images'] ?? '[]', true);
                $firstImg = $images[0] ?? null;
                $hasDisc  = $p['sale_price'] && $p['sale_price'] < $p['price'];
                $rating   = number_format((float)($p['avg_rating'] ?? 4.5), 1);
                $desc     = truncate(strip_tags($p['description'] ?? ''), 70);
                $isWishlisted = in_array($p['id'], $userWishlist);
            ?>
            <div class="p-card" data-aos="fade-up">
                <div class="p-card-img-wrap">
                    <a href="product.php?id=<?= $p['id'] ?>">
                        <img src="<?= productImageUrl($firstImg) ?>"
                             alt="<?= e($p['name']) ?>" loading="lazy">
                    </a>
                    <?php if ($p['featured']): ?>
                        <span class="eco-badge">Eco-Choice</span>
                    <?php endif; ?>
                    <button class="p-wishlist-btn <?= $isWishlisted ? 'active' : '' ?>" data-action="toggle-wishlist"
                            data-product-id="<?= $p['id'] ?>" aria-label="Wishlist">
                        <i class="bi <?= $isWishlisted ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                    </button>
                </div>
                <div class="p-card-body">
                    <div class="p-card-meta">
                        <h3 class="p-card-name"><?= e($p['name']) ?></h3>
                        <span class="p-card-rating">★ <?= $rating ?></span>
                    </div>
                    <?php if ($desc): ?>
                        <p class="p-card-desc"><?= e($desc) ?></p>
                    <?php endif; ?>
                    <div class="p-card-footer">
                        <div>
                            <?php if ($hasDisc): ?>
                                <span class="p-card-price">
                                    Rs. <?= number_format((float)$p['sale_price']) ?>
                                    <span class="price-original">Rs. <?= number_format((float)$p['price']) ?></span>
                                </span>
                            <?php else: ?>
                                <span class="p-card-price">Rs. <?= number_format((float)$p['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        <a href="product.php?id=<?= $p['id'] ?>" class="btn-view">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="no-results-box">
                <i class="bi bi-inbox"></i>
                <h5>No products found</h5>
                <p>Try different filters or search terms.</p>
                <a href="shop.php" class="btn-view" style="display:inline-flex;align-items:center;gap:.4rem">
                    <i class="bi bi-x-circle"></i> Clear Filters
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Result count (updated by AJAX) -->
        <p id="resultCount" style="font-size:.8rem;color:#9b8076;margin-top:.75rem;text-align:right">
            <?= $total ?> product<?= $total !== 1 ? 's' : '' ?> found
        </p>

        <!-- Loader -->
        <div id="filterLoader" class="d-none text-center py-4">
            <div class="spinner-border" style="color:#53362e;width:1.6rem;height:1.6rem" role="status">
                <span class="visually-hidden">Loading…</span>
            </div>
        </div>

        <!-- Pagination -->
        <div id="paginationWrap">
            <?php
            $pages = $pag['pages'];
            if ($pages > 1):
                $queryParams = ['sort' => $sortBy, 'price_max' => $priceMax];
                if ($searchQuery) $queryParams['q'] = $searchQuery;
                if (!empty($categorySlugs)) $queryParams['category'] = $categorySlugs;
            ?>
            <div class="shop-pagination">
                <!-- Prev -->
                <?php if ($currentPage > 1):
                    $queryParams['page'] = $currentPage - 1; ?>
                    <a class="pg-btn arrow" href="shop.php?<?= http_build_query($queryParams) ?>">&#8249;</a>
                <?php else: ?>
                    <span class="pg-btn arrow" style="opacity:.35;cursor:default">&#8249;</span>
                <?php endif; ?>

                <!-- Pages -->
                <?php for ($i = 1; $i <= $pages; $i++):
                    $queryParams['page'] = $i; ?>
                    <a class="pg-btn <?= $i === $currentPage ? 'active' : '' ?>"
                       href="shop.php?<?= http_build_query($queryParams) ?>"><?= $i ?></a>
                <?php endfor; ?>

                <!-- Next -->
                <?php if ($currentPage < $pages):
                    $queryParams['page'] = $currentPage + 1; ?>
                    <a class="pg-btn arrow" href="shop.php?<?= http_build_query($queryParams) ?>">&#8250;</a>
                <?php else: ?>
                    <span class="pg-btn arrow" style="opacity:.35;cursor:default">&#8250;</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

    </div><!-- /shop-main -->

</div><!-- /shop-wrapper -->

<?php include 'includes/footer.php'; ?>