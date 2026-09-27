<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['u'])) {
    exit("Please login first.");
}

$user_id = (int)$_SESSION['u']['user_id'];
$wishlist_id = (int)($_POST['wishlist_id'] ?? 0);

if ($wishlist_id <= 0) {
    exit("Invalid item.");
}

// Ensure the item belongs to the logged-in user before deleting
$check_rs = Database::search("SELECT `wishlist_id` FROM `wishlists` WHERE `wishlist_id` = '$wishlist_id' AND `user_id` = '$user_id'");

if ($check_rs->num_rows == 0) {
    exit("Item not found in your wishlist.");
}

Database::iud("DELETE FROM `wishlists` WHERE `wishlist_id` = '$wishlist_id' AND `user_id` = '$user_id'");

echo "success";
?>