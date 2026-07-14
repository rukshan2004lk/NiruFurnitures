<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$id = (int)($_GET["id"] ?? 0);
if (!$id) { redirect("shop.php"); }

$product = dbFetchOne("SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN categories c ON c.id = p.category_id WHERE p.id = ? AND p.status = 'active' LIMIT 1", "i", $id);
if (!$product) { redirect("shop.php"); }

dbExecute("UPDATE products SET views = views + 1 WHERE id = ?", "i", $id);

$images    = json_decode($product["images"] ?? "[]", true) ?: [];
$related   = dbFetchAll("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.category_id = ? AND p.id != ? AND p.status = 'active' LIMIT 4", "ii", $product["category_id"], $id);
$reviews   = dbFetchAll("SELECT r.*, u.name AS user_name, u.avatar FROM reviews r JOIN users u ON u.id = r.user_id WHERE r.product_id = ? AND r.approved = 1 ORDER BY r.created_at DESC", "i", $id);
$avgRating = dbFetchOne("SELECT AVG(rating) AS avg_r, COUNT(*) AS total FROM reviews WHERE product_id = ? AND approved = 1", "i", $id);

$inWishlist = false;
if (isLoggedIn()) {
    $wRow = dbFetchOne("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?", "ii", currentUserId(), $id);
    $inWishlist = (bool) $wRow;
}

$hasDiscount  = $product["sale_price"] && $product["sale_price"] < $product["price"];
$displayPrice = $hasDiscount ? $product["sale_price"] : $product["price"];

$pageTitle       = e($product["name"]) . " � NiRu-Furnitures";
$pageDescription = truncate(strip_tags($product["description"] ?? ""), 155);

include "includes/head.php";
include "includes/navbar.php";
?>

