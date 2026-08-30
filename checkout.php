<?php
session_start();
require_once 'connection.php';


// If your login code did this:


// Then in checkout.php, update your check:
if (!isset($_SESSION['u'])) {
  header("Location: login.php");
  exit();
}
$user_data = $_SESSION['u'];
$email = $user_data['email'];
// 2. Validate URL Parameters
$product_id = (int)($_GET["id"] ?? 0);
$qty = (int)($_GET["qty"] ?? 1);
if ($qty < 1) {
  $qty = 1;
}

if ($product_id <= 0) {
  header("Location: shop.php");
  exit();
}

// 3. Fetch Product Details
$product_rs = Database::search("SELECT * FROM `products` WHERE `product_id` = '" . $product_id . "'");

if ($product_rs->num_rows == 0) {
  header("Location: shop.php");
  exit();
}

$product = $product_rs->fetch_assoc();

// 4. Fetch Product Image
$image_rs = Database::search("SELECT * FROM `product_images` WHERE `product_id` = '" . $product_id . "' ORDER BY `is_primary` DESC, `sort_order` ASC LIMIT 1");
$image_data = $image_rs->fetch_assoc();
$image_src = (!empty($image_data["image_path"])) ? $image_data["image_path"] : "Images/products/nordic_lounge.png";

// 5. Price Calculations
$item_price = (float)$product["price"];
$subtotal = $item_price * $qty;
$tax = 0.00;
$total = $subtotal + $tax;

