<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "connection.php";

$product_id = (int)($_GET["id"] ?? 0);

if ($product_id <= 0) {
    header("Location: shop.php");
    exit();
}

$product_rs = Database::search("SELECT * FROM `products` WHERE `product_id` = '" . $product_id . "' AND `status_id` = 1");

if ($product_rs->num_rows == 0) {
    header("Location: shop.php");
    exit();
}

$product = $product_rs->fetch_assoc();

$category_rs = Database::search("SELECT * FROM `categories` WHERE `category_id` = '" . $product["category_id"] . "'");
$category_data = $category_rs->fetch_assoc();
$category_name = $category_data["name"] ?? "Collection";

$images_rs = Database::search("SELECT * FROM `product_images` WHERE `product_id` = '" . $product_id . "' ORDER BY `is_primary` DESC, `sort_order` ASC");

$images = [];
while ($img = $images_rs->fetch_assoc()) {
    $images[] = $img["image_path"];
}

if (empty($images)) {
    $images[] = "Images/products/nordic_lounge.png";
}

$review_stats_rs = Database::search("SELECT AVG(`rating`) AS `avg_rating`, COUNT(`review_id`) AS `total_reviews` 
                                     FROM `reviews` 
                                     WHERE `product_id` = '$product_id'");
$review_stats = $review_stats_rs->fetch_assoc();
$avg_rating = !empty($review_stats['avg_rating']) ? round((float)$review_stats['avg_rating'], 1) : 5.0;
$total_reviews = (int)($review_stats['total_reviews'] ?? 0);

$reviews_list_rs = Database::search("SELECT r.*, u.first_name, u.last_name 
                                    FROM `reviews` r 
                                    LEFT JOIN `user` u ON r.user_id = u.user_id 
                                    WHERE r.product_id = '$product_id' 
                                    ORDER BY r.created_at DESC");

$is_wishlisted = false;
$cart_count = 0;

if (isset($_SESSION['u'])) {
    $user_id = (int)$_SESSION['u']['user_id'];
    
    // Check Wishlist
    $wish_check = Database::search("SELECT `wishlist_id` FROM `wishlists` WHERE `user_id` = '$user_id' AND `product_id` = '$product_id'");
    if ($wish_check->num_rows > 0) {
        $is_wishlisted = true;
    }

    // Fetch Cart Quantity for Navbar Badge
    $cart_badge_rs = Database::search("SELECT SUM(ci.quantity) AS `qty` 
                                      FROM `cart_items` ci 
                                      INNER JOIN `carts` c ON ci.cart_id = c.cart_id 
                                      WHERE c.user_id = '$user_id'");
    $cart_count = (int)($cart_badge_rs->fetch_assoc()['qty'] ?? 0);
}
?>
<?php
$page_title = "NiRu Furnitures - " . htmlspecialchars($product["name"]);
include 'header.php';
?>

  <main style="padding-top: 100px;">
    <div class="container-xl py-4">
      
      <!-- Breadcrumbs -->
      <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="shop.php">Products</a></li>
          <li class="breadcrumb-item"><a href="shop.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($category_name); ?></a></li>
          <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
      </nav>

      <div class="row g-5 mb-5 align-items-start">
        
        <!-- Product Images Gallery -->
        <div class="col-12 col-lg-7">
          <div class="d-flex flex-column flex-sm-row gap-3">
            
            <!-- Thumbnails List -->
            <div class="d-flex flex-sm-column gap-3 overflow-auto order-2 order-sm-1">
              <?php foreach ($images as $index => $imgPath) { ?>
                <img src="<?php echo htmlspecialchars($imgPath); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?> Thumbnail" 
                     class="gallery-thumbnail <?php echo ($index === 0) ? 'active' : ''; ?>"
                     style="cursor: pointer; width: 70px; height: 70px; object-fit: cover; border-radius: 8px;"
                     onclick="document.getElementById('mainImg').src = this.src; document.querySelectorAll('.gallery-thumbnail').forEach(el => el.classList.remove('active')); this.classList.add('active');">
              <?php } ?>
            </div>
            
            <!-- Main Display Image -->
            <div class="flex-grow-1 order-1 order-sm-2 main-product-image">
              <img id="mainImg" 
                   src="<?php echo htmlspecialchars($images[0]); ?>" 
                   alt="<?php echo htmlspecialchars($product['name']); ?>" 
                   class="img-fluid rounded-4 shadow-sm w-100"
                   style="max-height: 520px; object-fit: cover;">
            </div>
          </div>
        </div>

        <!-- Product Summary & Actions -->
        <div class="col-12 col-lg-5">
          <div class="d-flex flex-column gap-4">
            
            <div>
              <span class="badge-collection mb-2">NEW COLLECTION</span>
              <h1 class="display-5 fw-bold mb-2" style="color: var(--niru-primary); line-height: 1.1;">
                <?php echo htmlspecialchars($product["name"]); ?>
              </h1>
              
              <div class="d-flex align-items-center gap-3 mt-3">
                <span class="fs-3 fw-semibold" style="color: var(--niru-primary);">
                  Rs. <?php echo number_format($product["price"], 2); ?>
                </span>
                <div class="d-flex align-items-center gap-2">
                  <div class="star-rating text-warning">
                    <?php 
                    for ($s = 1; $s <= 5; $s++) {
                        if ($s <= floor($avg_rating)) {
                            echo '<i class="bi bi-star-fill"></i> ';
                        } elseif (($s - $avg_rating) < 1) {
                            echo '<i class="bi bi-star-half"></i> ';
                        } else {
                            echo '<i class="bi bi-star"></i> ';
                        }
                    }
                    ?>
                  </div>
                  <span class="small text-muted">(<?php echo $total_reviews; ?> <?php echo ($total_reviews === 1) ? 'Review' : 'Reviews'; ?>)</span>
                </div>
              </div>
            </div>

            <p class="fs-6 text-muted mb-0" style="line-height: 1.6;">
              <?php echo nl2br(htmlspecialchars($product["description"] ?? 'Handcrafted timeless furniture designed for the modern sanctuary.')); ?>
            </p>

            <div class="material-info-box d-flex justify-content-between gap-3">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-tree fs-5" style="color: var(--niru-primary);"></i>
                <div>
                  <div class="fw-semibold small" style="color: var(--niru-primary); letter-spacing: 0.7px;">Sustainability</div>
                  <div class="small text-muted">FSC Certified Oak</div>
                </div>
              </div>

              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-shield-check fs-5" style="color: var(--niru-primary);"></i>
                <div>
                  <div class="fw-semibold small" style="color: var(--niru-primary); letter-spacing: 0.7px;">Warranty</div>
                  <div class="small text-muted">10-Year Frame</div>
                </div>
              </div>
            </div>

            <div>
              <div class="fw-semibold mb-2" style="font-size: 14px; letter-spacing: 0.7px; color: var(--niru-primary);">
                Available Colors: <span id="selectedColorName" class="fw-bold" style="color: var(--niru-primary);">Oatmeal Cream</span>
              </div>
              <div class="d-flex gap-3 align-items-center">
                <button type="button" class="color-swatch-btn active" style="background-color: #f5f5f0; border: 1px solid #ccc;" data-color-name="Oatmeal Cream" title="Oatmeal Cream" onclick="selectProductColor(this);"></button>
                <button type="button" class="color-swatch-btn" style="background-color: #707070;" data-color-name="Slate Grey" title="Slate Grey" onclick="selectProductColor(this);"></button>
                <button type="button" class="color-swatch-btn" style="background-color: #4a5d4e;" data-color-name="Forest Sage" title="Forest Sage" onclick="selectProductColor(this);"></button>
                <button type="button" class="color-swatch-btn" style="background-color: #8b5e3c;" data-color-name="Warm Saddle" title="Warm Saddle" onclick="selectProductColor(this);"></button>
                <button type="button" class="color-swatch-btn" style="background-color: #1b1c1c;" data-color-name="Obsidian Black" title="Obsidian Black" onclick="selectProductColor(this);"></button>
              </div>
            </div>

            <!-- Add To Cart / Purchase Controls / Wishlist Toggle -->
            <div class="d-flex flex-wrap align-items-center gap-2">
              <div class="quantity-control d-flex align-items-center border rounded-pill px-3 py-1">
                <button type="button" class="btn btn-sm border-0 p-0 me-2" onclick="adjustQty(-1);">-</button>
                <span id="qtyVal" class="fw-semibold px-2">1</span>
                <button type="button" class="btn btn-sm border-0 p-0 ms-2" onclick="adjustQty(1);">+</button>
              </div>

              <!-- Add to Cart -->
              <button type="button" onclick="addToCart(<?php echo $product['product_id']; ?>);" class="btn-add-to-cart text-decoration-none text-center flex-grow-1 border-0">
                <i class="bi bi-bag-plus me-1"></i> Add to Cart
              </button>

              <!-- Buy Now -->
              <button type="button" onclick="buyNow(<?php echo $product['product_id']; ?>);" class="btn btn-niru-outline text-decoration-none flex-grow-1 text-center">
                Buy Now
              </button>

              <!-- Wishlist Heart Button -->
              <button type="button" 
                      class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" 
                      style="width: 48px; height: 48px;" 
                      onclick="toggleWishlist(<?php echo $product['product_id']; ?>, this);" 
                      title="Save to Wishlist"
                      aria-label="Wishlist">
                <i class="bi <?php echo $is_wishlisted ? 'bi-heart-fill text-danger' : 'bi-heart'; ?> fs-5"></i>
              </button>
            </div>

            <div class="delivery-box">
              <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-truck text-dark"></i>
                <span class="fw-semibold small text-dark" style="letter-spacing: 0.7px;">Free Standard Shipping</span>
              </div>
              <p class="small text-muted mb-0 ps-4">Expected delivery: 5-7 business days. Fully assembled upon arrival.</p>
            </div>

          </div>
        </div>

      </div>

      <!-- Product Customer Reviews Section -->
      <section class="py-5 border-top" style="border-color: var(--niru-border) !important;">
        <div class="row g-5">
          
          <!-- Left: Existing Reviews List -->
          <div class="col-12 col-lg-7">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <h3 class="fs-4 fw-semibold mb-0" style="color: var(--niru-primary);">
                Customer Reviews (<?php echo $total_reviews; ?>)
              </h3>
              <div class="d-flex align-items-center gap-2">
                <span class="fs-5 fw-bold text-dark"><?php echo number_format($avg_rating, 1); ?></span>
                <div class="text-warning small">
                  <i class="bi bi-star-fill"></i>
                </div>
              </div>
            </div>

            <?php if ($reviews_list_rs->num_rows > 0) { ?>
              <div class="d-flex flex-column gap-3">
                <?php while ($rev = $reviews_list_rs->fetch_assoc()) { 
                    $reviewer_name = htmlspecialchars(trim(($rev['first_name'] ?? 'Verified') . ' ' . ($rev['last_name'] ?? 'Buyer')));
                    $rev_rating = (int)$rev['rating'];
                    $rev_date = date("M d, Y", strtotime($rev['created_at']));
                ?>
                  <div class="p-4 rounded-4 border bg-white shadow-sm">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; background-color: #f0eded; color: var(--niru-primary);">
                          <?php echo strtoupper(substr($reviewer_name, 0, 1)); ?>
                        </div>
                        <div>
                          <strong class="d-block text-dark small"><?php echo $reviewer_name; ?></strong>
                          <span class="text-muted" style="font-size: 11px;"><?php echo $rev_date; ?></span>
                        </div>
                      </div>

                      <div class="text-warning small">
                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                          <i class="bi <?php echo ($i <= $rev_rating) ? 'bi-star-fill' : 'bi-star'; ?>"></i>
                        <?php } ?>
                      </div>
                    </div>
                    <p class="text-muted mb-0 small" style="line-height: 1.6;">
                      <?php echo nl2br(htmlspecialchars($rev['review_text'])); ?>
                    </p>
                  </div>
                <?php } ?>
              </div>
            <?php } else { ?>
              <div class="p-4 text-center border rounded-4 bg-light text-muted">
                <i class="bi bi-chat-square-quote fs-3 d-block mb-2"></i>
                No reviews yet for this product. Be the first to share your thoughts!
              </div>
            <?php } ?>
          </div>

          <!-- Right: Submit Review Form -->
          <div class="col-12 col-lg-5">
            <div class="card p-4 rounded-4 border shadow-sm bg-white">
              <h4 class="fs-5 fw-bold mb-2" style="color: var(--niru-primary);">Write a Review</h4>
              <p class="small text-muted mb-3">Share your experience with this furniture piece.</p>

              <?php if (isset($_SESSION['u'])) { ?>
                <form id="productReviewForm" onsubmit="event.preventDefault(); submitProductReview();">
                  <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

                  <div class="mb-3">
                    <label class="form-label small fw-semibold">Overall Rating</label>
                    <select name="rating" class="form-select" required>
                      <option value="5" selected>★★★★★ (5 - Outstanding)</option>
                      <option value="4">★★★★☆ (4 - Good Quality)</option>
                      <option value="3">★★★☆☆ (3 - Average)</option>
                      <option value="2">★★☆☆☆ (2 - Below Expectations)</option>
                      <option value="1">★☆☆☆☆ (1 - Poor)</option>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small fw-semibold">Your Feedback</label>
                    <textarea name="review_text" rows="4" class="form-control" placeholder="What did you like or dislike about the materials, assembly, or comfort?" required></textarea>
                  </div>

                  <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold" style="background-color: var(--niru-primary); border-radius: 8px;">
                    Post Review
                  </button>
                </form>
              <?php } else { ?>
                <div class="text-center py-4">
                  <p class="small text-muted mb-3">You must be logged in to review this product.</p>
                  <a href="login.php" class="btn btn-outline-dark btn-sm px-4 py-2 rounded-pill">
                    Sign In to Review
                  </a>
                </div>
              <?php } ?>
            </div>
          </div>

        </div>
      </section>

      <!-- Pairs Well With Section -->
      <section class="pt-5 border-top" style="border-color: var(--niru-border) !important;">
        <div class="d-flex align-items-end justify-content-between mb-4">
          <div>
            <h2 class="fs-2 fw-semibold mb-1" style="color: var(--niru-primary);">Pairs Well With</h2>
            <p class="text-muted mb-0">Complete your minimalist sanctuary with these curated additions.</p>
          </div>
          <a href="shop.php" class="text-decoration-none fw-normal" style="color: var(--niru-primary);">
            View Entire Collection <i class="bi bi-arrow-right"></i>
          </a>
        </div>

        <div class="row g-4">
          <?php
          $related_rs = Database::search("SELECT * FROM `products` 
                                          WHERE `product_id` != '" . $product_id . "' AND `status_id` = 1 
                                          ORDER BY RAND() LIMIT 4");

          while ($related = $related_rs->fetch_assoc()) {
              $rel_id = (int)$related["product_id"];
              $rel_img_rs = Database::search("SELECT `image_path` FROM `product_images` 
                                              WHERE `product_id` = '$rel_id' 
                                              ORDER BY `is_primary` DESC, `sort_order` ASC LIMIT 1");
              $rel_img = $rel_img_rs->fetch_assoc();
              $rel_src = $rel_img["image_path"] ?? "Images/products/nordic_lounge.png";
          ?>
            <div class="col-12 col-sm-6 col-md-3">
              <div class="related-card d-flex flex-column h-100">
                <a href="product-detail.php?id=<?php echo $rel_id; ?>" class="text-decoration-none d-block">
                  <div class="related-img-wrapper mb-3 rounded-4 overflow-hidden" style="height: 220px; background-color: #f5f4f0;">
                    <img src="<?php echo htmlspecialchars($rel_src); ?>" 
                         alt="<?php echo htmlspecialchars($related['name']); ?>" 
                         class="w-100 h-100" 
                         style="object-fit: cover; object-position: center; display: block;">
                  </div>
                  <h3 class="fs-6 fw-semibold mb-1 text-truncate" style="color: var(--niru-primary);">
                    <?php echo htmlspecialchars($related["name"]); ?>
                  </h3>
                  <p class="fs-6 text-muted mb-0">Rs. <?php echo number_format($related["price"], 2); ?></p>
                </a>
              </div>
            </div>
          <?php } ?>
        </div>
      </section>

    </div>
  </main>

  <script>
    let currentQty = 1;
    let selectedColor = "Oatmeal Cream";

    function adjustQty(amount) {
      currentQty += amount;
      if (currentQty < 1) currentQty = 1;
      document.getElementById('qtyVal').textContent = currentQty;
    }

    function selectProductColor(btnElement) {
      document.querySelectorAll('.color-swatch-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      btnElement.classList.add('active');
      selectedColor = btnElement.getAttribute('data-color-name') || "Oatmeal Cream";

      const label = document.getElementById('selectedColorName');
      if (label) {
        label.textContent = selectedColor;
      }
    }

    function buyNow(productId) {
      const qty = document.getElementById('qtyVal') ? parseInt(document.getElementById('qtyVal').textContent) || 1 : 1;
      window.location.href = 'checkout.php?id=' + productId + '&qty=' + qty + '&color=' + encodeURIComponent(selectedColor);
    }
  </script>
<?php include 'footer.php'; ?>