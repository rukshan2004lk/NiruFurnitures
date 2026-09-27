<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connection.php';

if (!isset($_SESSION['a']) || ((int)($_SESSION['a']['role_id'] ?? 0) !== 1)) {
    exit("Access Denied.");
}

$order_id = (int)($_POST['order_id'] ?? 0);
$status_id = (int)($_POST['status_id'] ?? 0);

// Only allow IDs 6 through 11
$allowed_statuses = [6, 7, 8, 9, 10, 11];

if ($order_id <= 0 || !in_array($status_id, $allowed_statuses, true)) {
    exit("Invalid order or status.");
}

// Verify status exists in database
$check_status = Database::search("SELECT `status_id` FROM `status` WHERE `status_id` = '$status_id'");
if ($check_status->num_rows === 0) {
    exit("Status not found.");
}

Database::iud("UPDATE `orders` SET `order_status_id` = '$status_id' WHERE `order_id` = '$order_id'");

echo "success";
?>