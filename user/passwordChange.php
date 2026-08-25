<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION["u"])) {
    echo "Please login first.";
    exit();
}

$email = $_SESSION["u"]["email"];

$cpassword = $_POST["c"] ?? "";
$npassword = $_POST["n"] ?? "";

if (empty($cpassword)) {
    echo "Please Enter your Current Password.";
} else if (strlen($cpassword) < 5 || strlen($cpassword) > 20) {
    echo "Current Password must contain between 5 to 20 characters.";
} else if (empty($npassword)) {
    echo "Please Enter New Password.";
} else if (strlen($npassword) < 5 || strlen($npassword) > 20) {
    echo "New Password must contain between 5 to 20 characters.";
} else if ($cpassword === $npassword) {
    echo "New password cannot be the same as your current password.";
} else {
    // 1. Fetch current password hash from database
    $user_rs = Database::search("SELECT `password_hash` FROM `user` WHERE `email` = '" . $email . "'");

    if ($user_rs->num_rows === 1) {
        $user_data = $user_rs->fetch_assoc();

        // 2. Verify current password matches the stored hash
        if (password_verify($cpassword, $user_data["password_hash"])) {
            
            // 3. Hash the new password securely
            $new_hash = password_hash($npassword, PASSWORD_BCRYPT);

            // 4. Update the password in database
            Database::iud("UPDATE `user` SET `password_hash` = '" . $new_hash . "' WHERE `email` = '" . $email . "'");

            echo "success";
        } else {
            echo "Current password is incorrect.";
        }
    } else {
        echo "User not found.";
    }
}
?>