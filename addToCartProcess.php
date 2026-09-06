<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['u'])) {
    exit("Please login first.");
}

$user_id = (int)$_SESSION['u']['user_id'];
$product_id = (int)($_POST['id'] ?? 0);
$qty = max(1, (int)($_POST['qty'] ?? 1));

if ($product_id <= 0) {
    exit("Invalid product.");
}

$product_rs = Database::search("SELECT p.*, i.quantity AS stock 
                               FROM `products` p 
                               LEFT JOIN `inventory` i ON p.product_id = i.product_id 
                               WHERE p.product_id = '$product_id' AND p.status_id = 1");

if ($product_rs->num_rows == 0) {
    exit("Product not found or unavailable.");
}

$product = $product_rs->fetch_assoc();
$unit_price = (float)$product['price'];
$stock = (int)($product['stock'] ?? 0);

if ($stock < $qty) {
    exit("Only " . $stock . " items available in stock.");
}

$cart_rs = Database::search("SELECT `cart_id` FROM `carts` WHERE `user_id` = '$user_id'");

if ($cart_rs->num_rows > 0) {
    $cart = $cart_rs->fetch_assoc();
    $cart_id = (int)$cart['cart_id'];
} else {
    $session_token = 'sess_' . bin2hex(random_bytes(8));
    Database::iud("INSERT INTO `carts` (`user_id`, `session_token`, `created_at`, `updated_at`) 
                   VALUES ('$user_id', '$session_token', NOW(), NOW())");

    $new_cart_rs = Database::search("SELECT `cart_id` FROM `carts` WHERE `user_id` = '$user_id'");
    $new_cart = $new_cart_rs->fetch_assoc();
    $cart_id = (int)$new_cart['cart_id'];
}

$color = trim($_POST['color'] ?? '');

try {
    Database::iud("ALTER TABLE `cart_items` ADD COLUMN `color` VARCHAR(50) DEFAULT NULL");
} catch (Throwable $e) {}

$item_rs = Database::search("SELECT `cart_item_id`, `quantity` 
                             FROM `cart_items` 
                             WHERE `cart_id` = '$cart_id' AND `product_id` = '$product_id'");

if ($item_rs->num_rows > 0) {
    $item = $item_rs->fetch_assoc();
    $new_qty = $item['quantity'] + $qty;

    if ($new_qty > $stock) {
        exit("Cannot add more. Exceeds total available stock (" . $stock . ").");
    }

    try {
        Database::iud("UPDATE `cart_items` 
                       SET `quantity` = '$new_qty', `unit_price` = '$unit_price', `color` = '" . addslashes($color) . "' 
                       WHERE `cart_item_id` = '" . $item['cart_item_id'] . "'");
    } catch (Throwable $e) {
        Database::iud("UPDATE `cart_items` 
                       SET `quantity` = '$new_qty', `unit_price` = '$unit_price' 
                       WHERE `cart_item_id` = '" . $item['cart_item_id'] . "'");
    }
} else {
    try {
        Database::iud("INSERT INTO `cart_items` (`cart_id`, `product_id`, `quantity`, `unit_price`, `color`) 
                       VALUES ('$cart_id', '$product_id', '$qty', '$unit_price', '" . addslashes($color) . "')");
    } catch (Throwable $e) {
        Database::iud("INSERT INTO `cart_items` (`cart_id`, `product_id`, `quantity`, `unit_price`) 
                       VALUES ('$cart_id', '$product_id', '$qty', '$unit_price')");
    }
}

echo "success";
?>