<style>
.product-page{padding:3rem 0 5rem;background:#f4f1ef}
.gallery-main{border-radius:16px;overflow:hidden;background:#fff;border:1px solid #ded8d1;aspect-ratio:4/3;box-shadow:0 2px 10px rgba(74,44,36,.07);cursor:zoom-in;flex-grow:1;min-width:0}
.gallery-main img{width:100%;height:100%;object-fit:cover;transition:transform 400ms ease}
.gallery-main:hover img{transform:scale(1.03)}
.thumb-strip{display:flex;gap:.6rem;flex-wrap:wrap;flex-shrink:0}
@media (min-width:992px){.thumb-strip{flex-direction:column;flex-wrap:nowrap;overflow-y:auto;padding-right:4px;max-height:500px}.thumb-strip::-webkit-scrollbar{width:4px}.thumb-strip::-webkit-scrollbar-thumb{background:#ded8d1;border-radius:4px}}
.thumb-btn{width:72px;height:72px;border:2px solid #ddd5cc;border-radius:10px;overflow:hidden;padding:0;background:#f4f1ef;cursor:pointer;transition:border-color 180ms;flex-shrink:0}
.thumb-btn.active,.thumb-btn:hover{border-color:#7d4f3f}
.thumb-btn img{width:100%;height:100%;object-fit:cover}
.product-detail-card{background:#fff;border-radius:16px;border:1px solid #ded8d1;padding:2rem 2.25rem;box-shadow:0 2px 10px rgba(74,44,36,.05)}
.product-category-pill{display:inline-block;font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#9b6b55;background:#f4ede6;border-radius:99px;padding:3px 12px;margin-bottom:.85rem}
.product-detail-title{font-family:"Poppins",sans-serif;font-size:clamp(1.4rem,2.5vw,1.85rem);font-weight:700;color:#3d2219;line-height:1.25;margin-bottom:.75rem}
.price-main{font-family:"Poppins",sans-serif;font-size:2rem;font-weight:800;color:#53362e}
.price-orig{font-size:1.15rem;color:#9b8076;text-decoration:line-through;margin-left:.5rem}
.save-badge{background:#fee2e2;color:#c0392b;font-size:.72rem;font-weight:800;padding:3px 10px;border-radius:99px;margin-left:.5rem;vertical-align:middle}
.spec-row{display:flex;gap:.65rem;align-items:flex-start;margin-bottom:.65rem;font-size:.875rem}
.spec-row i{color:#9b6b55;font-size:1rem;margin-top:1px;flex-shrink:0}
.spec-label{font-weight:700;color:#4a3028;margin-right:.3rem}
.spec-val{color:#776965}
.btn-add-cart{background:#53362e;border:none;border-radius:10px;color:#fff;font-weight:700;font-size:.95rem;padding:.8rem 1.5rem;flex:1;transition:background 200ms}
.btn-add-cart:hover{background:#3d2219}
.btn-add-cart:disabled{background:#b0a09a;cursor:not-allowed}
.btn-wishlist{width:48px;height:48px;border-radius:10px;border:2px solid #ddd5cc;background:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#9b8076;transition:all 200ms;cursor:pointer;flex-shrink:0}
.btn-wishlist:hover,.btn-wishlist.wishlisted{border-color:#c07060;color:#c07060;background:#fff5f5}
.trust-pill{display:inline-flex;align-items:center;gap:.4rem;background:#f4f1ef;border:1px solid #ddd5cc;border-radius:99px;padding:5px 14px;font-size:.75rem;font-weight:600;color:#776965}
.product-tabs-card{background:#fff;border-radius:16px;border:1px solid #ded8d1;padding:2rem 2.25rem;box-shadow:0 2px 8px rgba(74,44,36,.05)}
.nav-pills .nav-link{border-radius:99px;font-weight:600;font-size:.875rem;color:#776965;padding:.5rem 1.25rem}
.nav-pills .nav-link.active{background:#53362e;color:#fff}
.nav-pills .nav-link:hover:not(.active){background:#f4ede6;color:#3d2219}
.review-card{background:#faf8f6;border-radius:14px;border:1px solid #ede8e3;padding:1.5rem;margin-bottom:1rem}
.review-avatar{width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid #ddc9b8}
.rating-summary-card{background:#f4ede6;border-radius:14px;padding:2rem;text-align:center;border:1px solid #ddc9b8}
.rating-big{font-family:"Poppins",sans-serif;font-size:3.5rem;font-weight:800;color:#3d2219;line-height:1}
.review-write-card{background:#fff;border-radius:14px;border:1px solid #ded8d1;padding:1.5rem;margin-top:1rem}
.review-write-card .form-control{border-radius:9px;border:1px solid #ddd5cc;background:#faf8f6;font-size:.875rem;color:#3d2219}
.review-write-card .form-control:focus{border-color:#9b6b55;box-shadow:0 0 0 3px rgba(155,107,85,.12);background:#fff}
.review-write-card .form-label{font-size:.83rem;font-weight:600;color:#4a3028}
.btn-submit-review{background:#53362e;border:none;border-radius:9px;color:#fff;font-weight:700;font-size:.875rem;padding:.65rem;width:100%;transition:background 200ms}
.btn-submit-review:hover{background:#3d2219}
.related-section{margin-top:3rem;padding-top:2.5rem;border-top:2px solid #ede8e3}
.related-section h3{font-family:"Poppins",sans-serif;font-weight:700;color:#3d2219;font-size:1.4rem;margin-bottom:1.5rem}
.out-of-stock-alert{background:#fff5f5;border:1px solid #f5c2c2;border-radius:12px;padding:1.1rem 1.25rem;color:#a0403a;font-weight:600;font-size:.9rem;margin-bottom:1.5rem}
</style>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                <li class="breadcrumb-item">
                    <a href="shop.php?category=<?= e($product['category_slug']) ?>">
                        <?= e($product['category_name']) ?>
                    </a>
                </li>
                <li class="breadcrumb-item active" style="color:#776965"><?= e($product['name']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<!-- Product Section -->
<section class="product-page">
    <div class="container-xl">
        <?= renderFlash() ?>
        <div class="row g-4 mb-4">

            <!-- Gallery -->
            <div class="col-lg-6 d-flex flex-column flex-lg-row gap-3 align-items-start">
                <?php if (count($images) > 1): ?>
                <div class="thumb-strip order-2 order-lg-1">
                    <?php foreach ($images as $i => $img): ?>
                    <button class="thumb-btn <?= $i === 0 ? 'active' : '' ?>"
                            onclick="setMainImage('<?= productImageUrl($img) ?>', this)"
                            aria-label="View image <?= $i + 1 ?>">
                        <img src="<?= productImageUrl($img) ?>" alt="Thumbnail <?= $i + 1 ?>" loading="lazy">
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="gallery-main order-1 order-lg-2 w-100" data-bs-toggle="modal" data-bs-target="#imageModal">
                    <img id="mainImage"
                         src="<?= productImageUrl($images[0] ?? null) ?>"
                         alt="<?= e($product['name']) ?>">
                </div>
            </div>

            <!-- Details -->
            <div class="col-lg-6">
                <div class="product-detail-card">

                    <span class="product-category-pill"><?= e($product['category_name']) ?></span>
                    <h1 class="product-detail-title"><?= e($product['name']) ?></h1>

                    <!-- Rating -->
                    <?php if ($avgRating['total'] > 0): ?>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span style="color:#d69200;font-size:1rem;letter-spacing:1px">
                            <?= starRating((float)$avgRating['avg_r']) ?>
                        </span>
                        <span style="font-size:.82rem;color:#9b8076">
                            <?= number_format((float)$avgRating['avg_r'], 1) ?>/5
                            &nbsp;&bull;&nbsp; <?= $avgRating['total'] ?> review<?= $avgRating['total'] != 1 ? 's' : '' ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <!-- Price -->
                    <div class="mb-3">
                        <?php if ($hasDiscount):
                            $savePct = round((1 - $product['sale_price'] / $product['price']) * 100); ?>
                        <span class="price-main"><?= formatPrice((float)$product['sale_price']) ?></span>
                        <span class="price-orig"><?= formatPrice((float)$product['price']) ?></span>
                        <span class="save-badge">Save <?= $savePct ?>%</span>
                        <?php else: ?>
                        <span class="price-main"><?= formatPrice((float)$product['price']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Stock -->
                    <div class="mb-3">
                        <?= stockBadge((int)$product['stock']) ?>
                        <?php if ($product['sku']): ?>
                        <span style="font-size:.78rem;color:#9b8076;margin-left:.75rem">SKU: <?= e($product['sku']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Short description -->
                    <?php if ($product['description']): ?>
                    <p style="font-size:.875rem;color:#776965;line-height:1.7;margin-bottom:1.25rem">
                        <?= nl2br(e(truncate(strip_tags($product['description']), 250))) ?>
                    </p>
                    <?php endif; ?>

                    <!-- Specs -->
                    <div class="mb-3">
                        <?php if ($product['material']): ?>
                        <div class="spec-row"><i class="bi bi-box-seam"></i><span><span class="spec-label">Material:</span><span class="spec-val"><?= e($product['material']) ?></span></span></div>
                        <?php endif; ?>
                        <?php if ($product['color']): ?>
                        <div class="spec-row"><i class="bi bi-palette"></i><span><span class="spec-label">Color:</span><span class="spec-val"><?= e($product['color']) ?></span></span></div>
                        <?php endif; ?>
                        <?php if ($product['dimensions']): ?>
                        <div class="spec-row"><i class="bi bi-rulers"></i><span><span class="spec-label">Dimensions:</span><span class="spec-val"><?= e($product['dimensions']) ?></span></span></div>
                        <?php endif; ?>
                        <?php if ($product['weight']): ?>
                        <div class="spec-row"><i class="bi bi-speedometer2"></i><span><span class="spec-label">Weight:</span><span class="spec-val"><?= e($product['weight']) ?> kg</span></span></div>
                        <?php endif; ?>
                    </div>

                    <?php if ((int)$product['stock'] > 0): ?>
                    <!-- Qty + Cart -->
                    <div class="d-flex gap-3 align-items-center mb-3">
                        <div class="qty-selector" style="flex-shrink:0">
                            <button type="button" class="qty-btn" data-qty="minus" aria-label="Decrease"><i class="bi bi-dash"></i></button>
                            <input type="number" id="productQty" class="qty-input" value="1" min="1" max="<?= min((int)$product['stock'], 10) ?>" aria-label="Quantity">
                            <button type="button" class="qty-btn" data-qty="plus" aria-label="Increase"><i class="bi bi-plus"></i></button>
                        </div>
                        <button class="btn-add-cart" data-action="add-to-cart" data-product-id="<?= $product['id'] ?>" id="addToCartBtn">
                            <i class="bi bi-bag-plus me-2"></i>Add to Cart
                        </button>
                        <button class="btn-wishlist <?= $inWishlist ? 'wishlisted' : '' ?>"
                                data-action="toggle-wishlist"
                                data-product-id="<?= $product['id'] ?>"
                                aria-label="Wishlist">
                            <i class="bi <?= $inWishlist ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                        </button>
                    </div>
                    <?php else: ?>
                    <div class="out-of-stock-alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        This product is currently out of stock.
                        <a href="contact.php" style="color:#7d4f3f;font-weight:700;margin-left:.35rem">Notify me</a>
                    </div>
                    <?php endif; ?>

                    <!-- Trust pills -->
                    <div class="d-flex flex-wrap gap-2 mt-1">
                        <span class="trust-pill"><i class="bi bi-truck"></i>Free Delivery</span>
                        <span class="trust-pill"><i class="bi bi-arrow-counterclockwise"></i>14-Day Returns</span>
                        <span class="trust-pill"><i class="bi bi-shield-check"></i>2-Yr Warranty</span>
                    </div>

                </div><!-- /detail-card -->
            </div>
        </div><!-- /row -->

        <!-- Tabs -->
        <div class="product-tabs-card mt-2">
            <ul class="nav nav-pills mb-4 gap-2" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabDescription" role="tab">
                        <i class="bi bi-info-circle me-1"></i>Description
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabReviews" role="tab">
                        <i class="bi bi-star me-1"></i>Reviews
                        <span class="badge ms-1" style="background:#53362e"><?= count($reviews) ?></span>
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Description -->
                <div class="tab-pane fade show active" id="tabDescription" role="tabpanel">
                    <?php if ($product['description']): ?>
                    <div style="font-size:.9rem;color:#776965;line-height:1.85">
                        <?= nl2br(e($product['description'])) ?>
                    </div>
                    <?php else: ?>
                    <p style="color:#9b8076">No description available for this product.</p>
                    <?php endif; ?>
                </div>

                <!-- Reviews -->
                <div class="tab-pane fade" id="tabReviews" role="tabpanel">
                    <div class="row g-4">
                        <!-- Summary + Write form -->
                        <div class="col-lg-4">
                            <div class="rating-summary-card">
                                <div class="rating-big">
                                    <?= $avgRating['total'] > 0 ? number_format((float)$avgRating['avg_r'], 1) : '�' ?>
                                </div>
                                <div style="color:#d69200;font-size:1.1rem;letter-spacing:2px;margin:.5rem 0">
                                    <?= starRating((float)($avgRating['avg_r'] ?? 0)) ?>
                                </div>
                                <div style="font-size:.8rem;color:#9b8076;font-weight:600">
                                    <?= $avgRating['total'] ?> review<?= $avgRating['total'] != 1 ? 's' : '' ?>
                                </div>
                            </div>

                            <?php if (isLoggedIn()): ?>
                            <div class="review-write-card">
                                <h6 style="font-family:'Poppins',sans-serif;font-weight:700;color:#3d2219;margin-bottom:1rem">Write a Review</h6>
                                <form method="POST" action="api/products.php" class="needs-validation" novalidate>
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="add_review">
                                    <input type="hidden" name="product_id" value="<?= $id ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Rating</label>
                                        <select name="rating" class="form-control form-select" required>
                                            <option value="">Select�</option>
                                            <?php for ($r = 5; $r >= 1; $r--): ?>
                                            <option value="<?= $r ?>"><?= $r ?> Star<?= $r > 1 ? 's' : '' ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Comment</label>
                                        <textarea name="comment" class="form-control" rows="4" maxlength="500" placeholder="Share your experience�" required></textarea>
                                    </div>
                                    <button type="submit" class="btn-submit-review">Submit Review</button>
                                </form>
                            </div>
                            <?php else: ?>
                            <div class="review-write-card text-center">
                                <p style="font-size:.875rem;color:#776965;margin-bottom:1rem">Please log in to write a review.</p>
                                <a href="login.php?next=product.php?id=<?= $id ?>"
                                   style="background:#53362e;border-radius:9px;color:#fff;font-weight:700;font-size:.85rem;padding:.6rem 1.5rem;text-decoration:none;display:inline-block">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Log In
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Review list -->
                        <div class="col-lg-8">
                            <?php if ($reviews): foreach ($reviews as $rev): ?>
                            <div class="review-card">
                                <div class="d-flex align-items-start gap-3">
                                    <img src="<?= avatarUrl($rev['avatar'] ?? null) ?>"
                                         alt="<?= e($rev['user_name']) ?>"
                                         class="review-avatar">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between flex-wrap gap-1 mb-1">
                                            <div>
                                                <strong style="font-size:.88rem;color:#3d2219"><?= e($rev['user_name']) ?></strong>
                                                <div style="color:#d69200;font-size:.9rem"><?= starRating((float)$rev['rating']) ?></div>
                                            </div>
                                            <small style="color:#9b8076;font-size:.75rem"><?= formatDate($rev['created_at']) ?></small>
                                        </div>
                                        <?php if ($rev['comment']): ?>
                                        <p style="font-size:.875rem;color:#776965;line-height:1.65;margin:0"><?= nl2br(e($rev['comment'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-chat-square-text" style="font-size:3rem;color:#ddc9b8;display:block;margin-bottom:1rem"></i>
                                <p style="color:#776965">No reviews yet. Be the first to review!</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if ($related): ?>
        <div class="related-section">
            <h3>You Might Also Like</h3>
            <div class="row g-4">
                <?php foreach ($related as $i => $rp):
                    $rImages = json_decode($rp['images'] ?? '[]', true);
                    $rImg    = $rImages[0] ?? null;
                    $rHasDsc = $rp['sale_price'] && $rp['sale_price'] < $rp['price'];
                ?>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?= $i * 80 ?>">
                    <div class="product-card">
                        <div class="product-card-img-wrap">
                            <a href="product.php?id=<?= $rp['id'] ?>">
                                <img src="<?= productImageUrl($rImg) ?>" class="product-card-img" alt="<?= e($rp['name']) ?>" loading="lazy">
                            </a>
                            <div class="product-card-overlay">
                                <button class="overlay-btn" data-action="add-to-cart" data-product-id="<?= $rp['id'] ?>"><i class="bi bi-bag-plus"></i></button>
                                <a href="product.php?id=<?= $rp['id'] ?>" class="overlay-btn"><i class="bi bi-eye"></i></a>
                            </div>
                        </div>
                        <div class="product-card-body">
                            <span class="product-category-tag"><?= e($rp['category_name']) ?></span>
                            <a href="product.php?id=<?= $rp['id'] ?>" class="text-decoration-none">
                                <h3 class="product-name"><?= e($rp['name']) ?></h3>
                            </a>
                            <div class="product-card-footer">
                                <div>
                                    <?php if ($rHasDsc): ?>
                                    <span class="price-sale fs-6"><?= formatPrice((float)$rp['sale_price']) ?></span>
                                    <?php else: ?>
                                    <span class="price-tag fs-6"><?= formatPrice((float)$rp['price']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <button class="btn btn-primary btn-sm" data-action="add-to-cart" data-product-id="<?= $rp['id'] ?>"><i class="bi bi-bag-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /container -->
</section>

<!-- Image Zoom Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-label="Product image">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="background:#1a1a1a">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-2 pb-4">
                <img id="modalImage" src="<?= productImageUrl($images[0] ?? null) ?>"
                     alt="<?= e($product['name']) ?>"
                     class="img-fluid" style="max-height:80vh;object-fit:contain;border-radius:8px">
            </div>
        </div>
    </div>
</div>

<script>
function setMainImage(src, btn) {
    document.getElementById('mainImage').src = src;
    document.getElementById('modalImage').src = src;
    document.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}
</script>

<?php include 'includes/footer.php'; ?>

