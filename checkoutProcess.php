<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['u'])) {
  exit("Please login first.");
}

$user_email = $_SESSION['u']['email'];
$product_id = (int)($_POST['product_id'] ?? 0);
$qty = (int)($_POST['qty'] ?? 1);

if ($product_id > 0) {
  // Case A: Single Product Direct Checkout
  $product_rs = Database::search("SELECT * FROM `products` WHERE `product_id` = '$product_id'");
  $product = $product_rs->fetch_assoc();
  $unit_price = (float)$product['price'];
  $total = $unit_price * $qty;

  // Insert into `orders` and `order_items`
} else {
  // Case B: Entire Cart Checkout
  $cart_rs = Database::search("SELECT * FROM `cart` INNER JOIN `products` ON `cart`.`product_id` = `products`.`product_id` WHERE `user_email` = '$user_email'");
  
  if ($cart_rs->num_rows == 0) {
    exit("Your cart is empty.");
  }

  // Calculate totals from database & insert multiple rows into `order_items`
  // Clear cart table for user upon success: DELETE FROM `cart` WHERE `user_email` = '$user_email'
}

echo "success";
?>