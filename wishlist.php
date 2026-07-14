<?php
/**
 * NiRu-Furnitures — Wishlist
 */
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$wishlistItems = dbFetchAll(
    "SELECT w.id AS wish_id, p.id AS product_id, p.name, p.price, p.sale_price, p.stock, p.images, c.name AS category_name
     FROM wishlist w
     JOIN products p ON p.id = w.product_id
     JOIN categories c ON c.id = p.category_id
     WHERE w.user_id = ?
     ORDER BY w.created_at DESC",
    'i',
    currentUserId()
);

$pageTitle = 'My Wishlist — NiRu-Furnitures';
include 'includes/head.php';
include 'includes/navbar.php';
?>

<style>
    .page-breadcrumb .breadcrumb-item a {
        color: #7d4f3f;
    }

    .page-breadcrumb .breadcrumb-item.active { color: #776965; }

    .wishlist-section {
        padding: 3rem 0 5rem;
        background: #f4f1ef;
    }

    /* reuse product-card but apply warm palette */
    .wishlist-section .product-card {
        border-color: #e0d8d0;
        border-radius: 14px;
    }

    .wishlist-section .product-card:hover {
        box-shadow: 0 10px 30px rgba(74, 44, 36, .12);
        border-color: transparent;
    }

    .wishlist-section .product-category-tag {
        color: #9b8076;
    }

    .wishlist-section .product-name {
        color: #3d2219;
    }

    .wishlist-section .btn-add-to-cart {
        background: #53362e;
        border: none;
        border-radius: 8px;
        color: #fff;
        font-size: .83rem;
        font-weight: 700;
        padding: .55rem 1rem;
        width: 100%;
        margin-top: .75rem;
        transition: background 200ms;
    }

    .wishlist-section .btn-add-to-cart:hover {
        background: #3d2219;
    }

    .wishlist-section .btn-oos {
        background: #f4f1ef;
        border: 1px solid #ddd5cc;
        border-radius: 8px;
        color: #9b8076;
        font-size: .83rem;
        padding: .55rem 1rem;
        width: 100%;
        margin-top: .75rem;
    }

    .wishlist-remove-btn {
        position: absolute;
        top: .75rem;
        right: .75rem;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .92);
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #dc3545;
        font-size: 1rem;
        cursor: pointer;
        transition: all 180ms;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .1);
        z-index: 2;
    }

    .wishlist-remove-btn:hover {
        background: #dc3545;
        color: #fff;
    }

    .wishlist-empty {
        text-align: center;
        padding: 6rem 1rem;
        background: #fff;
        border-radius: 20px;
        border: 1px solid #ded8d1;
    }

    .wishlist-empty .empty-icon {
        font-size: 4rem;
        color: #ddc9b8;
        display: block;
        margin-bottom: 1.25rem;
    }

    .wishlist-empty h3 {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        color: #3d2219;
        margin-bottom: .6rem;
    }

    .wishlist-empty p {
        color: #776965;
        margin-bottom: 1.75rem;
    }

    .btn-shop-now {
        background: #53362e;
        border: none;
        border-radius: 10px;
        color: #fff;
        font-weight: 700;
        font-size: .9rem;
        padding: .8rem 2.5rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        transition: background 200ms;
    }

    .btn-shop-now:hover {
        background: #3d2219;
        color: #fff;
    }
</style>


<!-- Hero -->
<section class="page-hero" aria-label="My Wishlist">
    <div class="container-xl">
        <span class="hero-label">Saved for Later</span>
        <h1>My Wishlist</h1>
        <p><?= count($wishlistItems) ?> item<?= count($wishlistItems) !== 1 ? 's' : '' ?> saved <?php if ($wishlistItems): ?>&nbsp;&mdash;&nbsp;<a href="shop.php" style="color:#f5ddc8;font-weight:700;text-decoration:underline">Continue Shopping</a><?php endif; ?></p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Wishlist</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Wishlist Grid -->
<section class="wishlist-section">
    <div class="container-xl">
        <?= renderFlash() ?>

        <?php if ($wishlistItems): ?>
            <div class="row g-4" id="wishlistGrid">
                <?php foreach ($wishlistItems as $i => $item):
                    $images = json_decode($item['images'] ?? '[]', true);
                    $imgUrl = productImageUrl($images[0] ?? null);
                    $hasDiscount = $item['sale_price'] && $item['sale_price'] < $item['price'];
                    ?>
                    <div class="col-xl-3 col-lg-4 col-md-6" id="wishItem_<?= $item['wish_id'] ?>" data-aos="fade-up"
                        data-aos-delay="<?= ($i % 4) * 70 ?>">
                        <div class="product-card" style="position:relative">
                            <div class="product-card-img-wrap">
                                <a href="product.php?id=<?= $item['product_id'] ?>">
                                    <img src="<?= $imgUrl ?>" class="product-card-img" alt="<?= e($item['name']) ?>"
                                        loading="lazy">
                                </a>
                                <?php if ($hasDiscount): ?>
                                    <span class="product-badge badge-sale">Sale</span>
                                <?php endif; ?>
                                <button class="wishlist-remove-btn"
                                    onclick="removeFromWishlist(<?= $item['wish_id'] ?>, <?= $item['product_id'] ?>)"
                                    aria-label="Remove from wishlist">
                                    <i class="bi bi-heart-fill"></i>
                                </button>
                            </div>
                            <div class="product-card-body">
                                <span class="product-category-tag"><?= e($item['category_name']) ?></span>
                                <a href="product.php?id=<?= $item['product_id'] ?>" class="text-decoration-none">
                                    <h3 class="product-name"><?= e($item['name']) ?></h3>
                                </a>
                                <div style="margin-bottom:.25rem">
                                    <?php if ($hasDiscount): ?>
                                        <span class="price-sale fw-bold"
                                            style="color:#53362e"><?= formatPrice((float) $item['sale_price']) ?></span>
                                        <del class="text-muted small ms-1"><?= formatPrice((float) $item['price']) ?></del>
                                    <?php else: ?>
                                        <span class="fw-bold" style="color:#53362e"><?= formatPrice((float) $item['price']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($item['stock'] > 0): ?>
                                    <button class="btn-add-to-cart" data-action="add-to-cart"
                                        data-product-id="<?= $item['product_id'] ?>">
                                        <i class="bi bi-bag-plus me-1"></i>Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button class="btn-oos" disabled>Out of Stock</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="wishlist-empty">
                <i class="bi bi-heart empty-icon"></i>
                <h3>Your wishlist is empty</h3>
                <p>Save items you love while you browse the shop.</p>
                <a href="shop.php" class="btn-shop-now"><i class="bi bi-bag-heart"></i>Explore Products</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    const CSRF = '<?= csrfToken() ?>';

    async function removeFromWishlist(wishId, productId) {
        const res = await fetch('api/products.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'toggle_wishlist', product_id: productId, csrf_token: CSRF }),
        });
        const data = await res.json();
        if (data.success) {
            const el = document.getElementById('wishItem_' + wishId);
            el.style.opacity = '0';
            el.style.transition = 'opacity 300ms';
            setTimeout(() => { el.remove(); window.showToast?.('Removed from wishlist.', 'info'); }, 300);
        }
    }
</script>

<?php include 'includes/footer.php'; ?>