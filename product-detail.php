<?php
require_once "connection.php";

$product_id = (int)($_GET["id"] ?? 0);

if ($product_id <= 0) {
    header("Location: shop.php");
    exit();
}

// 1. Fetch Main Product Details
$product_rs = Database::search("SELECT * FROM `products` WHERE `product_id` = '" . $product_id . "'");

if ($product_rs->num_rows == 0) {
    header("Location: shop.php");
    exit();
}

$product = $product_rs->fetch_assoc();

// 2. Fetch Category Details Separately
$category_rs = Database::search("SELECT * FROM `categories` WHERE `category_id` = '" . $product["category_id"] . "'");
$category_data = $category_rs->fetch_assoc();
$category_name = $category_data["name"] ?? "Collection";

// 3. Fetch All Images for this Product
$images_rs = Database::search("SELECT * FROM `product_images` WHERE `product_id` = '" . $product_id . "' ORDER BY `is_primary` DESC, `sort_order` ASC");

$images = [];
while ($img = $images_rs->fetch_assoc()) {
    $images[] = $img["image_path"];
}

// Fallback image if none uploaded
if (empty($images)) {
    $images[] = "Images/products/nordic_lounge.png";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NiRu Furnitures - <?php echo htmlspecialchars($product["name"]); ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- Navigation Header -->
  <header>
    <nav class="navbar navbar-expand-lg fixed-top px-3 px-lg-5">
      <div class="container-fluid max-w-1320">
        <a class="brand-logo me-4" href="index.html">NiRu</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
          <ul class="navbar-nav mx-auto text-center mb-2 mb-lg-0 gap-lg-4">
            <li class="nav-item"><a class="nav-link-custom" href="index.html">Home</a></li>
            <li class="nav-item"><a class="nav-link-custom active" href="shop.php">Shop</a></li>
            <li class="nav-item"><a class="nav-link-custom" href="about.html">About Us</a></li>
            <li class="nav-item"><a class="nav-link-custom" href="contact.html">Contact</a></li>
            <li class="nav-item"><a class="nav-link-custom" href="faq.html">FAQ</a></li>
          </ul>

          <div class="d-flex align-items-center gap-3">
            <a href="cart.html" class="icon-btn text-decoration-none position-relative" aria-label="Cart">
              <i class="bi bi-bag"></i>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">2</span>
            </a>

            <div class="dropdown">
              <button class="icon-btn dropdown-toggle border-0 bg-transparent p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account">
                <i class="bi bi-person"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3">
                <li><a class="dropdown-item py-2" href="user/dashboard.html"><i class="bi bi-speedometer2 me-2"></i>My Dashboard</a></li>
                <li><a class="dropdown-item py-2" href="user/orders.html"><i class="bi bi-box-seam me-2"></i>My Orders</a></li>
                <li><a class="dropdown-item py-2" href="user/wishlist.html"><i class="bi bi-heart me-2"></i>Wishlist</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item py-2" href="admin/admin-dashboard.html"><i class="bi bi-shield-lock me-2"></i>Admin Panel</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item py-2 text-danger" href="login.html"><i class="bi bi-box-arrow-right me-2"></i>Sign In / Register</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </header>

  <main style="padding-top: 100px;">
    <div class="container-xl py-4">
      
      <!-- Breadcrumbs -->
      <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
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
                     style="cursor: pointer;"
                     onclick="document.getElementById('mainImg').src = this.src; document.querySelectorAll('.gallery-thumbnail').forEach(el => el.classList.remove('active')); this.classList.add('active');">
              <?php } ?>
            </div>
            
            <!-- Main Display Image -->
            <div class="flex-grow-1 order-1 order-sm-2 main-product-image">
              <img id="mainImg" 
                   src="<?php echo htmlspecialchars($images[0]); ?>" 
                   alt="<?php echo htmlspecialchars($product['name']); ?>" 
                   class="img-fluid rounded-4 shadow-sm w-100">
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
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-half"></i>
                  </div>
                  <span class="small text-muted">(48 Reviews)</span>
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
                Available Colors: <span class="fw-normal" style="color: var(--niru-body-text);">Natural Oak</span>
              </div>
              <div class="d-flex gap-3">
                <button class="color-swatch-btn active" style="background-color: #f5f5f0;" aria-label="Oatmeal Cream"></button>
                <button class="color-swatch-btn" style="background-color: #707070;" aria-label="Slate Grey"></button>
                <button class="color-swatch-btn" style="background-color: #4a5d4e;" aria-label="Forest Sage"></button>
                <button class="color-swatch-btn" style="background-color: #8b5e3c;" aria-label="Warm Saddle"></button>
              </div>
            </div>

            <!-- Add To Cart / Purchase Controls -->
            <div class="d-flex flex-wrap align-items-center gap-3">
              <div class="quantity-control d-flex align-items-center border rounded-pill px-3 py-1">
                <button type="button" class="btn btn-sm border-0 p-0 me-2" onclick="adjustQty(-1);">-</button>
                <span id="qtyVal" class="fw-semibold px-2">1</span>
                <button type="button" class="btn btn-sm border-0 p-0 ms-2" onclick="adjustQty(1);">+</button>
              </div>
              <button type="button" onclick="addToCart(<?php echo $product['product_id']; ?>);" class="btn-add-to-cart text-decoration-none text-center flex-grow-1 border-0">
                <i class="bi bi-bag-plus me-1"></i> Add to Cart
              </button>
              <button type="button" onclick="buyNow(<?php echo $product['product_id']; ?>);" class="btn btn-niru-outline text-decoration-none flex-grow-1 text-center">
                Buy Now
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

      <!-- Dynamic Pairs Well With Section -->
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
                                          WHERE `product_id` != '" . $product_id . "' 
                                          ORDER BY RAND() LIMIT 4");

          while ($related = $related_rs->fetch_assoc()) {
              $rel_img_rs = Database::search("SELECT `image_path` FROM `product_images` 
                                              WHERE `product_id` = '" . $related["product_id"] . "' 
                                              ORDER BY `is_primary` DESC, `sort_order` ASC LIMIT 1");
              $rel_img = $rel_img_rs->fetch_assoc();
              $rel_src = $rel_img["image_path"] ?? "Images/products/nordic_lounge.png";
          ?>
            <div class="col-12 col-sm-6 col-md-3">
              <div class="related-card">
                <a href="product-detail.php?id=<?php echo $related['product_id']; ?>" class="text-decoration-none">
                  <div class="related-img-wrapper mb-3">
                    <img src="<?php echo htmlspecialchars($rel_src); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>" class="img-fluid rounded-3">
                  </div>
                  <h3 class="fs-6 fw-normal mb-1 text-truncate" style="color: var(--niru-primary);">
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

  <footer>
    <div class="container-xl">
      <div class="row g-4 mb-5">
        <div class="col-12 col-lg-3">
          <h3 class="fs-4 fw-bold mb-3" style="color: var(--niru-primary);">NiRu</h3>
          <p style="color: var(--niru-body-text);">
            Creating spaces that breathe. We specialize in ethically sourced, artisan-crafted furniture for the modern home.
          </p>
          <div class="d-flex gap-3 fs-5 mt-3" style="color: var(--niru-primary);">
            <a href="#" class="text-decoration-none" style="color: inherit;" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="text-decoration-none" style="color: inherit;" aria-label="Pinterest"><i class="bi bi-pinterest"></i></a>
            <a href="#" class="text-decoration-none" style="color: inherit;" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
          </div>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
          <div class="footer-heading">Shop</div>
          <ul class="footer-links">
            <li><a href="shop.php">All Products</a></li>
            <li><a href="about.html">About Us</a></li>
            <li><a href="faq.html">FAQ</a></li>
          </ul>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
          <div class="footer-heading">Support</div>
          <ul class="footer-links">
            <li><a href="faq.html">Shipping Info</a></li>
            <li><a href="faq.html">Returns & Warranty</a></li>
            <li><a href="contact.html">Contact Us</a></li>
          </ul>
        </div>

        <div class="col-12 col-md-4 col-lg-3">
          <div class="footer-heading">Newsletter</div>
          <p class="small text-muted mb-3">Join our list for early access to new collections and design tips.</p>
          <form class="d-flex border-bottom border-dark pb-1" onsubmit="event.preventDefault();">
            <input type="email" class="form-control border-0 bg-transparent ps-0 shadow-none" placeholder="Email Address">
            <button class="btn p-0 border-0" type="submit" aria-label="Submit Newsletter">
              <i class="bi bi-arrow-right fs-5" style="color: var(--niru-primary);"></i>
            </button>
          </form>
        </div>
      </div>

      <div class="pt-4 border-top text-start" style="border-color: var(--niru-border) !important;">
        <p class="small mb-0" style="color: var(--niru-body-text);">© 2026 NiRu Furnitures. Crafted for Comfort.</p>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/ajax/libs/bootstrap.bundle.min.js"></script>
  <script src="assets/js/script.js"></script>
  <script>
    let currentQty = 1;
    function adjustQty(amount) {
      currentQty += amount;
      if (currentQty < 1) currentQty = 1;
      document.getElementById('qtyVal').textContent = currentQty;
    }

    function buyNow(productId) {
      const qty = document.getElementById('qtyVal') ? parseInt(document.getElementById('qtyVal').textContent) || 1 : 1;
      window.location.href = 'checkout.php?id=' + productId + '&qty=' + qty;
    }
  </script>
</body>
</html>