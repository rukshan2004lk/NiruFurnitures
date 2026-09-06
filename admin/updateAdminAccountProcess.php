<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connection.php';

if (!isset($_SESSION['a']) || ((int)($_SESSION['a']['role_id'] ?? 0) !== 1)) {
    exit("Access Denied.");
}

$user_id    = (int)($_SESSION['a']['user_id'] ?? 0);
$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$phone      = trim($_POST['phone'] ?? '');

if (empty($first_name)) {
    exit("First Name cannot be empty.");
}

// Update admin details in the user table
Database::iud("UPDATE `user` SET 
               `first_name` = '" . addslashes($first_name) . "', 
               `last_name` = '" . addslashes($last_name) . "', 
               `phone` = '" . addslashes($phone) . "' 
               WHERE `user_id` = '$user_id'");

// Update active session data
$_SESSION['a']['first_name'] = $first_name;
$_SESSION['a']['last_name']  = $last_name;
$_SESSION['a']['phone']      = $phone;

if (isset($_SESSION['u'])) {
    $_SESSION['u']['first_name'] = $first_name;
    $_SESSION['u']['last_name']  = $last_name;
    $_SESSION['u']['phone']      = $phone;
}

echo "success";
?>