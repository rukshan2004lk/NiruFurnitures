<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['u'])) {
    header("Location: login.php");
    exit();
}

$user_session = $_SESSION['u'];
$user_id      = (int)$user_session['user_id'];
$email        = $user_session['email'];

// Read optional single-product parameters
$product_id = (int)($_GET["id"] ?? 0);
$qty        = max(1, (int)($_GET["qty"] ?? 1));
$color      = htmlspecialchars(trim($_GET["color"] ?? 'Oatmeal Cream'));

$checkout_items = [];
$subtotal = 0.00;

if ($product_id > 0) {
    // Mode A: Direct Buy Now
    $product_rs = Database::search("SELECT * FROM `products` WHERE `product_id` = '$product_id' AND `status_id` = 1");
    if ($product_rs->num_rows == 0) {
        header("Location: shop.php");
        exit();
    }
    $product = $product_rs->fetch_assoc();

    $img_rs = Database::search("SELECT `image_path` FROM `product_images` WHERE `product_id` = '$product_id' ORDER BY `is_primary` DESC, `sort_order` ASC LIMIT 1");
    $img_data = $img_rs->fetch_assoc();
    $img_src = (!empty($img_data["image_path"])) ? $img_data["image_path"] : "Images/products/nordic_lounge.png";

    $price = (float)$product["price"];
    $line = $price * $qty;
    $subtotal += $line;

    $checkout_items[] = [
        'name'       => $product['name'],
        'price'      => $price,
        'qty'        => $qty,
        'color'      => $color,
        'line_total' => $line,
        'image'      => $img_src
    ];
} else {
    // Mode B: Entire Cart Checkout
    $cart_query = "SELECT ci.quantity, ci.unit_price, ci.color, p.name, 
                          (SELECT image_path FROM product_images WHERE product_id = p.product_id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS image_path 
                   FROM `carts` c 
                   INNER JOIN `cart_items` ci ON c.cart_id = ci.cart_id 
                   INNER JOIN `products` p ON ci.product_id = p.product_id 
                   WHERE c.user_id = '$user_id'";
    $cart_rs = Database::search($cart_query);

    if ($cart_rs->num_rows == 0) {
        header("Location: cart.php");
        exit();
    }

    while ($c_item = $cart_rs->fetch_assoc()) {
        $price = (float)$c_item['unit_price'];
        $item_qty = (int)$c_item['quantity'];
        $line = $price * $item_qty;
        $subtotal += $line;

        $checkout_items[] = [
            'name'       => $c_item['name'],
            'price'      => $price,
            'qty'        => $item_qty,
            'color'      => $c_item['color'] ?? '',
            'line_total' => $line,
            'image'      => !empty($c_item['image_path']) ? $c_item['image_path'] : "Images/products/nordic_lounge.png"
        ];
    }
}

$tax = 0.00;
$total = $subtotal + $tax;

// Fetch fresh user profile details
$user_rs = Database::search("SELECT * FROM `user` WHERE `user_id` = '$user_id'");
$user_data = ($user_rs->num_rows > 0) ? $user_rs->fetch_assoc() : $user_session;
?>
<?php
$page_title = "NiRu Furnitures - Checkout";
include 'header.php';
?>

  <main style="padding-top: 100px; padding-bottom: 80px;">
    <div class="container-xl">

      <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
          <li class="breadcrumb-item"><a href="shop.php" class="text-decoration-none text-muted">Shop</a></li>
          <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
      </nav>

      <h1 class="display-6 fw-bold mb-4" style="color: var(--niru-primary);">Checkout</h1>

      <form onsubmit="event.preventDefault();">
        <div class="row g-4">

          <!-- Left Column: Delivery and Payment Forms -->
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
                  <input id="mobile" type="tel" class="form-control" name="mobile" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>" placeholder="071 2345678" required>
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
                  <input id="line1" type="text" class="form-control mb-2" name="line1" placeholder="Street address, house number" value="<?php echo htmlspecialchars($user_data['address_line1'] ?? ''); ?>" required>
                </div>
                <div class="col-12">
                  <label class="form-label small fw-semibold">Address Line 2 (Optional)</label>
                  <input id="line2" type="text" class="form-control" name="line2" placeholder="Apartment, suite, unit, floor" value="<?php echo htmlspecialchars($user_data['address_line2'] ?? ''); ?>">
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
                  <input id="cardNumber" type="text" class="form-control" placeholder="4242 •••• •••• 4242" value="4242 4242 4242 4242" required>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold">Expiration Date</label>
                  <input id="expDate" type="text" class="form-control" placeholder="MM/YY" value="12/28" required>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold">CVV Security Code</label>
                  <input id="cvv" type="password" class="form-control" placeholder="123" value="123" maxlength="4" required>
                </div>
              </div>
            </div>

          </div>

          <!-- Right Column: Dynamic Order Summary -->
          <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px; background-color: var(--niru-bg-alt);">
              <h5 class="fw-bold mb-4" style="color: var(--niru-primary);">Review Your Order</h5>

              <!-- Dynamic Order Item Loop -->
              <div class="order-items-list mb-3" style="max-height: 280px; overflow-y: auto;">
                <?php foreach ($checkout_items as $item) { ?>
                  <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                      <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover; background: #fff;">
                      <div>
                        <h6 class="mb-0 fw-bold small"><?php echo htmlspecialchars($item["name"]); ?></h6>
                        <?php if (!empty($item['color'])) { ?>
                          <div style="font-size: 11px;"><span class="badge bg-secondary-subtle text-dark border px-2 py-1 my-1">Color: <?php echo htmlspecialchars($item['color']); ?></span></div>
                        <?php } ?>
                        <small class="text-muted">Qty: <?php echo $item['qty']; ?> &times; Rs. <?php echo number_format($item['price'], 2); ?></small>
                      </div>
                    </div>
                    <span class="fw-semibold small">Rs. <?php echo number_format($item['line_total'], 2); ?></span>
                  </div>
                <?php } ?>
              </div>

              <!-- Pricing Calculation -->
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

              <!-- State elements for placeOrder() -->
              <input type="hidden" id="productId" value="<?php echo $product_id; ?>">
              <input type="hidden" id="qty" value="<?php echo $qty; ?>">
              <input type="hidden" id="color" value="<?php echo htmlspecialchars($color); ?>">
              <hr class="my-3">

              <div class="d-flex justify-content-between mb-4">
                <span class="fs-5 fw-bold" style="color: var(--niru-primary);">Total Due</span>
                <span class="fs-4 fw-bold" style="color: var(--niru-primary);">Rs. <?php echo number_format($total, 2); ?></span>
              </div>

              <button type="button" onclick="placeOrder();" class="btn btn-niru-primary w-100 py-3 rounded-3 fw-bold fs-6 shadow-sm mb-3">
                Place Order & Pay <i class="bi bi-lock-fill ms-2"></i>
              </button>

              <div class="text-center">
                <small class="text-muted"><i class="bi bi-arrow-counterclockwise me-1"></i> 30-Day Money Back Guarantee & Warranty</small>
              </div>
            </div>
          </div>

        </div>
      </form>
    </div>
  </main>

<?php include 'footer.php'; ?>

</html>