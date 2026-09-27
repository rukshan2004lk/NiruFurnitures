<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connection.php';

// Authentication Guard: Only logged-in Admin can update roles
if (!isset($_SESSION['a']) || ((int)($_SESSION['a']['role_id'] ?? 0) !== 1)) {
    exit("Access Denied.");
}

$user_id   = (int)($_POST['user_id'] ?? 0);
$role_id   = (int)($_POST['role_id'] ?? 0);
$status_id = (int)($_POST['status_id'] ?? 1);

if ($user_id <= 0 || $role_id <= 0) {
    exit("Invalid user or role parameters.");
}

// Check role exists in user_role (1=Admin, 2=Customer, 3=Staff)
$role_check = Database::search("SELECT `role_id` FROM `user_role` WHERE `role_id` = '$role_id'");
if ($role_check->num_rows === 0) {
    exit("Selected role does not exist.");
}

// Prevent admin from demoting or disabling their own account
$current_admin_id = (int)($_SESSION['a']['user_id'] ?? 0);
if ($user_id === $current_admin_id && ($role_id !== 1 || $status_id != 1)) {
    exit("You cannot change or deactivate your own active Admin account.");
}

Database::iud("UPDATE `user` SET `role_id` = '$role_id', `status_id` = '$status_id' WHERE `user_id` = '$user_id'");

echo "success";
?>