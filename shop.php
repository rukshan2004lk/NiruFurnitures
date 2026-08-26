<?php
require_once 'connection.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NiRu Furnitures - Products Catalog</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

  <?php
  $categories_rs = Database::search("SELECT * FROM `categories`");
  $categories_num = $categories_rs->num_rows;
  ?>

  <header>
    <nav class="navbar navbar-expand-lg fixed-top px-3 px-lg-5">
      <div class="container-fluid max-w-1320">
        <a class="brand-logo me-4" href="index.html">NiRu</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
          <ul class="navbar-nav mx-auto text-center mb-2 mb-lg-0 gap-lg-4">
            <li class="nav-item">
              <a class="nav-link-custom" href="index.html">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link-custom active" href="shop.php">Shop</a>
            </li>
            <li class="nav-item">
              <a class="nav-link-custom" href="about.html">About Us</a>
            </li>
            <li class="nav-item">
              <a class="nav-link-custom" href="contact.html">Contact</a>
            </li>
            <li class="nav-item">
              <a class="nav-link-custom" href="faq.html">FAQ</a>
            </li>
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
      <div class="row g-4">

        <!-- Sidebar Filters -->
        <aside class="col-12 col-lg-3">
          <div class="d-flex flex-column gap-4">

            <!-- Category Section -->
            <div>
              <h3 class="fs-4 fw-semibold mb-3" style="color: var(--niru-primary);">Categories</h3>
              <div class="d-flex flex-column gap-2">
                <a href="#" onclick="selectCategory(0, this); return false;" class="category-filter-btn active">
                  <span>All Collections</span>
                  <i class="bi bi-chevron-right"></i>
                </a>

                <?php
                for ($x = 0; $x < $categories_num; $x++) {
                  $categories_data = $categories_rs->fetch_assoc();
                  $categories_id = $categories_data["category_id"];
                ?>
                  <a href="#" onclick="selectCategory(<?php echo $categories_id; ?>, this); return false;" id="category<?php echo $categories_id; ?>" class="category-filter-btn inactive">
                    <span><?php echo htmlspecialchars($categories_data["name"]); ?></span>
                    <i class="bi bi-chevron-right"></i>
                  </a>
                <?php } ?>
              </div>
            </div>

            <!-- Price Range Section -->
            <div>
              <h3 class="fs-4 fw-semibold mb-3" style="color: var(--niru-primary);">Price Range</h3>
              <div class="px-2">
                <input type="range" class="form-range" min="0" max="50000" step="500" value="50000" id="priceRange" oninput="updatePrice(this.value);" onchange="filterProducts();">
                <div class="d-flex justify-content-between mt-2 fw-semibold small" style="color: var(--niru-body-text);">
                  <span>Rs. 0</span>
                  <span id="priceDisplay">Rs. 50,000</span>
                </div>
              </div>
            </div>

           
            <div class="eco-badge-card">
              <i class="bi bi-leaf eco-icon mb-2 d-block"></i>
              <h4 class="mb-1">Eco-Choice</h4>
              <p>Sustainable materials sourced for longevity and comfort.</p>
            </div>

          </div>
        </aside>

        <!-- Product Listing Section -->
        <section class="col-12 col-lg-9">

          <!-- Search Bar -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="d-flex align-items-center gap-2">
                <input
                  id="searchTxt"
                  class="form-control rounded-pill px-3 py-2 shadow-sm"
                  type="text"
                  placeholder="Search furniture..."
                  onkeydown="if(event.key === 'Enter'){ searchProducts(); }"
                >
                <button
                  type="button"
                  onclick="searchProducts();"
                  class="btn btn-niru-sm d-flex align-items-center justify-content-center px-4 py-2"
                >
                  <i class="bi bi-search"></i>
                </button>
              </div>
            </div>
          </div>

     
          <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
              <h1 class="fs-2 fw-semibold mb-1" style="color: var(--niru-primary);">Our Products</h1>
              <p class="mb-0 text-muted" style="color: var(--niru-body-text); font-size: 16px;">Discover handcrafted comfort for your home.</p>
            </div>

           <div class="sort-pills-container d-inline-flex flex-nowrap align-items-center p-1">
  <button type="button" class="sort-pill active" onclick="selectSort('newest', this);">Newest</button>
  <button type="button" class="sort-pill" onclick="selectSort('popular', this);">Popular</button>
  <button type="button" class="sort-pill" onclick="selectSort('sales', this);">Sales</button>
  <button type="button" class="sort-pill" onclick="selectSort('price_low', this);">Price: Low</button>
  <button type="button" class="sort-pill" onclick="selectSort('price_high', this);">Price: High</button>
</div>
          </div>

          <!-- Dynamic Product Grid Container -->
          <div class="row g-4 mb-5" id="productContainer">
            <!-- Loaded dynamically via AJAX -->
          </div>

          <!-- Pagination -->
          <div class="d-flex align-items-center justify-content-center gap-2">
            <a href="#" class="pagination-btn active">1</a>
            <a href="#" class="pagination-btn">2</a>
            <a href="#" class="pagination-btn">3</a>
            <a href="#" class="pagination-btn" aria-label="Next Page">
              <i class="bi bi-chevron-right"></i>
            </a>
          </div>

        </section>

      </div>
    </div>
  </main>

  <footer>
    <div class="container-xl">
      <div class="row g-4 mb-5">
        <div class="col-12 col-lg-3">
          <h3 class="fs-4 fw-bold mb-3" style="color: var(--niru-primary);">NiRu</h3>
          <p style="color: var(--niru-body-text);">
            Crafting timeless furniture pieces that blend artisanal craftsmanship with modern utility.
          </p>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
          <div class="footer-heading">EXPLORE</div>
          <ul class="footer-links">
            <li><a href="about.html">About Us</a></li>
            <li><a href="shop.php">Products</a></li>
            <li><a href="faq.html">FAQ</a></li>
          </ul>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
          <div class="footer-heading">SUPPORT</div>
          <ul class="footer-links">
            <li><a href="faq.html">Shipping Info</a></li>
            <li><a href="faq.html">Returns & Warranty</a></li>
            <li><a href="contact.html">Contact Us</a></li>
          </ul>
        </div>

        <div class="col-12 col-md-4 col-lg-3">
          <div class="footer-heading">JOIN OUR JOURNAL</div>
          <form class="d-flex gap-2" onsubmit="event.preventDefault();">
            <input type="email" class="form-control newsletter-input" placeholder="Email Address">
            <button class="btn btn-niru-sm" type="submit">Join</button>
          </form>
        </div>
      </div>

      <div class="pt-4 border-top text-center" style="border-color: rgba(212,195,190,0.3) !important;">
        <p class="mb-0" style="color: var(--niru-body-text);">© 2026 NiRu Furnitures. Crafted for Comfort.</p>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/ajax/libs/bootstrap.bundle.min.js"></script>
  <script src="assets/js/script.js"></script>
</body>

</html>