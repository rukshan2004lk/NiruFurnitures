<?php
$page_title = "NiRu Furnitures - Products Catalog";
include 'header.php';
require_once 'connection.php';

$categories_rs = Database::search("SELECT * FROM `categories`");
$categories_num = $categories_rs ? $categories_rs->num_rows : 0;
?>

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
                while ($cat = $categories_rs->fetch_assoc()) {
                  $cat_id = $cat["category_id"];
                ?>
                  <a href="#" onclick="selectCategory(<?php echo $cat_id; ?>, this); return false;" id="category<?php echo $cat_id; ?>" class="category-filter-btn inactive">
                    <span><?php echo htmlspecialchars($cat["name"]); ?></span>
                    <i class="bi bi-chevron-right"></i>
                  </a>
                <?php } ?>
              </div>
            </div>

            <!-- Price Range Section -->
            <div>
              <h3 class="fs-4 fw-semibold mb-3" style="color: var(--niru-primary);">Price Range</h3>
              <div class="px-2">
                <input type="range" class="form-range" min="0" max="50000" step="500" value="50000" id="priceRange" 
                       oninput="currentMaxPrice = this.value; document.getElementById('priceDisplay').textContent = 'Rs. ' + parseInt(this.value).toLocaleString();" 
                       onchange="filterProducts();">
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
            <!-- Loaded dynamically via loadProductsProcess.php -->
          </div>

        </section>

      </div>
    </div>
  </main>

<?php include 'footer.php'; ?>