<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php';

if (!isset($_SESSION['u'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['u'];
$user_id = (int)$user['user_id'];

$query = "SELECT ci.cart_item_id, ci.product_id, ci.quantity, ci.unit_price, 
                 p.name AS product_name, p.slug, 
                 (SELECT image_path FROM product_images WHERE product_id = p.product_id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS image_path 
          FROM `carts` c 
          INNER JOIN `cart_items` ci ON c.cart_id = ci.cart_id 
          INNER JOIN `products` p ON ci.product_id = p.product_id 
          WHERE c.user_id = '$user_id'";

$cart_rs = Database::search($query);
$subtotal = 0.00;

$page_title = "NiRu Furnitures - Shopping Cart";
include 'header.php';
?>

  <main style="padding-top: 100px; padding-bottom: 80px;">
    <div class="container-xl">
      <h1 class="display-6 fw-bold mb-4" style="color: var(--niru-primary);">Your Shopping Cart</h1>

      <?php if ($cart_rs->num_rows == 0) { ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
          <i class="bi bi-bag-x display-1 text-muted mb-3"></i>
          <h4>Your cart is empty!</h4>
          <p class="text-muted">Looks like you haven't added anything to your cart yet.</p>
          <div class="mt-3">
            <a href="shop.php" class="btn btn-niru-primary px-4 py-2">Start Shopping</a>
          </div>
        </div>
      <?php } else { ?>
        <div class="row g-4">
          <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4">
              <div class="table-responsive">
                <table class="table align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="border-0">Product</th>
                      <th class="border-0 text-center">Price</th>
                      <th class="border-0 text-center">Quantity</th>
                      <th class="border-0 text-end">Total</th>
                      <th class="border-0"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    while ($item = $cart_rs->fetch_assoc()) { 
                      $line_total = (float)$item['unit_price'] * (int)$item['quantity'];
                      $subtotal += $line_total;
                      $img = !empty($item['image_path']) ? $item['image_path'] : "Images/products/nordic_lounge.png";
                    ?>
                      <tr>
                        <td>
                          <div class="d-flex align-items-center gap-3">
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="rounded-3" style="width: 70px; height: 70px; object-fit: cover; background: #f4efeb;">
                            <div>
                              <h6 class="mb-1 fw-bold">
                                <a href="product-detail.php?id=<?php echo $item['product_id']; ?>" class="text-decoration-none text-dark">
                                  <?php echo htmlspecialchars($item['product_name']); ?>
                                </a>
                              </h6>
                            </div>
                          </div>
                        </td>
                        <td class="text-center fw-semibold">Rs. <?php echo number_format($item['unit_price'], 2); ?></td>
                        <td class="text-center">
                          <div class="input-group input-group-sm mx-auto" style="width: 110px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="changeCartQty(<?php echo $item['cart_item_id']; ?>, <?php echo $item['quantity'] - 1; ?>)">-</button>
                            <input type="text" class="form-control text-center" value="<?php echo $item['quantity']; ?>" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="changeCartQty(<?php echo $item['cart_item_id']; ?>, <?php echo $item['quantity'] + 1; ?>)">+</button>
                          </div>
                        </td>
                        <td class="text-end fw-bold" style="color: var(--niru-primary);">Rs. <?php echo number_format($line_total, 2); ?></td>
                        <td class="text-end">
                          <button class="btn btn-link text-danger p-0" title="Remove Item" onclick="removeCartItem(<?php echo $item['cart_item_id']; ?>)">
                            <i class="bi bi-trash fs-5"></i>
                          </button>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

              <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="shop.php" class="btn btn-outline-dark rounded-3 px-4"><i class="bi bi-arrow-left me-2"></i>Continue Shopping</a>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4" style="background-color: var(--niru-bg-alt);">
              <h5 class="fw-bold mb-4" style="color: var(--niru-primary);">Order Summary</h5>

              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal</span>
                <span class="fw-semibold">Rs. <?php echo number_format($subtotal, 2); ?></span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Estimated Shipping</span>
                <span class="text-success fw-semibold">FREE</span>
              </div>
              <hr class="my-3">

              <div class="d-flex justify-content-between mb-4">
                <span class="fs-5 fw-bold" style="color: var(--niru-primary);">Total</span>
                <span class="fs-4 fw-bold" style="color: var(--niru-primary);">Rs. <?php echo number_format($subtotal, 2); ?></span>
              </div>

              <!-- Cart Checkout: direct to checkout.php with product_id=0 -->
              <a href="checkout.php" class="btn btn-niru-primary w-100 py-3 rounded-3 text-center fw-bold fs-6 shadow-sm mb-3">
                Proceed to Checkout <i class="bi bi-arrow-right ms-2"></i>
              </a>
            </div>
          </div>
        </div>
      <?php } ?>

    </div>
  </main>

<?php include 'footer.php'; ?>
</html>