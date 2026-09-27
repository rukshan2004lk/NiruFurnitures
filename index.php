<?php
$page_title = "NiRu Furnitures - Stylish Furniture For Modern Living";
include 'header.php';
?>

    <main style="padding-top: 80px">
      
      <section class="hero-section">
        <div class="container-xl">
          <div
            class="hero-banner mb-5 rounded-4 p-4 p-md-5 d-flex align-items-center justify-content-center text-center position-relative overflow-hidden"
            style="
              background: url(&quot;Images/background.png&quot;) center/cover
                no-repeat;
              min-height: 520px;
            "
          >
            
            <div
              class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-25"
            ></div>

           <div
  class="hero-overlay-card position-relative z-1 p-4 p-md-5 rounded-4 mx-auto"
  style="
    max-width: 680px;
    background: rgba(255, 255, 255, 0.45);
    backdrop-filter: blur(16px) saturate(180%);
    -webkit-backdrop-filter: blur(16px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
  "
>
              <h1
                class="display-4 fw-bold mb-3"
                style="color: var(--niru-primary); line-height: 1.15"
              >
                Stylish Furniture<br />For Modern Living
              </h1>
              <p
                class="fs-5 mb-4"
                style="color: var(--niru-body-text); line-height: 1.6"
              >
                Curating comfort through artisanal craftsmanship and sustainable
                materials. Discover a sanctuary designed for your home.
              </p>
              <div class="d-flex flex-wrap gap-3 justify-content-center">
                <a href="shop.php" class="btn btn-niru-primary btn-lg px-4"
                  >Shop Now</a
                >
                <a href="shop.php" class="btn btn-niru-outline btn-lg px-4"
                  >Explore Categories</a
                >
              </div>
            </div>
          </div>

          <div class="mb-4">
            <h2
              class="fs-2 fw-semibold mb-2"
              style="color: var(--niru-primary)"
            >
              Shop by Category
            </h2>
            <div class="category-title-bar"></div>
          </div>

          <div class="row g-4">
            
            <div class="col-12 col-lg-6">
              <div class="bento-card bento-card-large">
                <img src="Images/Category/sofa.png" alt="Sofas" />
                <div class="bento-overlay">
                  <h3 class="fs-3 fw-semibold text-white mb-2">Sofas</h3>
                  <a
                    href="shop.php"
                    class="text-white text-decoration-underline fw-semibold small"
                    style="letter-spacing: 0.7px"
                    >Browse Collection</a
                  >
                </div>
              </div>
            </div>

            <div class="col-12 col-lg-6">
              <div class="row g-4">
                
                <div class="col-12 col-sm-6">
                  <a href="shop.php" class="text-decoration-none">
                    <div class="bento-card">
                      <img
                        src="Images/Category/chair.png"
                        alt="Chairs"
                      />
                      <div class="bento-overlay">
                        <h3 class="fs-4 fw-semibold text-white mb-0">Chairs</h3>
                      </div>
                    </div>
                  </a>
                </div>

                <div class="col-12 col-sm-6">
                  <a href="shop.php" class="text-decoration-none">
                    <div class="bento-card">
                      <img
                        src="Images/Category/table.png"
                        alt="Tables"
                      />
                      <div class="bento-overlay">
                        <h3 class="fs-4 fw-semibold text-white mb-0">Tables</h3>
                      </div>
                    </div>
                  </a>
                </div>

                <div class="col-12 col-sm-6">
                  <a href="shop.php" class="text-decoration-none">
                    <div class="bento-card">
                      <img
                        src="Images/Category/bed.png"
                        alt="Beds"
                      />
                      <div class="bento-overlay">
                        <h3 class="fs-4 fw-semibold text-white mb-0">Beds</h3>
                      </div>
                    </div>
                  </a>
                </div>

                <div class="col-12 col-sm-6">
                  <a href="shop.php" class="text-decoration-none">
                    <div class="bento-card">
                      <img
                        src="Images/Category/office.png"
                        alt="Office"
                      />
                      <div class="bento-overlay">
                        <h3 class="fs-4 fw-semibold text-white mb-0">Office</h3>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="py-5" style="background-color: var(--niru-bg-alt)">
        <div class="container-xl py-4">
          <div class="text-center mb-5 max-w-576 mx-auto">
            <h2
              class="fs-2 fw-semibold mb-3"
              style="color: var(--niru-primary)"
            >
              Featured Arrivals
            </h2>
            <p
              class="text-muted"
              style="color: var(--niru-body-text); font-size: 16px"
            >
              Selected pieces that represent our philosophy of durability,
              comfort, and timeless aesthetics.
            </p>
          </div>

          <div class="row g-4">
            
            <div class="col-12 col-sm-6 col-lg-3">
              <div class="product-card">
                <div class="product-img-wrapper">
                  <a href="product-detail.php">
                    <img
                      src="Images/featured/nordic_lounge.png"
                      alt="Nordic Lounge Chair"
                    />
                  </a>
                  <button class="wishlist-btn" aria-label="Add to Wishlist">
                    <i class="bi bi-heart"></i>
                  </button>
                </div>
                <div class="p-4">
                  <div class="category-badge mb-1">SEATING</div>
                  <h3
                    class="fs-5 fw-normal mb-2"
                    style="color: var(--niru-primary)"
                  >
                    <a href="product-detail.php" class="text-decoration-none" style="color: inherit;">Nordic Lounge Chair</a>
                  </h3>
                  <p
                    class="fs-6 mb-0 fw-normal"
                    style="color: var(--niru-body-text)"
                  >
                    Rs. 849.00
                  </p>
                </div>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
              <div class="product-card">
                <div class="product-img-wrapper">
                  <a href="product-detail.php">
                    <img
                      src="Images/featured/sulptural_coffee_table.png"
                      alt="Sculptural Coffee Table"
                    />
                  </a>
                  <button class="wishlist-btn" aria-label="Add to Wishlist">
                    <i class="bi bi-heart"></i>
                  </button>
                </div>
                <div class="p-4">
                  <div class="category-badge mb-1">LIVING ROOM</div>
                  <h3
                    class="fs-5 fw-normal mb-2"
                    style="color: var(--niru-primary)"
                  >
                    <a href="product-detail.php" class="text-decoration-none" style="color: inherit;">Sculptural Coffee Table</a>
                  </h3>
                  <p
                    class="fs-6 mb-0 fw-normal"
                    style="color: var(--niru-body-text)"
                  >
                    Rs. 1,299.00
                  </p>
                </div>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
              <div class="product-card">
                <div class="product-img-wrapper">
                  <a href="product-detail.php">
                    <img
                      src="Images/featured/Linear_Oak.png"
                      alt="Linear Oak Bookshelf"
                    />
                  </a>
                  <button class="wishlist-btn" aria-label="Add to Wishlist">
                    <i class="bi bi-heart"></i>
                  </button>
                </div>
                <div class="p-4">
                  <div class="category-badge mb-1">STORAGE</div>
                  <h3
                    class="fs-5 fw-normal mb-2"
                    style="color: var(--niru-primary)"
                  >
                    <a href="product-detail.php" class="text-decoration-none" style="color: inherit;">Linear Oak Bookshelf</a>
                  </h3>
                  <p
                    class="fs-6 mb-0 fw-normal"
                    style="color: var(--niru-body-text)"
                  >
                    Rs. 620.00
                  </p>
                </div>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
              <div class="product-card">
                <div class="product-img-wrapper">
                  <a href="product-detail.php">
                    <img
                      src="Images/featured/Brass_desk.png"
                      alt="Brass Desk Luminary"
                    />
                  </a>
                  <button class="wishlist-btn" aria-label="Add to Wishlist">
                    <i class="bi bi-heart"></i>
                  </button>
                </div>
                <div class="p-4">
                  <div class="category-badge mb-1">LIGHTING</div>
                  <h3
                    class="fs-5 fw-normal mb-2"
                    style="color: var(--niru-primary)"
                  >
                    <a href="product-detail.php" class="text-decoration-none" style="color: inherit;">Brass Desk Luminary</a>
                  </h3>
                  <p
                    class="fs-6 mb-0 fw-normal"
                    style="color: var(--niru-body-text)"
                  >
                    Rs. 310.00
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="py-5 bg-white">
        <div class="container-xl py-4">
          
          <div
            class="d-flex align-items-center justify-content-between mb-4 mb-md-5"
          >
            <div>
              <span
                class="text-uppercase fw-semibold tracking-wider small text-muted d-block mb-1"
                >Testimonials</span
              >
              <h2 class="fs-2 fw-bold mb-0" style="color: var(--niru-primary)">
                Voices of Comfort
              </h2>
            </div>
            <div class="d-flex gap-2">
              <button
                class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center p-0"
                style="width: 42px; height: 42px"
                aria-label="Previous"
              >
                <i class="bi bi-arrow-left fs-5"></i>
              </button>
              <button
                class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center p-0"
                style="width: 42px; height: 42px"
                aria-label="Next"
              >
                <i class="bi bi-arrow-right fs-5"></i>
              </button>
            </div>
          </div>

          <div class="row g-4 align-items-stretch">
            
            <div class="col-12 col-md-4">
              <div
                class="testimonial-card card h-100 border-0 bg-light p-4 rounded-4 d-flex flex-column justify-content-between shadow-sm"
              >
                <div>
                  <div class="d-flex text-warning gap-1 mb-3">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                  </div>
                  <p
                    class="fst-italic fs-6 mb-4"
                    style="color: var(--niru-primary); line-height: 1.7"
                  >
                    "The Nordic Lounge Chair is the centerpiece of my studio.
                    The quality of the wood and the comfort of the cushions
                    exceeded every expectation. It's functional art."
                  </p>
                </div>
                <div
                  class="d-flex align-items-center gap-3 pt-3 border-top border-2 border-white"
                >
                  <div
                    class="avatar-circle rounded-circle bg-white fw-bold d-flex align-items-center justify-content-center shadow-sm"
                    style="
                      width: 48px;
                      height: 48px;
                      min-width: 48px;
                      color: var(--niru-primary);
                    "
                  >
                    AM
                  </div>
                  <div>
                    <h4
                      class="fs-6 fw-bold mb-0"
                      style="color: var(--niru-primary)"
                    >
                      Adrian Miller
                    </h4>
                    <small class="text-muted">Architect, Oslo</small>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div
                class="testimonial-card card h-100 border-0 bg-light p-4 rounded-4 d-flex flex-column justify-content-between shadow-sm"
              >
                <div>
                  <div class="d-flex text-warning gap-1 mb-3">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                  </div>
                  <p
                    class="fst-italic fs-6 mb-4"
                    style="color: var(--niru-primary); line-height: 1.7"
                  >
                    "NiRu has mastered the balance of minimalism and warmth. My
                    living room feels so much more grounded since the modular
                    sofa arrived. Delivery was seamless too."
                  </p>
                </div>
                <div
                  class="d-flex align-items-center gap-3 pt-3 border-top border-2 border-white"
                >
                  <div
                    class="avatar-circle rounded-circle bg-white fw-bold d-flex align-items-center justify-content-center shadow-sm"
                    style="
                      width: 48px;
                      height: 48px;
                      min-width: 48px;
                      color: var(--niru-primary);
                    "
                  >
                    SW
                  </div>
                  <div>
                    <h4
                      class="fs-6 fw-bold mb-0"
                      style="color: var(--niru-primary)"
                    >
                      Sarah White
                    </h4>
                    <small class="text-muted">Interior Designer, London</small>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div
                class="testimonial-card card h-100 border-0 bg-light p-4 rounded-4 d-flex flex-column justify-content-between shadow-sm"
              >
                <div>
                  <div class="d-flex text-warning gap-1 mb-3">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                  </div>
                  <p
                    class="fst-italic fs-6 mb-4"
                    style="color: var(--niru-primary); line-height: 1.7"
                  >
                    "Beautiful craftsmanship. You can tell these pieces are made
                    to last generations. The sustainable approach NiRu takes
                    made the decision easy."
                  </p>
                </div>
                <div
                  class="d-flex align-items-center gap-3 pt-3 border-top border-2 border-white"
                >
                  <div
                    class="avatar-circle rounded-circle bg-white fw-bold d-flex align-items-center justify-content-center shadow-sm"
                    style="
                      width: 48px;
                      height: 48px;
                      min-width: 48px;
                      color: var(--niru-primary);
                    "
                  >
                    EL
                  </div>
                  <div>
                    <h4
                      class="fs-6 fw-bold mb-0"
                      style="color: var(--niru-primary)"
                    >
                      Elena Lindqvist
                    </h4>
                    <small class="text-muted"
                      >Creative Director, Colombo</small
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

<?php include 'footer.php'; ?>