// 6. Fetch User Details Safely
$user_rs = Database::search("SELECT * FROM `user` WHERE `email` = '" . $email . "'");
$user_data = ($user_rs->num_rows > 0) ? $user_rs->fetch_assoc() : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NiRu Furnitures - Checkout</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

  <header>
    <nav class="navbar navbar-expand-lg fixed-top px-3 px-lg-5">
      <div class="container-fluid max-w-1320">
        <a class="brand-logo me-4" href="index.html">NiRu</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
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
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item py-2" href="admin/admin-dashboard.html"><i class="bi bi-shield-lock me-2"></i>Admin Panel</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item py-2 text-danger" href="login.html"><i class="bi bi-box-arrow-right me-2"></i>Sign In / Register</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </header>

  <main style="padding-top: 100px; padding-bottom: 80px;">
    <div class="container-xl">

      <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html" class="text-decoration-none text-muted">Home</a></li>
          <li class="breadcrumb-item"><a href="shop.php" class="text-decoration-none text-muted">Shop</a></li>
          <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
      </nav>

      <h1 class="display-6 fw-bold mb-4" style="color: var(--niru-primary);">Checkout</h1>

      <form onsubmit="event.preventDefault(); window.location.href='user/orders.html';">
        <div class="row g-4">

          <!-- Left Column: Forms -->
          <div class="col-12 col-lg-7">

            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
              <h5 class="fw-bold mb-3" style="color: var(--niru-primary);"><i class="bi bi-person-lines-fill me-2"></i>Contact Information</h5>
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold">Email Address</label>
                  <input id="email" type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user_data['email'] ?? $email); ?>" readonly>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold">Phone Number</label>
                  <input id="mobile type=" tel" class="form-control" name="mobile" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>" placeholder="076 1234567" required>
                </div>
              </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
              <h5 class="fw-bold mb-3" style="color: var(--niru-primary);"><i class="bi bi-geo-alt-fill me-2"></i>Shipping Address</h5>
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold">First Name</label>
                  <input id="fname" type="text" class="form-control" name="fname" value="<?php echo htmlspecialchars($user_data['first_name'] ?? ''); ?>" required>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold">Last Name</label>
                  <input id="lname" type="text" class="form-control" name="lname" value="<?php echo htmlspecialchars($user_data['last_name'] ?? ''); ?>" required>
                </div>
                <div class="col-12">
                  <label class="form-label small fw-semibold">Address Line 1</label>
                  <input
                    id="line1"
                    type="text"
                    class="form-control mb-2"
                    name="line1"
                    placeholder="Street address, P.O. box, company name"
                    value="<?php echo htmlspecialchars($user_data['line_1'] ?? ''); ?>"
                    required>
                </div>

                <div class="col-12">
                  <label class="form-label small fw-semibold">Address Line 2 (Optional)</label>
                  <input
                    id="line2"
                    type="text"
                    class="form-control"
                    name="line2"
                    placeholder="Apartment, suite, unit, building, floor, etc."
                    value="<?php echo htmlspecialchars($user_data['line_2'] ?? ''); ?>">
                </div>
                <div class="col-12 col-md-5">
                  <label class="form-label small fw-semibold">City</label>
                  <input id="city" type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($user_data['city'] ?? ''); ?>" required>
                </div>
                <div class="col-12 col-md-4">
                  <label class="form-label small fw-semibold">Country</label>
                  <select id="country" class="form-select" name="country">
                    <option selected>Sri Lanka</option>
                    <option>United States</option>
                    <option>United Kingdom</option>
                    <option>Sweden</option>
                  </select>
                </div>
                <div class="col-12 col-md-3">
                  <label class="form-label small fw-semibold">Postal Code</label>
                  <input id="pcode" type="text" class="form-control" name="postal_code" value="<?php echo htmlspecialchars($user_data['postal_code'] ?? ''); ?>" required>
                </div>
              </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
              <h5 class="fw-bold mb-3" style="color: var(--niru-primary);"><i class="bi bi-credit-card-fill me-2"></i>Payment Method</h5>

              <div class="form-check p-3 rounded-3 border mb-3">
                <input class="form-check-input" type="radio" name="paymentOption" id="cardPay" checked>
                <label class="form-check-label d-flex justify-content-between align-items-center w-100 fw-semibold ms-2" for="cardPay">
                  <span>Credit or Debit Card</span>
                  <div><i class="bi bi-credit-card-2-front fs-5 me-2"></i><i class="bi bi-stripe fs-5"></i></div>
                </label>
              </div>

              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label small fw-semibold">Card Number</label>
                  <input id="cardNumber" type="text" class="form-control" placeholder="4532 •••• •••• 8892" value="4532 9901 2234 8892" required>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold">Expiration Date</label>
                  <input id="expDate" type="text" class="form-control" placeholder="MM/YY" value="08/28" required>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold">CVV Security Code</label>
                  <input id="cvv" type="password" class="form-control" placeholder="123" value="382" required>
                </div>
              </div>
            </div>

          </div>

          <!-- Right Column: Dynamic Order Summary -->
          <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px; background-color: var(--niru-bg-alt);">
              <h5 class="fw-bold mb-4" style="color: var(--niru-primary);">Review Your Order</h5>

              <!-- Dynamic Item Row -->
              <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                  <img src="<?php echo htmlspecialchars($image_src); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="rounded-3" style="width: 54px; height: 54px; object-fit: cover; background: #fff;">
                  <div>
                    <h6 class="mb-0 fw-bold small"><?php echo htmlspecialchars($product["name"]); ?></h6>
                    <small class="text-muted">Qty: <?php echo $qty; ?> &times; Rs. <?php echo number_format($item_price, 2); ?></small>
                  </div>
                </div>
                <span class="fw-semibold small">Rs. <?php echo number_format($subtotal, 2); ?></span>
              </div>

              <!-- Pricing Breakdown -->
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Subtotal</span>
                <span class="fw-semibold small">Rs. <?php echo number_format($subtotal, 2); ?></span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Shipping (Standard Insured)</span>
                <span class="text-success fw-semibold small">FREE</span>
              </div>
              <div class="d-flex justify-content-between mb-3">
                <span class="text-muted small">Tax</span>
                <span class="fw-semibold small">Rs. <?php echo number_format($tax, 2); ?></span>
              </div>
              <!-- Inside <form> in checkout.php -->
              <input type="hidden" id="productId" value="<?php echo $product_id; ?>">
              <input type="hidden" id="qty" value="<?php echo $qty; ?>">
              <input type="hidden" id="itemPrice" value="<?php echo $item_price; ?>">
              <hr class="my-3">

              <!-- Total -->
              <div class="d-flex justify-content-between mb-4">
                <span class="fs-5 fw-bold" style="color: var(--niru-primary);">Total Due</span>
                <span class="fs-4 fw-bold" style="color: var(--niru-primary);">Rs. <?php echo number_format($total, 2); ?></span>
              </div>

              <button type="submit" class="btn btn-niru-primary w-100 py-3 rounded-3 fw-bold fs-6 shadow-sm mb-3">Place Order & Pay <i class="bi bi-lock-fill ms-2"></i></button>

              <div class="text-center">
                <small class="text-muted"><i class="bi bi-arrow-counterclockwise me-1"></i> 30-Day Money Back Guarantee & Warranty</small>
              </div>
            </div>
          </div>

        </div>
      </form>
    </div>
  </main>

  <footer>
    <div class="container-xl">
      <div class="pt-4 border-top text-center" style="border-color: var(--niru-border) !important;">
        <p class="small mb-0" style="color: var(--niru-body-text);">© 2026 NiRu Furnitures. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/script.js"></script>
</body>

</html>