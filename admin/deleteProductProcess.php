<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connection.php';

// Authentication Guard
if (!isset($_SESSION['a']) || ((int)($_SESSION['a']['role_id'] ?? 0) !== 1)) {
    exit("Access Denied.");
}

$product_id = (int)($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    exit("Invalid Product ID.");
}

// Check product exists
$check_rs = Database::search("SELECT `product_id` FROM `products` WHERE `product_id` = '$product_id'");
if ($check_rs->num_rows === 0) {
    exit("Product not found.");
}

// Mark status as inactive/archived (status_id = 2) or delete if preferred
Database::iud("UPDATE `products` SET `status_id` = 2 WHERE `product_id` = '$product_id'");

echo "success";
?>