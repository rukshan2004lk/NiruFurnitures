<?php 
session_start();
include "connection.php";

$email    = $_POST["e"] ?? "";
$password = $_POST["p"] ?? "";

if (empty($email)) {
    echo "Please Enter your Email.";
} else if (strlen($email) > 100) {
    echo "Email must contain Less Than 100 Characters.";
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid Email Address.";
} else if (empty($password)) {
    echo "Please Enter your Password.";
} else if (strlen($password) < 5 || strlen($password) > 20) {
    echo "Password must contain between 5 to 20 characters.";
} else {
    // 1. Search for the user by email only
    $rs = Database::search("SELECT * FROM `user` WHERE `email`='" . $email . "'");
    $num = $rs->num_rows;

    if ($num > 0) {
        $user = $rs->fetch_assoc();


        if ($user["status"] !== "active") {
            echo "Your account has been deactivated. Please contact support.";
            exit();
        }

      
        if (password_verify($password, $user["password_hash"])) {
       
            $_SESSION["u"] = $user;
            echo "success";
        } else {
            echo "Invalid Password.";
        }
    } else {
        echo "User with this Email does not exist.";
    }
}
?>