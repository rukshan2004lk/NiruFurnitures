<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connection.php';

if (!isset($_SESSION['a']) || ((int)($_SESSION['a']['role_id'] ?? 0) !== 1)) {
    exit("Access Denied.");
}

$user_id          = (int)($_SESSION['a']['user_id'] ?? 0);
$current_password = $_POST['current_password'] ?? '';
$new_password     = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($current_password)) {
    exit("Please enter your current password.");
}
if (empty($new_password)) {
    exit("Please enter a new password.");
}
if (strlen($new_password) < 5 || strlen($new_password) > 20) {
    exit("New password must be between 5 and 20 characters.");
}
if ($new_password !== $confirm_password) {
    exit("New passwords do not match.");
}

// Fetch stored hash
$rs = Database::search("SELECT `password_hash` FROM `user` WHERE `user_id` = '$user_id'");
if ($rs->num_rows === 0) {
    exit("Admin account not found.");
}

$user = $rs->fetch_assoc();
$stored_hash = $user['password_hash'] ?? '';

// Verify current password
if (!password_verify($current_password, $stored_hash) && $current_password !== $stored_hash) {
    exit("Incorrect current password.");
}

// Hash and update new password
$new_hash = password_hash($new_password, PASSWORD_BCRYPT);
Database::iud("UPDATE `user` SET `password_hash` = '$new_hash' WHERE `user_id` = '$user_id'");

echo "success";
?>