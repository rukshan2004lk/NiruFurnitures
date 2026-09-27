<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "connection.php";

if (!isset($_SESSION['u'])) {
    exit("login_required");
}

$user_id     = (int)$_SESSION['u']['user_id'];
$product_id  = (int)($_POST['product_id'] ?? 0);
$rating      = (int)($_POST['rating'] ?? 5);
$review_text = trim($_POST['review_text'] ?? '');

if ($product_id <= 0) {
    exit("Invalid product.");
}
if ($rating < 1 || $rating > 5) {
    exit("Please select a rating between 1 and 5 stars.");
}
if (empty($review_text)) {
    exit("Please write a short review before submitting.");
}

// Check if user already reviewed this product; if so, update it, else insert new
$existing_rs = Database::search("SELECT `review_id` FROM `reviews` WHERE `product_id` = '$product_id' AND `user_id` = '$user_id'");

if ($existing_rs->num_rows > 0) {
    $rev = $existing_rs->fetch_assoc();
    $rev_id = $rev['review_id'];
    Database::iud("UPDATE `reviews` SET 
                   `rating` = '$rating', 
                   `review_text` = '" . addslashes($review_text) . "', 
                   `created_at` = NOW() 
                   WHERE `review_id` = '$rev_id'");
    echo "updated";
} else {
    Database::iud("INSERT INTO `reviews` (`product_id`, `user_id`, `rating`, `review_text`, `created_at`) 
                   VALUES ('$product_id', '$user_id', '$rating', '" . addslashes($review_text) . "', NOW())");
    echo "success";
}
?>