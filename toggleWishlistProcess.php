<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['u'])) {
    exit("login_required");
}

$user_id = (int)$_SESSION['u']['user_id'];
$product_id = (int)($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    exit("Invalid product.");
}

$check_rs = Database::search("SELECT `wishlist_id` FROM `wishlists` 
                             WHERE `user_id` = '$user_id' AND `product_id` = '$product_id'");

if ($check_rs->num_rows > 0) {
    Database::iud("DELETE FROM `wishlists` 
                   WHERE `user_id` = '$user_id' AND `product_id` = '$product_id'");
    echo "removed";
} else {
    Database::iud("INSERT INTO `wishlists` (`user_id`, `product_id`, `created_at`) 
                   VALUES ('$user_id', '$product_id', NOW())");
    echo "added";
}
?>