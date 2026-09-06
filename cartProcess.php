<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['u'])) {
    exit("Please login first.");
}

$user_id = (int)$_SESSION['u']['user_id'];
$action  = $_POST['action'] ?? '';
$item_id = (int)($_POST['item_id'] ?? 0);

if ($item_id <= 0) {
    exit("Invalid item.");
}

// Security: Verify item belongs to user's cart
$check_rs = Database::search("SELECT ci.cart_item_id, ci.product_id, i.quantity AS stock 
                             FROM `cart_items` ci 
                             INNER JOIN `carts` c ON ci.cart_id = c.cart_id 
                             LEFT JOIN `inventory` i ON ci.product_id = i.product_id 
                             WHERE ci.cart_item_id = '$item_id' AND c.user_id = '$user_id'");

if ($check_rs->num_rows == 0) {
    exit("Item not found in your cart.");
}

$row = $check_rs->fetch_assoc();
$stock = (int)($row['stock'] ?? 0);

if ($action === "update") {
    $qty = (int)($_POST['qty'] ?? 1);
    if ($qty < 1) {
        Database::iud("DELETE FROM `cart_items` WHERE `cart_item_id` = '$item_id'");
    } else if ($qty > $stock) {
        exit("Only " . $stock . " items in stock.");
    } else {
        Database::iud("UPDATE `cart_items` SET `quantity` = '$qty' WHERE `cart_item_id` = '$item_id'");
    }
    echo "success";
} else if ($action === "remove") {
    Database::iud("DELETE FROM `cart_items` WHERE `cart_item_id` = '$item_id'");
    echo "success";
} else {
    echo "Invalid request.";
}
?>